<?php
$signature = $_GET["signature"];
$timestamp = $_GET["timestamp"];
$nonce = $_GET["nonce"];

$token = 'cozy';
$tmpArr = array($token, $timestamp, $nonce);
sort($tmpArr, SORT_STRING);
$tmpStr = implode( $tmpArr );
$tmpStr = sha1( $tmpStr );
file_put_contents('1.txt',json_encode($_REQUEST).PHP_EOL, FILE_APPEND);
if( $tmpStr == $signature ){
    header('content-type:text');
    echo $_GET['echostr'];
    file_put_contents('1.txt',"success".PHP_EOL, FILE_APPEND);
    return true;
}else{
    echo "failure";
    file_put_contents('1.txt',"failure".PHP_EOL, FILE_APPEND);
    return false;
}