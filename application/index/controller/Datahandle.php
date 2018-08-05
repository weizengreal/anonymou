<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/4/20
 * Time: 下午7:49
 * 该类库用户获取数据集合
 */
namespace app\index\controller;

use app\index\logic\Userhandle;
use app\index\model\Comment;
use think\Controller;
use think\Request;
use think\Db;
use app\index\model\Media;
use app\index\model\Conf;
use app\index\logic\AdminHandle;

class Datahandle extends Controller
{

    private $userLogic;

    public function __construct(Request $request = null) {
        $this->userLogic = new Userhandle();
        parent::__construct($request);
    }

    // add一个教师数据
    public function addTeacher() {
        if(!empty(\app\index\service\Basic::getStatus()) && isset($_COOKIE['mediaid'])) {
            $mediaid = $_COOKIE['mediaid'];
        }
        else{
            return ['status' => -3, 'info' => "lose params!", 'url' => url("index/message/msg_error", array('content' => "cookie超时，请使用微信端重新打开链接！"))];
        }
        $postData = Request::instance()->post();
        $ru = [
            'nameInput' => 'require',
            'detailInput' => 'require',
            'lessonInput' => 'require',
            'teaimg' => 'require',
        ];
        $res = $this->validate($postData, $ru);
        if ($res === true) {
            $retArr = $this->userLogic->newTeacher($mediaid, $postData);
            if ($retArr['errcode'] == 1) {
                return [
                    'status' => 1,
                    'info' => $retArr['ischeck'] == "1" ? "恭喜您，您的新增需求已经提交审核！" : "恭喜您，新增教师信息成功！",
                    'ischeck' => $retArr['ischeck'],
                    'url' => $retArr['ischeck'] == "1" ? url("Index/message/msg_success", array('content' => "恭喜您，您的新增需求已经提交审核，请等待管理员的审核！", 'redirectUri' => urlencode(url("Index/index/index", "", false)), false)) : url("Index/index/index", "", false)
                ];
            } else {
                return ['status' => -4, 'info' => "inner error!"];
            }
        } else {
            return ['status' => -1, 'info' => $res];
        }
    }


    // 实现分页，默认一页12个，接受post数据---page 页数
    public function getData() {
        if (isset($_POST['page'])  && isset($_COOKIE['mediaid'])) {
            $mediaid = $_COOKIE['mediaid'];
            $page = Request::instance()->post("page", 1);
            return [
                'status' => 1,
                'data' => $this->userLogic->getData($page,$mediaid),
                'info' => "ok"
            ];
        } else {
            return ['status' => -2, 'info' => 'lose params!'];
        }
    }

    public function searchData() {
        if (isset($_POST['page']) && isset($_POST['searchWords']) && isset($_COOKIE['mediaid'])) {
            $mediaid = $_COOKIE['mediaid'];
            $page = Request::instance()->post("page", 1);
            $searchWords = addslashes(Request::instance()->post("searchWords"));
            return [
                'status' => 1,
                'data' => $this->userLogic->getSearchData($page, $searchWords,$mediaid),
                'info' => "ok"
            ];
        } else {
            return ['status' => -2, 'info' => 'lose params!'];
        }
    }


    /*
     * 设置该用户的打分
     * */
    public function setScore() {
        $postData = Request::instance()->post();
        $ru = [
            'quality' => 'require',
            'responsible' => 'require',
            'pass' => 'require',
            'tid' => 'require',
        ];
        $res = $this->validate($postData, $ru);
        if ($res === true) {
            return $this->userLogic->addScore($postData);
        }
        else {
            return ['status' => -2, 'info' => $res.'lose params!'];
        }
    }

    /*
     * 添加一个新的评论
     * 某用户对某个教师进行了评论
     * */
    public function comment() {
        $postData = Request::instance()->post();
        $ru = [
            'comment' => 'require',
            'tid' => 'require',
        ];
        $res = $this->validate($postData, $ru);
        if ($res === true) {
            return $this->userLogic->addComment($postData);
        }
        else {
            return ['status' => -2, 'info' => $res.'lose params!'];
        }
    }


    /*
     * 获取评论数据
     * */
    public function getComData() {
        $postData = Request::instance()->post();
        $ru = [
            'page' => 'require',
            'tid' => 'require',
        ];
        $res = $this->validate($postData, $ru);
        if ($res === true) {
            return [
                'status' => 1,
                'data' => $this->userLogic->getComData($postData),
                'info' => "ok"
            ];
        }
        else {
            return ['status' => -2, 'info' => 'lose params!'];
        }
    }

