<?php
/**
 * Created by PhpStorm.
 * User: WeiZeng
 * Date: 2016/8/1
 * Time: 0:51
 * 判定用户是否在线
 * 微信权限鉴定
 */
namespace wxLib;
class Scope {
    private $appId;
    private $appSecret;
//    private $logpath;

    public function __construct() {
        //服务号appid  和   appsecret
        $this->appId=config("axcAppid");
        $this->appSecret=config("axcSecret");
    }

    public function getToken($code){
        $code=$this->getRightCode($code);
        if(! empty($code)){
            $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appId&secret=$this->appSecret&code=$code&grant_type=authorization_code";
            $result=json_decode(file_get_contents($url),true);
            session("re_token",$result['refresh_token']);
            session("openid",$result['openid']);
            session("access_token",$result['access_token']);
        }
        else{
            if(session("?re_token")){
                $refresh_token=session("re_token");
                $url="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=$this->appId&grant_type=refresh_token&refresh_token=$refresh_token";
                $result=json_decode(file_get_contents($url),true);
                if( !empty($result['errcode']) && $result['errcode'] == "40030") {
                    //invalid re_token，原因可能是用户长时间浏览一个带code的网页，刷新之后再次来到这里出现问题或者用户在其他地方重新进行了微信授权操作，这是一个比较少见的情况
                    $result=array(
                        'errcode'=>-3,
                        'errmsg'=>'refresToken换取access_token失败'.$result['errcode'].'错误信息：'.$result['errmsg'],
                    );
                }
                else {
                    $result['isRightCode']=1;
                    session("openid",$result['openid']);
                    session("access_token",$result['access_token']);
                }
//                \think\Log::write(json_encode($result),"Warning");
            }
            else{
                $result=array(
                    'errcode'=>-5,
                    'errmsg'=>'refresToken换取access_token失败'
                );
            }
        }
        return $result;
    }

    //判定accessToken是否有效
    public function judgeAccessToken($access_token,$openid) {
        $url="https://api.weixin.qq.com/sns/auth?access_token=$access_token&openid=$openid";
        $result=json_decode(file_get_contents($url),true);
        // file_put_contents("1.txt", json_encode($result));
        // return $result;
        if($result['errcode']==0) {
            return true;
        }
        else{
            return false;
        }
    }

    //获取unionid
    public function getUnionid($code){
        if(empty($code)) {
            return array(
                'errcode'=>'-3',
                'errMsg'=>'页面停留时间过长或者旧版本返回问题',
                'viewMsg'=>"请关闭浏览器重试！"
            );
        }
        $res=$this->getToken($code);
        if(! empty($res['access_token'])) {
            $access_token=$res['access_token'];
            $openid=$res['openid'];
            $url="https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
            $result=json_decode(file_get_contents($url),true);
            if(! empty($result['unionid'])) {
                return $result;//正确
            }
            else{
                //若失败，销毁session
                session("access_token",null);
                session("openid",null);
                return array(
                    'errcode'=>'-1',
                    'errMsg'=>'unionid 拉取失败,错误代码：'.$result['errcode'].'错误信息：'.$result['errmsg'],
                    'viewMsg'=>"亲!请返回对应公众号重新拉取信息!");
            }
        }
        else {
            return array(
                'errcode'=>'-2',
                'errMsg'=>'access_token 拉取失败,错误代码：'.$res['errcode'].'错误信息：'.$res['errmsg'],
                'viewMsg'=>"亲!请返回对应公众号重新拉取信息!"
            );
        }
    }

    private function getRightCode($code) {
        if($code == session("code")) {
            return null;
        }
        else {
            session("code",$code);
            return $code;
        }
    }








    //------------------------
    //获取开发者接入微信公众号的accessTone
//    private function getAccessToken() {
//        return file_get_contents("http://121.42.57.23/wxJssdk/JssdkInterface.php?type=access_token_web");
//    }
//
//    //更新开发者接入微信公众号的accessTone
//    private function updateAccessToken() {
//        return file_get_contents("http://121.42.57.23/wxJssdk/JssdkInterface.php?type=update_access_token");
//    }
//
//    //通过accessToken获取用户基本信息
//    public function getBasicInfo($openid,$media){
//        if($media=="gh_2f97120599f5"){
//            $access_token=$this->getAccessToken();
//            $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
//            $res = file_get_contents($url);
//            $result=json_decode($res, true);
//            if( empty($result['errcode'])){
//                return $result;
//            }
//            else{
//                return $this->basicInfoFromUpdate($openid);
//            }
//        }
//        else{
//            return null;
//        }
//    }
//
//    //通过updateAccessToken获取用户基本信息
//    private function basicInfoFromUpdate($openid){
//        $access_token=$this->updateAccessToken();
//        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
//        $res = file_get_contents($url);
//        return json_decode($res, true);
//    }
//
//    //判断是否为已知公众号用户
//    public function getMediaId($openid){
//        $access_token=$this->getAccessToken();
//        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
//        $res = file_get_contents($url);
//        $result=json_decode($res, true);
//        if( empty($result['errcode'])){
//            return "gh_2f97120599f5";
//        }
//        else{
//            return "";
//        }
//    }
}
