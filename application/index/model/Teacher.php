<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/4/20
 * Time: 下午5:02
 */
namespace app\index\model;
use think\Model;

class Teacher extends Model {

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
     * 根据openid更新函数
     * 参数1：需要更改的用户openid
     * 参数2：待更新的关联数组
     * */
    public function updata($id,$upArr) {
        if($this->where("`id`='$id'")->update($upArr) !== false){
            return true;
        }
        else{
            return false;
        }
    }

    /*
     * 为某一个教师的评论数+1
     * */
    public function addComCount($arr) {
        $this->where($arr)->setInc("comcount",1);
    }


    /*
     * 批量获得教师数据
     * 传入参数start、limit
     * */
    public function getTeaData($start,$mediaid,$limit = 12) {
        return $this
            ->field("id,name,detail,lessons,quality,responsible,headimg as teaimg")
            ->where("`show` = '1' and `mediaid`='$mediaid'")
            ->order("`comcount` desc")
            ->limit($start,$limit)
            ->select();
    }

    public function getSeaData($start,$searchWords,$mediaid,$limit = 12) {
        return $this
            ->field("id,name,detail,lessons,quality,responsible,headimg as teaimg")
            ->where("`show` = '1' and `mediaid`='$mediaid' and (`name` like '%$searchWords%' or `lessons` like '%$searchWords%' or `detail` like '%$searchWords%') ")
            ->order("`comcount` desc")->limit($start,$limit)->select();
    }

    /**
     * york 大学定制化
     *
     * @param $start
     * @param $searchWords
     * @param $mediaid
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getSeaData_gh_d7a0ac02f3ef($start,$searchWords,$mediaid,$limit = 12) {
        return $this
            ->field("id,name,detail,lessons,quality,responsible,headimg as teaimg")
            ->where("`show` = '1' and `mediaid`='$mediaid' and `name` like '%$searchWords%' ")
            ->limit($start,$limit)->select();
    }


}