    // ====================== 分割线，下面为config页面的函数接口 ======================

    /*
     * 将excel文件上传到指定的地方
     * 上传文件的大小限制为10MB，只能上传xls文件
     * */
    public function upload() {
        if(empty($_COOKIE['mediaid'])) {
            return [
                'stauts'=>-2,
                'info'=>'权限超时，请刷新界面'
            ];
        }
        $file = request()->file('excel');
        $mediaid = $_COOKIE['mediaid'];
        $info = $file->validate(['size'=>10485760,'ext'=>'xls'])->move(ROOT_PATH . 'public' . DS . 'excel',$mediaid.rand(1000,123456798).'.xls');
        if($info){
            // 成功上传后 获取上传信息
            $filename = $info->getSaveName();
            $result = AdminHandle::excelToMysql($mediaid,$filename);
            return $result+['info'=>$result['status'] == 1 ? 'ok' : '内部错误!'];
        }else{
            // 上传失败获取错误信息
            return [
                'status'=>-3,
                'info'=>$file->getError()
            ];
        }
    }

    /*
     * 设置教师为通过状态
     * */
    public function setPass() {
        $id = Request::instance()->post("tid",false);
        if($id) {
            if(Db::table('anony_teacher')->where(['id'=>$id])->setField('show',1) !== false) {
                return [
                    'status'=>1,
                    'info'=>'successful'
                ];
            }
            else {
                return [
                    'status'=>2,
                    'info'=>'内部错误，请刷新重试！'
                ];
            }
        }
        else {
            return [
                'status'=>-1,
                'info'=>'lose params！'
            ];
        }
    }

    /*
     * 获取数据
     * */
    public function getDataForConf() {
        if(empty($_COOKIE['mediaid'])) {
            return [
                'stauts'=>-2,
                'info'=>'权限超时，请刷新界面'
            ];
        }
        $page = Request::instance()->post("page",1);
        if(is_numeric($page)) {
            $mediaid = $_COOKIE['mediaid'];
            $res = Db::table('anony_teacher')->field('id as tid,name,detail,headimg as teaimg,lessons')->where(['show'=>2,'mediaid'=>$mediaid])->page($page,15)->order("subtime desc")->select();
            foreach ($res as $index => $item) {
                $res[$index]['lessons']=json_decode($res[$index]['lessons'],true);
            }
            return [
                'status'=>1,
                'data'=>$res,
                'info'=>'ok'
            ];
        }
        else {
            return [
                'status'=>-1,
                'info'=>'参数不符合要求！'
            ];
        }
    }

    /*
     * 设置不允许通过
     * */
    public function setUnPass() {
        $id = Request::instance()->post("tid",false);
        if($id) {
            if(Db::table('anony_teacher')->where(['id'=>$id])->setField('show',3) !== false) {
                return [
                    'status'=>1,
                    'info'=>'successful'
                ];
            }
            else {
                return [
                    'status'=>2,
                    'info'=>'内部错误，请刷新重试！'
                ];
            }
        }
        else {
            return [
                'status'=>-1,
                'info'=>'lose params！'
            ];
        }
    }

