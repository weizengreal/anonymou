<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/4/20
 * Time: 下午6:02
 */
namespace app\index\logic;

use app\index\model\User;
use app\index\model\Teacher;
use app\index\model\Conf;
use app\index\model\Score;
use app\index\model\Comment;

class Userhandle {

    private $user;
    private $teacher;
    private $conf;
    private $score;
    private $comment;

    public function __construct() {
        $this->user = new User();
        $this->teacher = new Teacher();
        $this->conf = new Conf();
        $this->score = new Score();
        $this->comment = new Comment();
    }

    // 完全匿名，只保留openid字段用于唯一标记用户
    public function addNewUser($scopeData) {
        $accessToken = \app\index\service\Basic::setStu($scopeData['openid'],password_hash($scopeData['openid'],PASSWORD_BCRYPT));
        if(!$this->user->isExist(array('openid'=>$scopeData['openid']))) {
            $data = array(
//                'unionid'=>$scopeData['unionid'],
                'openid'=>$scopeData['openid'],
                'headimgurl'=>getHeadImg(),
                'nickname'=>getName(),
                'addtime'=>time(),
                'accesstoken'=>$accessToken,
                'overtime'=>time()+3600*6*24
            );
            return $this->user->addNew($data);
        }
        else {
            //需更新 accessToken
            \app\index\service\Basic::setStu($scopeData['openid']);
            return true;
        }
    }

    /*
     * 该函数输入一个postData和mediaid参数，可以满足两个需求：
     * 第一  可以实现新增教师
     * 第二  可以实现修改教师
     *
     * 这里第一次出现配置项 check，定义value值得意义：
     * 1：新增教师需要审核
     * 2：新增教师不需要审核
     *
     * */
    public function newTeacher($mediaid,$postData) {
        $res = $this->conf->getInfoByArr(array(
            'mediaid'=>$mediaid,
            'name'=>'check'
        ));
        $lessonsArr = explode("|",$postData['lessonInput']);
        unset($lessonsArr[count($lessonsArr)-1]);
        $data=array(
            'mediaid'=>$mediaid,
            'name'=>$postData['nameInput'],
            'detail'=>$postData['detailInput'],
            'headimg'=>$postData['teaimg'],
            'lessons'=>json_encode($lessonsArr,JSON_UNESCAPED_UNICODE),
            'subtime'=>time(),
        );
        if($res['value'] == "2") {
            //表示该公众号不需要审核
            $data['show']=1;// 直接显示
        }
        return [
            'errcode'=>$this->teacher->addNew($data) ? 1 : -1,
            'ischeck'=>$res['value']
        ];
    }

    /*
     * 获取教师信息
     * */
    public function getData($page,$mediaid) {
        $start = ($page-1)*12;
        $res = $this->teacher->getTeaData($start,$mediaid);
        $result = array();
        foreach ($res as $item) {
            $item['tid']=authcode($item['id'],"ENCODE");
            unset($item['id']);
            $result[]=$item;
        }
        return $result;
    }

    /*
     * 搜索逻辑：
     * 搜索关键词优先级：
     * 教师名
     * 专业
     * 学院
     * */
    public function getSearchData($page,$searchWords,$mediaid) {
        $start = ($page-1)*12;
        if ($mediaid == 'gh_d7a0ac02f3ef') {
            $res = $this->teacher->getSeaData_gh_d7a0ac02f3ef($start,$searchWords,$mediaid);
        }else {
            $res = $this->teacher->getSeaData($start,$searchWords,$mediaid);
        }
        $result = array();
        foreach ($res as $item) {
            $item['tid']=authcode($item['id'],"ENCODE");
            unset($item['id']);
            $result[]=$item;
        }
        return $result;
    }

