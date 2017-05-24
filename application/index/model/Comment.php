<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/4/20
 * Time: 下午5:03
 */
namespace app\index\model;
use think\Model;

class Comment extends Model {


    public function isExist($openidArr) {
        if(!is_array($openidArr)) {
            $openidArr=array(
                'openid'=>"$openidArr"
            );
        }
        if( $this->where($openidArr)->count() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    //新增数据
    public function addNew($arr) {
        if($this->insert($arr)){
            return true;
        }
        else{
            return false;
        }
    }

    //批量传入关联数组以获取基本信息，返回一条数据
    public function getInfoByArr($arr) {
        return $this->where($arr)->find();
    }

    /*
     * 获取分页数据
     * */
    public function getPageData($tid,$start,$limit=12) {
        return $this->field("anony_user.headimgurl,anony_user.nickname,anony_comment.cretime,anony_comment.content")->where("anony_comment.tid = '$tid'")->join("__USER__","anony_user.openid=anony_comment.openid")->order("anony_comment.cretime asc")->limit($start,$limit)->select();
//        return $this->field("``")->where("`tid`='$tid'")->order("cretime desc")->limit($start,$limit)->select();
    }

    /*
     * 根据openid更新函数
     * 参数1：需要更改的用户openid
     * 参数2：待更新的关联数组
     * */
    public function updata($openid,$upArr) {
        if($this->where("`openid`='$openid'")->update($upArr) !== false){
            return true;
        }
        else{
            return false;
        }
    }
}