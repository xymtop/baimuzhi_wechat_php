<?php
//数据库相关
class Database{
    public static function MysqlQuery($query)
    {
        $servername = "";   //数据库地址
        $username = "";          //登录名
        $password = "";            //数据库密码
        $dataname = "";          //数据库名
        $conn = new mysqli($servername, $username, $password,$dataname);
        if (!$conn){
            die("连接失败: " . $conn->connect_error);
        }

        $res = $conn->query($query);

        $conn->close();

        return $res;

    }
}