    /*
     * 搜索教师信息
     * */
    public function searchTea() {
        if(empty($_COOKIE['mediaid'])) {
            return [
                'stauts'=>-2,
                'info'=>'权限超时，请刷新界面'
            ];
        }
        $searchWords = Request::instance()->post('searchWords',false);
        $page = Request::instance()->post('page',false);
        if ($searchWords !== false || ($page !== false && empty($page)) ) {
            $mediaid = $_COOKIE['mediaid'];
            if(empty($searchWords)) {
                // 当前状态表示默认，需要将所有的数据全部显示
                $res = Db::table("anony_teacher")->field("id as tid,name,detail,lessons,headimg as teaimg,show")
                    ->where("`mediaid`='$mediaid'")
                    ->order("`subtime` asc")
                    ->page($page,15)
                    ->select();
                foreach ($res as $index => $item) {
                    $res[$index]['lessons']=json_decode($res[$index]['lessons'],true);
                }
            }
            else {
                // 这里需要按照搜索提供的关键字返回数据，其实就是简单的加个分页，其他的全部让前端处理
                $res = Db::table("anony_teacher")->field("id as tid,name,detail,lessons,headimg as teaimg,show")
                    ->where("`mediaid`='$mediaid' and (`name` like '%$searchWords%' or `lessons` like '%$searchWords%' or `detail` like '%$searchWords%') ")
                    ->order("`subtime` desc")
                    ->page($page,15)
                    ->select();
                foreach ($res as $index => $item) {
                    $res[$index]['lessons']=json_decode($res[$index]['lessons'],true);
                }
            }
            return [
                'status'=>1,
                'data'=>$res,
                'info'=>'ok'
            ];
        }
        else {
            return [
                'status'=>-1,
                'info'=>'lose params！'
            ];
        }
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

    /*
     * 修改当前公众号的新增教师模式
     * */
    public function changeTeaStu() {
        if(empty($_COOKIE['mediaid'])) {
            return [
                'stauts'=>-2,
                'info'=>'权限超时，请刷新界面'
            ];
        }
        $check = Request::instance()->post('check',false);
        $upImg = Request::instance()->post('upImg',false);
        if ($check !== false && ($check == 1 || $check == 2) && $upImg !== false && ($upImg == 1 || $upImg == 2) ) {
            $mediaid = $_COOKIE['mediaid'];
            if(Db::table('anony_conf')->where(['mediaid'=>$mediaid,'name'=>'check'])->setField('value',$check) !== false
                && Db::table('anony_conf')->where(['mediaid'=>$mediaid,'name'=>'allowUpImg'])->setField('value',$upImg) !== false) {
                return [
                    'status'=>1,
                    'info'=>'successful'
                ];
            }
            else {
                return [
                    'status'=>2,
                    'info'=>'内部错误，请刷新重试！'
                ];
            }
        }
        else {
            return [
                'status'=>-1,
                'info'=>'lose params！'
            ];
        }

    }

    /*
     * 获取某一个公众号下的评论内容
     * */
//    public function getComment() {
//        if(empty($_COOKIE['mediaid'])) {
//            return [
//                'stauts'=>-2,
//                'info'=>'权限超时，请刷新界面'
//            ];
//        }
//        $mediaid = $_COOKIE['mediaid'];
//        $comment = new Comment();
//        $page = Request::instance()->post('page',0);
//        return [
//            'status'=>1,
//            'data' => $comment->comByMed($mediaid,$page)
//        ];
//    }

    /*
     * 搜索某个关键字的评论
     * */
    public function searchComment() {
        if(empty($_COOKIE['mediaid'])) {
            return [
                'stauts'=>-2,
                'info'=>'权限超时，请刷新界面'
            ];
        }
        $mediaid = $_COOKIE['mediaid'];
        $comment = new Comment();
        $searchKey = Request::instance()->post('searchWords');
        $page = Request::instance()->post('page',0);
        $whereOpt = 'anony_comment.display = 1 ';
        if(empty($searchKey)) {
            $res = $comment->comForSea($mediaid,$whereOpt,$page);
            foreach ($res as $index => $item) {
                $res[$index]['lessons']=json_decode($res[$index]['lessons'],true);
                $res[$index]['cretime']=date('Y-m-d G:i:s',$res[$index]['cretime']);
            }
            return [
                'status'=> 1,
                'data' => $res
            ];
        }
        else {
            $whereOpt .= ' and ';
            $seaArr =['anony_comment.content','anony_teacher.name','anony_teacher.lessons'];
            $creTime = strtotime($searchKey);
            if($creTime === false) {
                foreach ($seaArr as $item) {
                    $whereOpt .= $item." like %'$searchKey'% or ";
                }
                $whereOpt = substr($whereOpt,0,strlen($whereOpt) - 3);
            }
            else {
                $whereOpt .= 'anony_comment.cretime >= '.$creTime;
            }
            $res = $comment->comForSea($mediaid,$whereOpt,$page);
            foreach ($res as $index => $item) {
                $res[$index]['lessons']=json_decode($res[$index]['lessons'],true);
                $res[$index]['cretime']=date('Y-m-d G:i:s',$res[$index]['cretime']);
            }
            return [
                'status' => 1,
                'data' => $res
            ];
        }
    }

    /*
     * 隐藏某一条评论
     * */
    public function hideComment() {
        if(empty($_COOKIE['mediaid'])) {
            return [
                'stauts'=>-2,
                'info'=>'权限超时，请刷新界面'
            ];
        }
        $comment = new Comment();
        $commentId = Request::instance()->post('commentId',false);
        if($commentId === false) {
            return [
                'status'=> -1,
                'info' => '缺少必要参数'
            ];
        }
        else {
            return [
                'status'=>$comment->hideOneComment($commentId) ? 1 : 2,
                'info' => 'inner error'
            ];
        }
    }





}

