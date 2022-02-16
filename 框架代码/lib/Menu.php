<?php
include_once __DIR__."/DataBase.php";
include_once __DIR__."/Msg.php";

//菜单操作函数
class Menu
{
 
    //反馈
    public static function FeedBack($xml,$key)
    {
        $id = Response::TransText(time());
        $wxid = Response::TransText($xml->FromUserName);
        $key = Response::TransText($key);
        $res = Database::MysqlQuery('insert into feedback (id,wxid,text,code) values ('.$id.','.$wxid.','.$key.',1)');
        if($res){
            $msg = 
            "
------------------------------
>>反馈成功！
------------------------------
------------------------------
>>记录id为".$id."
------------------------------
------------------------------
>>反馈消息为".$key."
------------------------------
            ";
            Msg::SendMsgText($xml,$msg);
        }else{
             Msg::SendMsgText($xml,'反馈失败');
        }
       
    }

    //绑定账号
    public static function Bind($xml,$key)
    {
           $res = Response::ReturnArray(Database::MysqlQuery('select username from user where id = '.$key))[0];
           if($res){
               $wxid = Response::TransText($xml->FromUserName);
            $res = Database::MysqlQuery('update user set wxid = '.$wxid);
            if($res){
                $msg = 
                "
    ----------------------
    >>绑定成功！
    ----------------------
    ----------------------
    >>您的绑定用户名为为
    ".$res."
    ---------------------
    ---------------------
    >>微信id为
    ".$wxid."
    --------------------
                ";
                Msg::SendMsgText($xml,$msg);
            }else{
                Msg::SendMsgText($xml,'抱歉，绑定失败');
            }
           }else{
               Msg::SendMsgText($xml,'抱歉，没有查询到您的用户id');
           }
    }

     //搜索疾病
     public static function SearchDis($xml,$key)
     {

     }


      //搜索公众号文章
    public static function SearchArtic($xml,$key)
    {
        $key1 = $key;
        $key = Response::TransText($key);
        $res = Database::MysqlQuery('select * from artic where keyword REGEXP '.$key.'or title REGEXP '.$key);
        $res1 = $res;
        if($res->num_rows){
       
          $res = Response::ReturnArray($res);

          $i = 0;
        //   $temp = "";
          $msg = "";
          while($i<$res1->num_rows)
          {
            $temp = 
            "
  >>标题：".$res[$i]["title"]."
  ----------------------
  >>地址：".$res[$i]["url"]."
  --------------------
  ";
            $i++;
            $msg = $msg.$temp;
          }

Msg::SendMsgText($xml,$msg);


        }else{
            //没有查询到信息
            $msg = 
            "
----------------------
>>搜索结果为 0
----------------------
数据库暂时没有找到
--------------------
#".$key1."
--------------------
            ";
            Msg::SendMsgText($xml,$msg);
        }
    }

    
 
     

    

}