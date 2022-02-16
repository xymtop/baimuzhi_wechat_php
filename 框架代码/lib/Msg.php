<?php
include_once __DIR__."/Response.php";


//这是回复消息的相关函数
class Msg
{
    //回复文本消息
    public static function SendMsgText($xml,$content)
    {
       $temp = "
       <xml>
       <ToUserName><![CDATA[".$xml->FromUserName."]]></ToUserName>
       <FromUserName><![CDATA[".$xml->ToUserName."]]></FromUserName>
       <CreateTime>".$xml->CreateTime."</CreateTime>
       <MsgType><![CDATA[text]]></MsgType>
       <Content><![CDATA[".$content."]]></Content>
       </xml>
     ";
     Response::ReturnXml($temp);
    }

    //回复图片消息
    public static function SendMsgImg($xml,$id)
    {
         $temp = "
         <xml>
        <ToUserName><![CDATA[".$xml->FromUserName."]]></ToUserName>
        <FromUserName><![CDATA[".$xml->ToUserName."]]></FromUserName>
       <CreateTime>".$xml->CreateTime."</CreateTime>
        <MsgType><![CDATA[image]]></MsgType>
        <Image>
            <MediaId><![CDATA[".$id."]]></MediaId>
        </Image>
        </xml>
        ";
        Response::ReturnXml($temp);
    }


    //回复语音消息
    public static function SendMsgVoice($xml,$id)
    {
        $temp = 
        "
        <xml>
      <ToUserName><![CDATA[".$xml->FromUserName."]]></ToUserName>
       <FromUserName><![CDATA[".$xml->ToUserName."]]></FromUserName>
       <CreateTime>".$xml->CreateTime."</CreateTime>
        <MsgType><![CDATA[voice]]></MsgType>
        <Voice>
            <MediaId><![CDATA[".$id."]]></MediaId>
        </Voice>
        </xml>
        ";
        Response::ReturnXml($temp);
    }

    //回复视频消息
    public static function SendMsgVideo($xml,$id,$title,$description)
    {
        $temp = 
        "
        <xml>
        <ToUserName><![CDATA[".$xml->FromUserName."]]></ToUserName>
       <FromUserName><![CDATA[".$xml->ToUserName."]]></FromUserName>
       <CreateTime>".$xml->CreateTime."</CreateTime>
        <MsgType><![CDATA[video]]></MsgType>
        <Video>
            <MediaId><![CDATA[".$id."]]></MediaId>
            <Title><![CDATA[".$title."]]></Title>
            <Description><![CDATA[".$description."]]></Description>
        </Video>
        </xml>
        ";
        Response::ReturnXml($temp);
    }


     //回复音乐消息
     public static function SendMsgMusic($xml,$title,$id)
     {
         $temp = 
         "
         <xml>
       <ToUserName><![CDATA[".$xml->FromUserName."]]></ToUserName>
       <FromUserName><![CDATA[".$xml->ToUserName."]]></FromUserName>
       <CreateTime>".$xml->CreateTime."</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        <Music>
            <Title><![CDATA[".$title."]]></Title>
            <Description><![CDATA[DESCRIPTION]]></Description>
            <MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl>
            <HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
            <ThumbMediaId><![CDATA[".$id."]]></ThumbMediaId>
        </Music>
        </xml>
         ";
         Response::ReturnXml($temp);
     }

      //回复图文消息,暂时只支持回复一条消息
    public static function SendMsgAritic($xml,$title,$description,$picurl,$url)
    {
        $temp = 
        "
        <xml>
       <ToUserName><![CDATA[".$xml->FromUserName."]]></ToUserName>
       <FromUserName><![CDATA[".$xml->ToUserName."]]></FromUserName>
       <CreateTime>".$xml->CreateTime."</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>1</ArticleCount>
        <Articles>
            <item>
            <Title><![CDATA[".$title."]]></Title>
            <Description><![CDATA[".$description."]]></Description>
            <PicUrl><![CDATA[".$picurl."]]></PicUrl>
            <Url><![CDATA[".$url."]]></Url>
            </item>
        </Articles>
        </xml>
        ";
        Response::ReturnXml($temp);
    }
   

    //数据库查询回复的消息
    public static function ReplyMsgData($xml,$text)
    {
              $text = Response::TransText($text);
             $res = Database::MysqlQuery('select gettext from reply where posttext like %'.$text.'% and code = 1;');
             if($res->num_rows){
                 //这里已经匹配到需要回复的语句
                 $res = Response::ReturnArray($res)[0]['gettext'];
                 Msg::SendMsgText($xml,$res);
             }else{
                $id = time();
                // $text = Response::TransText($text);
                //表示没有匹配到语句
                $res = Database::MysqlQuery('insert into reply (id,posttext,code) values ('.$id.','.$text.',0);');
                if($res){
                    $msg  = 
                    "
------------------------------
很抱歉我还不会回答您的这个问题哦
------------------------------
问题：".$text."
------------------------------
可以回复
#".$id."+回复内容
来教我如何回复哦
------------------------------
                    ";
                    Msg::SendMsgText($xml,$msg);
                }else{
                 Msg::SendMsgText($xml,'兄弟你手速太快了，慢一点呗！！！');

                }
               
             }
    }


    //学习如何回复
    public static function StudyReply($xml,$id,$text)
    {
          $text = Response::TransText($text);
          $res = Database::MysqlQuery('update reply  set  gettext = '.$text.' where id = '.$id);
          $res = Database::MysqlQuery('update reply  set  code = 1 where id = '.$id);
          if($res){   
            //已经添加
            $msg  = 
            "
------------------------------
我已经学会这个问题了，谢谢您
------------------------------
回复:".$text."
------------------------------
            ";
            Msg::SendMsgText($xml,$msg);
          }else{
               Msg::SendMsgText($xml,'臣妾学不会呀！');
          }

    }

}