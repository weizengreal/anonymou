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
        return $this->field("anony_user.headimgurl,anony_user.nickname,anony_comment.cretime,anony_comment.content")
            ->where("anony_comment.tid = '$tid' and anony_comment.display = 1")
            ->join("__USER__","anony_user.openid=anony_comment.openid")
            ->order("anony_comment.cretime asc")
            ->limit($start,$limit)
            ->select();
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

    /*
     * 获取某个mediaId下的评论条数
     * */
    public function comGetCount($mediaId) {
        return $this
            ->where(['display'=>1])
            ->join('__TEACHER__',"anony_teacher.mediaid='$mediaId' and anony_teacher.id=anony_comment.tid")
            ->count();
    }

    /*
     * 根据media_id获取该公众号下的用户评论
     * */
    public function comByMed($mediaId,$page=0,$listRow = 15,$whereOpt=1) {
        return $this
            ->field('anony_comment.cid,anony_teacher.name,anony_teacher.lessons,anony_comment.content,anony_comment.cretime,anony_comment.display')
            ->where($whereOpt)
            ->join('__TEACHER__',"anony_teacher.mediaid='$mediaId' and anony_teacher.id=anony_comment.tid")
            ->order('anony_comment.cretime desc')
            ->page($page,$listRow)
            ->select();
    }

    /*
     * 根据 media_id 获取该公众号下的用户评论，扩展到查询功能
     * 查询逻辑：时间、评论内容
     * */
    public function comForSea($mediaId,$whereOpt,$page=0,$listRow = 15) {
        return $this
            ->field('anony_comment.cid,anony_teacher.name,anony_teacher.lessons,anony_comment.content,anony_comment.cretime,anony_comment.display')
            ->where($whereOpt)
            ->join('__TEACHER__',"anony_teacher.mediaid='$mediaId' and anony_teacher.id=anony_comment.tid")
            ->order('anony_comment.cretime desc')
            ->page($page,$listRow)
            ->select();
    }

    /*
     * 隐藏一个评论
     * */
    public function hideOneComment($commentid) {
        return $this->where(['cid'=>$commentid])->limit(1)->update(['display'=>2]) !== false;
    }

}