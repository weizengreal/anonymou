<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/4/24
 * Time: 下午8:01
 */
namespace app\index\service;

use app\index\model\User;
use think\Db;

class Basic {

    /*
     * 获取当前用户的状态信息，数据库角度自动鉴定该数据是否有效
     * */
    public static function getStatus() {
        if(isset( $_COOKIE['accessToken'] )) {
            $user = new User();
            $accessToken = $_COOKIE['accessToken'];
            if(!$user->isExist(array('accesstoken'=>$accessToken,'overtime'=>array(">",time())))) {
                $accessToken = null;
            }
        }
        else {
            $accessToken = null;
        }
        return $accessToken;
    }

    /*
     * 记录当前状态信息到user数据库
     * */
    public static function setStu($openid,$accessToken = null) {
        if(empty($accessToken)) {
            $user = new User();
            $accessToken = password_hash($openid,PASSWORD_BCRYPT);
            if($user->updata($openid,array('accesstoken'=>$accessToken,'overtime'=>time()+3600*6*24))) {
                // 设置该accesstoken有效期为6天
                setcookie("accessToken",$accessToken,time()+3600*6*24,"/");
            }
        }
        else {
            setcookie("accessToken",$accessToken,time()+3600*6*24,"/");
        }
        return $accessToken;
    }

    /*
     * 根据授权信息抓取用户是否已经打分
     * 返回true表示已经打分，
     * */
    public static function isScore($tid,$accessToken) {
        if(!empty($accessToken)) {
            $res = Db::query("SELECT count(*) as scoreCount FROM `anony_user` as a,`anony_score` as b  WHERE a.openid=b.openid and a.accesstoken = '$accessToken' and b.tid='$tid'");
            return $res[0]['scoreCount'] == 1;
        }
        else {
            return false;
        }
    }

    /*
     * 检测用户身份是否正确
     * */


}
