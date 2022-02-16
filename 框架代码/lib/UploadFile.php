<?php

class UpLoad{

    //上传模拟函数
    public static function https_request($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $file_contents = curl_exec($ch);
        if ($file_contents==false){
            var_dump(curl_error($ch));
        }
        curl_close($ch);
        return $file_contents;
    }

    //上传图片
    public static function UpLoadImage($file_address,$access_token){

        $params = new CURLFile(realpath($file_address));

        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";

        $data = array('filename' =>$params);

        $res = UpLoad::https_request($url,$data);

        return $res;
    }

    //上传视频
    public static function UpLoadVideo($file_address,$access_token){

        $params = new CURLFile(realpath($file_address));

        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=video";

        $data = array('filename' =>$params);

        $res = UpLoad::https_request($url,$data);

        return $res;
    }

    //上传语音
    public static function UpLoadVoice($file_address,$access_token){

        $params = new CURLFile(realpath($file_address));

        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=voice";

        $data = array('filename' =>$params);

        $res = UpLoad::https_request($url,$data);

        return $res;
    }

    //上传缩略图
    public static function UpLoadThumb($file_address,$access_token){

        $params = new CURLFile(realpath($file_address));

        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=thumb";

        $data = array('filename' =>$params);

        $res = UpLoad::https_request($url,$data);

        return $res;
    }


    //获取用户发送的图片
    public static function GetImg($media_id,$access_token){

        $url = "https://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$media_id;
        //判断是否存在文件存放目录
        if(!file_exists("./uploads")){
            mkdir("./uploads",0777,true);
        }
        $imgpath = "./uploads/".$media_id.'.jpg';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $fp = fopen($imgpath,'wb');
        curl_setopt($ch,CURLOPT_FILE,$fp);
        curl_setopt($ch,CURLOPT_HEADER,0);
        // curl_setopt($ch, CURLOPT_NOBODY,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $res;
    }
}