    /*
     * 添加一条新的打分
     * */
    public function addScore($postData) {
        //根据accessToken获取该用户的openid
//        dump($_COOKIE);
        $accessToken = \app\index\service\Basic::getStatus();
        if(empty($accessToken)) {
            return [
                'status'=>-1,
                'info'=>'cookie outTime！'
            ];
        }
        else {
            if($postData['quality'] > 100 || $postData['responsible'] > 100 || $postData['pass'] > 100) {
                return [
                    'status'=>-4,
                    'info'=>'please input right number'
                ];
            }
            $tid = authcode($postData['tid'],"DECODE");
            if(empty($tid)) {
                return ['status'=>-4,"info"=>"error params!"];
            }
            $result = $this->user->getInfoByArr(array(
                'accesstoken'=>$accessToken
            ));
            $data = array(
                'openid'=>$result['openid'],
                'tid'=>$tid,
                'quality'=>$postData['quality'],
                'responsible'=>$postData['responsible'],
                'pass'=>$postData['pass'],
                'addtime'=>time()
            );
            //添加一条打分记录
            if(!$this->score->addNew($data)) {
                return [
                    'status'=>-2,
                    'info'=>'inner error！111'
                ];
            }
            /*
             * 更新该教师的分数
             * 1、获得基本教师分数
             * 2、调用教师分数计算方法
             * 3、更新教师分数
             * */
            $teaScore = $this->teacher->getInfoByArr(array('id'=>$tid));
            if(!isset($_COOKIE['mediaid'])) {
                return [
                    'status'=>-3,
                    'info'=>'lose params mediaid！'
                ];
            }
            $upData = $this->mathJsScore($_COOKIE['mediaid'],$teaScore['quality'],$teaScore['responsible'],$teaScore['pass'],$postData['quality'],$postData['responsible'],$postData['pass']);
            if(!$this->teacher->updata($tid,$upData)) {
                return [
                    'status'=>-2,
                    'info'=>'inner error！222'
                ];
            }
            return [
                'status'=>1,
                'info'=>'ok'
            ]+$upData;
        }
    }

    /*
     * 添加一个评论
     * */
    public function addComment($postData) {
        $accessToken = \app\index\service\Basic::getStatus();
        if(!empty($accessToken)) {
            $tid = authcode($postData['tid'],"DECODE");
            if(empty($tid)) {
                return ['status'=>-4,"info"=>"error params!"];
            }
            $info = $this->user->getInfoByArr(array('accesstoken'=>$accessToken));
            if($this->comment->addNew(array(
                'openid'=>$info['openid'],
                'tid'=>$tid,
                'cretime'=>time(),
                'content'=>$postData['comment'],
            ))) {
                // 应该将该教师的评论条数+1
                $this->teacher->addComCount(['id'=>$tid]);
                return ['status'=>1,"info"=>"ok","headimgurl"=>$info['headimgurl'],'name'=>$info['nickname']];
            }
            else {
                return ['status'=>-3,'info'=>""];
            }
        }
        else {
            return ['status'=>-1,'info'=>"cookie outtime!"];
        }
    }

    /*
     * 获取评论基本数据 getPageData
     * */
    public function getComData($postData) {
        $tid = authcode($postData['tid'],"DECODE");
        if(empty($tid)) {
            return null;
        }
        $start = ($postData['page']-1)*12;
        $res = $this->comment->getPageData($tid,$start);
        foreach ($res as $index => $item) {
            $res[$index]['cretime']=getTime($res[$index]['cretime']);
        }
        return $res;
    }


    /*
     * TODO::算法简单粗暴，等待更好的算法
     * 教师分数计算方法(该函数不进行逻辑判断，只进行数理运算)
     * 根据该公众号数据库配置项 mathlevel，根据 mathlevel计算三个数据的值
     * mathlevel说明如下：
     * mathlevel有10个级别，分表表示当前公众号设置下，该用户打分取得的权值，值为1-10，
     * 该用户的取值为 [数据源分数]orignScore*((100-mathlevel)/100)+[学生打分]nowScore*(mathlevel/100)
     * */
    public function mathJsScore($mediaid,$quality,$responsible,$pass,$inputQua,$inputRes,$inputPass) {
        $confArr = $this->conf->getInfoByArr(array(
            'mediaid'=>$mediaid,
            'name'=>"mathlevel"
        ));
        $mathLevel = !empty($confArr['mathlevel']) ? (int)$confArr['mathlevel'] : 6;
        $quality=$quality*((100-$mathLevel)/100)+$inputQua*($mathLevel/100);
        $responsible=$responsible*((100-$mathLevel)/100)+$inputRes*($mathLevel/100);
        $pass=$pass*((100-$mathLevel)/100)+$inputPass*($mathLevel/100);
        //保留两位小数
        return array(
            'quality'=>sprintf("%.2f", $quality),
            'responsible'=>sprintf("%.2f", $responsible),
            'pass'=>sprintf("%.2f", $pass)
        );
    }




}

