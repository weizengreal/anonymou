<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/3/30
 * Time: 下午1:05
 */

namespace app\index\controller;
use think\Controller;
class message extends Controller {


    /*
     * 成功的操作反馈
     * */
    public function msg_success($title=null,$content=null,$buttonCot=null,$redirectUri=null) {
        return $this->fetch("success",[
            'title'=>empty($title) ? "操作成功" : $title,
            'content'=>empty($content) ? "" : $content ,
            'buttonCot'=>empty($buttonCot) ? "确定" : $buttonCot ,
            'redirectUri'=>empty($redirectUri) ? "" : urldecode($redirectUri),
        ]);
    }

    /*
     * 失败的操作反馈
     * */
    public function msg_error($title=null,$content=null,$buttonCot=null,$redirectUri=null) {
        return $this->fetch("error",[
            'title'=>empty($title) ? "操作失败" : $title,
            'content'=>empty($content) ? "您好，您当前的操作失败，请重试或联系管理员！谢谢您的大力支持" : $content ,
            'buttonCot'=>empty($buttonCot) ? "确定" : $buttonCot ,
            'redirectUri'=>empty($redirectUri) ? "" : $redirectUri,
        ]);
    }

}


