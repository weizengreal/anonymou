<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\index\logic\Userhandle;
use app\index\service\Basic;

class Index extends Controller {
    public function index() {
        if(isset($_GET['media_id']) ) {
            // 将公众号标记放到cookie里面
            $mediaid = Request::instance()->get("media_id");
            setcookie("mediaid",$mediaid,time()+3600*24*30*6);
        }
        else {
            //先检测是否已经缓存过mediaid，有则直接赋值，没有则导向默认
            if(isset($_COOKIE['mediaid'])) {
                $mediaid = $_COOKIE['mediaid'];
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '入口出错' , 'content'=>"请前往指定公众号打开微评教!")));
            }
        }
        $media=new \app\index\model\Media();
        $mediaArr = $media->getInfoByArr(array('mediaid'=>$mediaid));
        if(!empty($mediaArr)) {
            $accessToken = Basic::getStatus();
            return $this->fetch("index",[
                'add'=>url("Index/Index/add","",false,true),
                'authUrl'=> !empty($accessToken) ? "{0}" : getWxAuthUrl("base","{0}","" ),
                'getData'=>url("Index/Datahandle/getData","",false),
                'searchData'=>url("Index/Datahandle/searchData","",false),
                'url'=>url("Index/Index/detail","",false,true),
                'isAuth'=> empty($accessToken) ? 2 : 1,
                'schoolName'=>empty($mediaArr['schoolname']) ? "微评教" : $mediaArr['schoolname']
            ]);
        }
        else {
            $this->redirect(url("index/message/msg_error",array('title' => '入口出错' , 'content'=>"请前往指定公众号打开微评教!")));
        }
    }

    public function add() {
        $code = Request::instance()->get("code",false);
        $mediaid = isset($_COOKIE['mediaid']) ? $_COOKIE['mediaid'] : 'jld';
        $confArr = Db::table('anony_conf')->where(['mediaid'=>$mediaid])->order('name desc')->select();
        if($code !== false) {
            $wxScope = new \wxLib\Scope();
            $scopeData = $wxScope->getToken(Request::instance()->get("code"));
            if( !empty($scopeData['openid']) ) {
                $user = new Userhandle();
                if($user->addNewUser($scopeData)) {
                    return $this->fetch("add",[
                        "addTeacher"=>url("Index/Datahandle/addTeacher","",false),
                        'headrand'=>rand(1,20),
                        'upImg'=>empty($confArr[1]['value']) ? 2 : $confArr[1]['value'],
                    ]);
                }
                else {
                    $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接或联系管理员")));
                }
            }
            else{
                $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接",'redirectUri' => urlencode(url("Index/index/index", "", false)))));
            }
        }
        else {
            $accessToken = Basic::getStatus();
            if(!empty($accessToken)) {
                // 使用缓存数据
                return $this->fetch("add_test",[
                    "addTeacher"=>url("Index/Datahandle/addTeacher","",false),
                    'headrand'=>rand(1,20),
                    'upImg'=>empty($confArr[1]['value']) ? 2 : $confArr[1]['value'],
                ]);
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接"  )));
            }
        }
    }

    public function add_test() {
        $code = Request::instance()->get("code",false);
        $mediaid = isset($_COOKIE['mediaid']) ? $_COOKIE['mediaid'] : 'jld';
        $confArr = Db::table('anony_conf')->where(['mediaid'=>$mediaid])->order('name desc')->select();
        if($code !== false) {
            $wxScope = new \wxLib\Scope();
            $scopeData = $wxScope->getToken(Request::instance()->get("code"));
            if( !empty($scopeData['openid']) ) {
                $user = new Userhandle();
                if($user->addNewUser($scopeData)) {
                    return $this->fetch("add_test",[
                        "addTeacher"=>url("Index/Datahandle/addTeacher","",false),
                        'headrand'=>rand(1,20),
                        'upImg'=>empty($confArr[1]['value']) ? 2 : $confArr[1]['value'],
                    ]);
                }
                else {
                    $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接或联系管理员")));
                }
            }
            else{
                $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接",'redirectUri' => urlencode(url("Index/index/index", "", false)))));
            }
        }
        else {
            $accessToken = Basic::getStatus();
            if(!empty($accessToken)) {
                // 使用缓存数据
                return $this->fetch("add_test",[
                    "addTeacher"=>url("Index/Datahandle/addTeacher","",false),
                    'headrand'=>rand(1,20),
                    'upImg'=>empty($confArr[1]['value']) ? 2 : $confArr[1]['value'],
                ]);
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接"  )));
            }
        }
    }

    public function detail($tid) {
        $accessToken = null;
        $isScore = false;
        $id = authcode($tid,"DECODE");
        if(Request::instance()->get("code",false)) {
            $wxScope = new \wxLib\Scope();
            $scopeData = $wxScope->getToken(Request::instance()->get("code"));
            if( empty($scopeData['isRightCode']) && !empty($scopeData['openid'])  ) {
                //若获得参数code，则创建该用户，并要求页面进行授权并设置accessToken以更新权限
                $user = new Userhandle();
                if($user->addNewUser($scopeData)) {
                    $accessToken = Basic::setStu($scopeData['openid']);
                }
                else {
                    $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接或联系管理员")));
                }
//                $isAuth = 1;//已授权
//                $isScore = Basic::isScore($id,$accessToken);
            }
            else  if (!empty($scopeData['isRightCode'])) {
                // 这里表示用户在经过静默授权后直接刷新页面，不允许再次刷新cookie
                $accessToken = Basic::getStatus();
//                $isAuth = 1;//已授权
//                $isScore = Basic::isScore($id,$accessToken);
            }
            else {
                $this->redirect(url("index/message/msg_error",array('title' => '内部错误' , 'content'=>"请使用微信端重新打开连接",'redirectUri' => urlencode(url("Index/index/index", "", false)))));
            }
            $isAuth = 1;//已授权
            $isScore = Basic::isScore($id,$accessToken);
        }
        else{
            $accessToken = Basic::getStatus();
            if(! empty($accessToken) ) {
                //存在本地权限--->已授权
                $isAuth = 1;
                $isScore = Basic::isScore($id,$accessToken);
            }
            else {
                $isAuth = 2;
            }
        }
        $teacher = new \app\index\model\Teacher();
        $res = $teacher->getInfoByArr(array('id'=>$id));

        return $this->fetch("detail",[
            'basicData' => $res,
            'tid' => $tid,
            'lessons' => json_decode($res['lessons'],true),
            'isAuth' => $isAuth,
            'showScore' => $isScore ? "" : "none",
            'setScore' => $isScore ? "none" : "",
            'setScoreUrl' => url("Index/Datahandle/setScore","",false),
            'authUrl' => getWxAuthUrl("base",url("Index/Index/detail",array('tid'=>$tid),false,true),"" ),
            'comment' => url("Index/Datahandle/comment","",false),
            'getComment' => url("Index/Datahandle/getComData","",false),
            'getCommentCount' => url("Index/Datahandle/getAllCount","",false),
        ]);
    }

}
