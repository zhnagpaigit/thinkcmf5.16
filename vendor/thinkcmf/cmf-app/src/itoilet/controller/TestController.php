<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/4/13
 * Time: 16:05
 */

namespace app\itoilet\controller;



use cmf\controller\BaseController;

/**
 *
 */
class TestController extends BaseController {
    public $appid = "wxa80ff8a91552c2ff";//智能马桶
    public $scret = "32f59df40b9db6398c0e9b4922e24843";
    public $apiUrl = "https://mycmf.yirj.xin";
    public function index()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'cozy';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
//        file_put_contents('../data/runtime/log/1.txt',json_encode($_REQUEST).PHP_EOL, FILE_APPEND);
        if( $tmpStr == $signature ){
            header('content-type:text');
            echo $_GET['echostr'];
//            file_put_contents('../data/runtime/log/1.txt',"success".PHP_EOL, FILE_APPEND);
//            return 1;
        }else{
            echo "failure";
//            file_put_contents('../data/runtime/log/1.txt',"failure".PHP_EOL, FILE_APPEND);
//            return 0;
        }
        exit;

    }

    public function getopen()
    {

      //  $callbackUrl = urlencode($this->apiUrl.url("test/saveopenid"));
        $callbackUrl = "https://mycmf.yirj.xin/itoilet/test/saveopenid";
        $codeUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$callbackUrl."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
        Header("Location: $codeUrl");
    }


    /**
     * 微信第三方登录 --
     */
    public function saveopenid()
    {

        echo '  <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no">';
        $code = $_GET['code'];
        if (isset($code)) {//登录成功
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->scret . "&code=" . $code . "&grant_type=authorization_code";
            echo "code = ",$code,'<BR>';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT,  'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $tmpInfo=curl_exec($ch);
            if (curl_errno($ch)) {
                return curl_errno($ch);
            }
            curl_close($ch);

            $access = json_decode($tmpInfo, true);
            echo "openID 数组：<br/>";
            $open_id = $access['openid'];
            dump($access);


            // 获取token:
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->scret;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($data,true);
            echo "token 数组：<br/>";
            $access_token = $data['access_token'];
            dump($data);





            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$open_id."&lang=zh_CN";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($data,true);
            echo "userInfo 数组：<br/>";
//            $access_token = $data['access_token'];

            echo "<table>
                    <tr><th>名称</th>数据</th></tr>
                    <tr><th>openID</th><td>{$open_id}</td></tr>
                     <tr><th>昵称</th><td>{$data['nickname']}</td></tr>
                     <tr><th>省</th><td>{$data['province']}</td></tr>
                     <tr><th>市</th><td>{$data['city']}</td></tr>
                     <tr><th>图像</th><td><image src='".$data['headimgurl']."' /></td></tr>
            </table>";



        }

    }



}