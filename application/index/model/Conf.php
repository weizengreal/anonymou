<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/4/21
 * Time: 上午11:33
 */
namespace app\index\model;
use think\Model;

class Conf extends Model {
    public function isExist($keyArr) {
        if(!is_array($keyArr)) {
            $keyArr=array(
                'mediaid'=>"$keyArr"
            );
        }
        if( $this->where($keyArr)->count() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    //新增数据
    public function addNew($arr) {
        return $this->insert($arr) !== false;
    }

    /*
     * 新增多条配置信息
     * */
    public function addMore($dataSource) {
        return $this->insertAll($dataSource) !== false;
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
    public function updata($mediaid,$name,$upArr) {
        if($this->where("`mediaid`='$mediaid' and `name`='$name'")->update($upArr) !== false){
            return true;
        }
        else{
            return false;
        }
    }
}