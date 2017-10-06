<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/5/20
 * Time: 下午1:27
 */


namespace app\index\controller;
use app\index\model\Comment;
use think\Controller;
use think\Db;
use weixiao\WxAccess;
use weixiao\WxInfo;
use think\Request;
use app\index\model\Media;
use app\index\model\Conf;

class Admin extends Controller {

    /*
     * 应用全局接口
     * 处理开启应用、应用触发
     *
     * 开启应用：需要将公众号的状态信息初始化
     * 应用触发：需要导向到指定的模块下
     * 应用配置页：需要先行鉴定公众号是否通过权限认证，导向配置页
     * */
    public function index() {
        $weixiao = new WxAccess();
        $type = Request::instance()->get("type");
        switch ($type) {
            case "open": {
                $res = $weixiao->open();
                if($res['errcode'] == 0) {
                    $mediaid = $res['media_id'];
                    unset($res['media_id']);
                    // 初始化该公众号基本信息
                    if($this->initGzh($mediaid,empty($gzhInfo['school_name']) ? "" : $gzhInfo['school_name'])) {
                        return json_encode($res);
                    }
                    else {
                        return json_encode([
                            'errcode' => 4001,
                            'errmsg' => '应用初始化失败，请重试，若出现多次请留言应用'
                        ]);
                    }
                }
                else {
                    return json_encode($res);
                }
                break;
            }
            case "trigger" : {
                $this->redirect('http://notice.woai662.net/anonymou/?media_id='.$_GET['media_id']);
                break;
            }
            case "monitor": {
                $weixiao->monitor();
                break;
            }
            case 'config': {
                // 这里需要对公众号进行初始化学校的操作
                $res = $weixiao->config();
                if($res['errcode'] == "0") {
                    $wxInfo = new WxInfo();
                    $gzhInfo = json_decode($wxInfo->getInfo($res['media_id']),true);
                    if(! empty($gzhInfo['school_name'])) {
                        $this->initGzh($res['media_id'],$gzhInfo['school_name']);
                    }
                    return $this->conf($res['media_id']);
                }
                else{
                    $this->redirect(url("index/message/msg_error",array('title' => '权限超时' , 'content'=>"请刷新页面重新打开"),false,true));
                }
                break;
            }
            default : {
                return "你好";
            }
        }
    }

    /*
     * 应用配置页
     * */
    public function conf($media_id) {
        $confArr = Db::table('anony_conf')->where(['mediaid'=>$media_id])->order('name desc')->select();
        $confArr = $this->formatConf($confArr);
        $comment = new Comment();
        return $this->fetch("conf",[
            'mediaId'=>$media_id,
            'setPass'=>url("index/datahandle/setPass","",false),
            'setUnPass'=>url("index/datahandle/setUnPass","",false),
            'getData'=>url("index/datahandle/getDataForConf","",false),
            'searchTea'=>url("index/datahandle/searchTea","",false),
            'changeTeaStu'=>url("index/datahandle/changeTeaStu","",false),
            'upload'=>url("index/datahandle/upload","",false),
            'check'=>$confArr['check'],
            'upImg'=>empty($confArr['allowUpImg']) ? 2 : $confArr['allowUpImg'],
            'comFilter'=>empty($confArr['comFilter']) ? 1 : $confArr['comFilter'],
            'end'=>ceil((Db::table('anony_teacher')->where(['mediaid'=>$media_id])->count())/15),
            'comEnd'=>ceil(($comment->comGetCount($media_id)) / 15),
            'searchCom'=>url("index/datahandle/searchComment","",false),
            'deleteCom'=>url("index/datahandle/hideComment","",false),
        ]);
    }

    /*
     * 应用配置页测试界面
     * */
    public function test_conf() {
        die('please call to admin:weizeng');
        $media_id='gh_367c5510c3ee';
        setcookie('mediaid',$media_id,time()+1000000,'/');
        $confArr = Db::table('anony_conf')->where(['mediaid'=>$media_id])->order('name desc')->select();
        $confArr = $this->formatConf($confArr);
        $comment = new Comment();
        return $this->fetch("test_conf",[
            'mediaId'=>$media_id,
            'setPass'=>url("index/datahandle/setPass","",false),
            'setUnPass'=>url("index/datahandle/setUnPass","",false),
            'getData'=>url("index/datahandle/getDataForConf","",false),
            'searchTea'=>url("index/datahandle/searchTea","",false),
            'changeTeaStu'=>url("index/datahandle/changeTeaStu","",false),
            'upload'=>url("index/datahandle/upload","",false),
            'check'=>$confArr['check'],
            'upImg'=>empty($confArr['allowUpImg']) ? 2 : $confArr['allowUpImg'],
            'comFilter'=>empty($confArr['comFilter']) ? 1 : $confArr['comFilter'],
            'end'=>ceil((Db::table('anony_teacher')->where(['mediaid'=>$media_id])->count())/15),
            'comEnd'=>ceil(($comment->comGetCount($media_id)) / 15),
            'searchCom'=>url("index/datahandle/searchComment","",false),
            'deleteCom'=>url("index/datahandle/hideComment","",false),
        ]);
    }

    /*
     * 格式化DB中配置内容到二维数组
     * */
    private function formatConf($confArr) {
        $result = [];
        foreach ($confArr as $item) {
            $result[$item['name']] = $item['value'];
        }
        return $result;
    }

    /*
     * 初始化公众号的基本信息
     * 开启应用时将自动调用此函数，如有更新需求重新开启即可
     * */
    private function initGzh($mediaid,$name) {
        $media = new Media();
        $conf = new Conf();
        $mediaArr = [
            'schoolname'=>$name,
        ];
        if(!$media->isExist(['mediaid'=>$mediaid])) {
            // 添加
            $mediaArr+=[
                'mediaid'=>$mediaid,
                'apikey'=>config("API_KEY"),
            ];
            $boolean = $media->addNew($mediaArr) !== false;
        }
        else {
            // 更新
            $boolean = $media->updata($mediaid,$mediaArr) !== false;
        }
        if(!$conf->isExist(['mediaid'=>$mediaid])) {
            $dataSource = array(
                [
                    'mediaid'=>$mediaid,
                    'name'=>'check',
                    'value'=>1,
                ],
                [
                    'mediaid'=>$mediaid,
                    'name'=>'allowUpImg',
                    'value'=>2,
                ]
            );
            $boolean = $conf->addMore($dataSource) !== false;
        }
        return $boolean;
    }

}


