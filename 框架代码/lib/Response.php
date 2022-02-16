<?php
include_once __DIR__ . "/Msg.php";
include_once __DIR__ . "/Menu.php";


class Response
{

    
    //将对象转化为数组返回
    public static function ReturnArray($res)
    {
        $i = 0;
        while ($row = $res->fetch_assoc()) {
            $ret[$i] = $row;
            // echo $ret[$i];
            $i++;
        }
        // echo $ret[0];
        return $ret;
    }
    //文本转换成可以被数据库识别的数据项
    public static function TransText($text)
    {
        $text = '\'' . $text . '\'';
        return $text;
    }

    //返回xml数据给微信服务器
    public static function ReturnXml($xml)
    {
        header("Content-type: text/xml");
        echo $xml;
    }

    //第一次通过微信服务器的校验
    public static function Check()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = 'baimuzhi';   //这里直接替换token即可
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo ($_GET['echostr']);
        } else {
            echo ('error');
        }
    }

    //收到文本消息时的回复函数
    public static function ReplyText($xml)
    {
        if ($xml->Content == '菜单') {
            Response::GetMenu($xml);
            }
      
        elseif (substr($xml->Content, 0, 1) == '@') {

            //第一个字符为@，可以判断为指令

            Response::Menu($xml, substr($xml->Content, 1, 2), substr($xml->Content, 3, strlen($xml->Content) - 3));
        }elseif (substr($xml->Content, 0, 1) == '#') {

            //第一个字符为#，可以判断回复新增

            Msg::StudyReply($xml, substr($xml->Content, 1, 10), substr($xml->Content, 11, strlen($xml->Content) - 11));
        } else {
            //不是指令，就是正常聊天的语句
            Msg::ReplyMsgData($xml,$xml->Content);
        }
    }

    //收到图片消息的回复
    public static function ReplyImg($xml)
    {
          //获取图片的媒体id
        $id = $xml->MediaId; 
        Msg::SendMsgImg($xml,$id);
     
    
    }

    //收到语音消息时的回复
    public static function ReplyVoice($xml)
    {
        if ($xml->Recognition == '菜单') {
            Response::GetMenu($xml);
        } elseif (substr($xml->Recognition, 0, 1) == '@') {

            //第一个字符为@，可以判断为指令

            Response::Menu($xml, substr($xml->Recognition, 1, 2), substr($xml->Recognition, 3, strlen($xml->Recognition) - 3));
        } else {
            //不是指令，就是正常聊天的语句
            Msg::ReplyMsgData($xml,$xml->Recognition);
        }
    }


    //收到视频消息的回复,包括小视频
    public static function ReplyVedio($xml)
    {
           $id = $xml->MediaId;
           Msg::SendMsgVideo($xml,$id,'您的视频消息','我还不会处理这个请求');
    }


    //收到用户链接消息的回复
    public static function ReplyLink($xml)
    {
       Msg::SendMsgText($xml,'我们还不会处理这个链接哦');
    }


    //收到事件消息的回复
    public static function ReplyEvent($xml)
    {
        //用户关注消息事件
        if ($xml->Event == 'subscribe') {
            Response::GetMenu($xml);

            //用户取消关注
        } elseif ($xml->Event == 'unsubscribe') {
        }
    }


    //自定义菜单事件（个人订阅号不可用）
    public static function ReplyMenu($xml)
    {

    }

    //收到其他类型的消息回复
    public static function ReplyOther($xml)
    {
        Msg::SendMsgText($xml,'我还不会处理这个链接哦');
    }


    //菜单操作
    public static function Menu($xml, $menu, $key)
    {
        //   Msg::SendMsgText($xml,"menu:".$menu."key:".$key);
        if ($menu == 'fk') {
            Menu::FeedBack($xml, $key);
        }
        elseif($menu == 'ql'){
        Response::ClearMsg($xml);
        }
         elseif ($menu == 'bd') {
            // Msg::SendMsgText($xml, '绑定成功，您绑定账号为 || ' . $key);
            Menu::Bind($xml,$key);
        } elseif ($menu == 'ss') {
            Msg::SendMsgText($xml, '抱歉，没有搜索到内容');
            
        } elseif ($menu == 'wz') {
           Menu::SearchArtic($xml,$key);
        } else {
            Msg::SendMsgText($xml, "抱歉，错误的指令" . $menu);
        }
    }

    //发送菜单
    public static function GetMenu($xml)
    {
        $menu =
            "
        您好，欢迎关注百沐植科技（BaiMuZhiCom），以下是本公众号的菜单，你也可以回复文本或语音消息 菜单  获取本菜单
------------------------------
>>@fk+反馈内容（向我们反馈文本消息）
------------------------------
>>@bd+百沐植用户id（把百沐植用户id与该微信号绑定)
------------------------------
>>@ss+关键字（搜索疾病的相关信息）
------------------------------
>>@wz+关键字（搜索公众号相关文章）
------------------------------
>>不加 @  符号（正常与公众号聊天）
        ";
        Msg::SendMsgText($xml, $menu);
    }


    //发送post请求
    public static function post($url, $post_data = '', $timeout = 5){

        $ch = curl_init();

        if($post_data != ''){
            $url = $url.'?grant_type=client_credential&appid='.$post_data['appid'].'&secret='.$post_data['secret'];
      
            curl_setopt ($ch, CURLOPT_URL, $url);
        }

        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $file_contents = curl_exec($ch);
        return $file_contents;
    }


    //文件上传post
    public static function FilePost($url,$data)
    {
     
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);//启用POST提交
            curl_setopt($ch, CURLOPT_PORT, 80); //设置端口
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
            $aa = curl_exec($ch);
            curl_close($ch);
            return $aa;
    }
    
    //获取Token
    public static function GetToken($appid,$secret)
    {
        $appid="wxfc139f5c4bd73978";
        $secret="a949e032113ed91508b8fbc29eb2e1ac";
        $url='https://api.weixin.qq.com/cgi-bin/token';

        $data=Response::post($url,array(
            'appid' => 'wxf1bb2126ff63cf68&secret',
            'secret' => 'a949e032113ed91508b8fbc29eb2e1ac',));
            $data = json_decode($data);
            return $data->access_token;
    }


    //清理学习的消息
    public static function ClearMsg($xml)
    {
      
            $res = Database::MysqlQuery('delete from  reply where code = 0');
            if($res){
                Msg::SendMsgText($xml,'清理成功');
            }else{
                Msg::SendMsgText($xml,'清理失败');
            }
          
    }


}


