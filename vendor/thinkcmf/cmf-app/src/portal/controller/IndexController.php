<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;

class IndexController extends HomeBaseController
{

    // 首页
    public function index()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'cozy';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        file_put_contents('../data/runtime/log/1.txt',json_encode($_REQUEST).PHP_EOL, FILE_APPEND);
        if( $tmpStr == $signature ){
            header('content-type:text');
            echo $_GET['echostr'];
            file_put_contents('../data/runtime/log/1.txt',"success".PHP_EOL, FILE_APPEND);
            return true;
        }else{
            echo "failure";
            file_put_contents('../data/runtime/log/1.txt',"failure".PHP_EOL, FILE_APPEND);
            return false;
        }

    }


    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt 密文消息
     */
    public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
    {
        //排序
        try {
            $array = array($encrypt_msg, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return array(ErrorCode::$OK, sha1($str));
        } catch (Exception $e) {
            //print $e . "\n";
            return array(ErrorCode::$ComputeSignatureError, null);
        }
    }

    public function wxToken()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'cozy';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        file_put_contents('../data/runtime/log/1.txt',json_encode($_REQUEST).PHP_EOL, FILE_APPEND);
        if( $tmpStr == $signature ){
            header('content-type:text');
            echo $_GET['echostr'];
            file_put_contents('../data/runtime/log/1.txt',"success".PHP_EOL, FILE_APPEND);
            return $_GET['echostr'];
        }else {
            echo "failure";
            file_put_contents('../data/runtime/log/1.txt', "failure" . PHP_EOL, FILE_APPEND);
            return false;
        }
    }

}

