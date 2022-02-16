<?php
include_once __DIR__."/Msg.php";
include_once __DIR__."/Response.php";


//路由类，入口
class Route
{
      public static function Run()
      {
          //获取当前请求的xml数据
            $input = file_get_contents('php://input');
            //解析xml数据,返回解析对象
            $xml = simplexml_load_string($input);
            
             //文本
            if($xml->MsgType=='text'){
             Response::ReplyText($xml);
                        //图片
            }elseif($xml->MsgType=='image'){
                
                Response::ReplyImg($xml);
                //语音
            }elseif($xml->MsgType=='voice'){
                
                Response::ReplyVoice($xml);
                //视频
            }elseif($xml->MsgType=='video'){
                
                Response::ReplyVedio($xml);
                //小视频
            }elseif($xml->MsgType=='shortvideo'){
                
                Response::ReplyVedio($xml);
                //链接
            }elseif($xml->MsgType=='link'){
                
                Response::ReplyLink($xml);
                //事件消息类型
            }elseif($xml->MsgType=='event'){
               Response::ReplyEvent($xml); 
               
                //其他消息类型
            }else{
                
               Response::ReplyOther($xml);
            }
            

      }
}