<?php
/*
 * 微信消息返回
 * */
namespace wxLib;
//gh_2f97120599f5
class Response {
    private $token;

    public function __construct() {
        $this->token = "aufecmu";
    }

    public function valid() {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ) {
            return true;
        }else{
            return false;
        }
    }

    public function chatBack(){
        if (isset($_GET['echostr']))
        {
            $this->valid();
        }
        else {
            $postDate = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");
            $object= simplexml_load_string($postDate,"SimpleXMLElement",LIBXML_NOCDATA);
            $arr['openid']=$object->FromUserName;
            $arr['mediaid']=$object->ToUserName;
            $arr['MsgType']=$object->MsgType;
            $arr['Event']=$object->Event;
            if($object->Event == "user_get_card") {
                $arr['code']= $object->UserCardCode ;
                $arr['cardId']= $object->CardId ;
            }
            return $arr;
        }
    }

    public function chatBackForInterface(){
        if (isset($_GET['echostr']))
        {
            $this->valid();
        }
        else {
//            file_put_contents("test.txt",json_encode(file_get_contents("php://input")).PHP_EOL,FILE_APPEND);
            $postDate = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");
            $object= simplexml_load_string($postDate,"SimpleXMLElement",LIBXML_NOCDATA);
            return $object;
        }
    }

    public function backText($openId,$publicId,$text){
        $replyXml = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[text]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								</xml>";
        $resultStr = sprintf($replyXml,$openId,$publicId,time(),$text);
        echo $resultStr;die;
    }

    public function error($openId,$publicId){
        $replyXml = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[text]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								</xml>";
        $resultStr = sprintf($replyXml,$openId,$publicId,time(),"请重新点击菜单,若多次失败请联系管理人员!");
        echo $resultStr;die;
    }

}


?>