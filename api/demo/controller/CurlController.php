<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/3/18
 * Time: 9:25
 */
namespace api\demo\controller;
use think\App;

class CurlController extends ApiBaseController
{
    // https://www.php.net/manual/zh/book.curl.php

    public  $_url_base='http://test.newcmf.com/api/demo/curl/';
    public $_url_get ;
    public $_url_post;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->_url_get = $this->_url_base.'get';
        $this->_url_post = $this->_url_base.'post';
    }

    public function show()
    {
        $this->init();
    }


    //  -- 当前版本 -- 数组
    public function version()
    {
        $version = curl_version();
        dump($version);
    }

    // -- 初始化 会话 ，返回 资源句柄
    public function init()
    {
        $resource = curl_init();
        dump($resource);
    }

    //-- 设置一个cURL传输选项。
    /*
     * 选项	可选value值	备注
        CURLOPT_AUTOREFERER	当根据Location:重定向时，自动设置header中的Referer:信息。
        CURLOPT_BINARYTRANSFER	在启用CURLOPT_RETURNTRANSFER的时候，返回原生的（Raw）输出。
        CURLOPT_COOKIESESSION	启用时curl会仅仅传递一个session cookie，忽略其他的cookie，默认状况下cURL会将所有的cookie返回给服务端。session cookie是指那些用来判断服务器端的session是否有效而存在的cookie。
        CURLOPT_CRLF	启用时将Unix的换行符转换成回车换行符。
        CURLOPT_DNS_USE_GLOBAL_CACHE	启用时会启用一个全局的DNS缓存，此项为线程安全的，并且默认启用。
        CURLOPT_FAILONERROR	显示HTTP状态码，默认行为是忽略编号小于等于400的HTTP信息。
        CURLOPT_FILETIME	启用时会尝试修改远程文档中的信息。结果信息会通过curl_getinfo()函数的CURLINFO_FILETIME选项返回。 curl_getinfo().
        CURLOPT_FOLLOWLOCATION	启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
        CURLOPT_FORBID_REUSE	在完成交互以后强迫断开连接，不能重用。
        CURLOPT_FRESH_CONNECT	强制获取一个新的连接，替代缓存中的连接。
        CURLOPT_FTP_USE_EPRT	启用时当FTP下载时，使用EPRT (或 LPRT)命令。设置为FALSE时禁用EPRT和LPRT，使用PORT命令 only.
        CURLOPT_FTP_USE_EPSV	启用时，在FTP传输过程中回复到PASV模式前首先尝试EPSV命令。设置为FALSE时禁用EPSV命令。
        CURLOPT_FTPAPPEND	启用时追加写入文件而不是覆盖它。
        CURLOPT_FTPASCII	CURLOPT_TRANSFERTEXT的别名。
        CURLOPT_FTPLISTONLY	启用时只列出FTP目录的名字。
        CURLOPT_HEADER	启用时会将头文件的信息作为数据流输出。
        CURLINFO_HEADER_OUT	启用时追踪句柄的请求字符串。	从 PHP 5.1.3 开始可用。CURLINFO_前缀是故意的(intentional)。
        CURLOPT_HTTPGET	启用时会设置HTTP的method为GET，因为GET是默认是，所以只在被修改的情况下使用。
        CURLOPT_HTTPPROXYTUNNEL	启用时会通过HTTP代理来传输。
        CURLOPT_MUTE	启用时将cURL函数中所有修改过的参数恢复默认值。
        CURLOPT_NETRC	在连接建立以后，访问~/.netrc文件获取用户名和密码信息连接远程站点。
        CURLOPT_NOBODY	启用时将不对HTML中的BODY部分进行输出。
        CURLOPT_NOPROGRESS  启用时关闭curl传输的进度条，此项的默认设置为启用。
            Note: PHP自动地设置这个选项为TRUE，这个选项仅仅应当在以调试为目的时被改变。
        CURLOPT_NOSIGNAL	启用时忽略所有的curl传递给php进行的信号。在SAPI多线程传输时此项被默认启用。	cURL 7.10时被加入。
        CURLOPT_POST	启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        CURLOPT_PUT	启用时允许HTTP发送文件，必须同时设置CURLOPT_INFILE和CURLOPT_INFILESIZE。
        CURLOPT_RETURNTRANSFER	将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        CURLOPT_SSL_VERIFYPEER	禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书使用CURLOPT_CAPATH选项设置证书目录 如果CURLOPT_SSL_VERIFYPEER(默认值为2)被启用，CURLOPT_SSL_VERIFYHOST需要被设置成TRUE否则设置为FALSE。	自cURL 7.10开始默认为TRUE。从cURL 7.10开始默认绑定安装。
        CURLOPT_TRANSFERTEXT	启用后对FTP传输使用ASCII模式。对于LDAP，它检索纯文本信息而非HTML。在Windows系统上，系统不会把STDOUT设置成binary模式。
        CURLOPT_UNRESTRICTED_AUTH	在使用CURLOPT_FOLLOWLOCATION产生的header中的多个locations中持续追加用户名和密码信息，即使域名已发生改变。
        CURLOPT_UPLOAD	启用后允许文件上传。
        CURLOPT_VERBOSE	启用时会汇报所有的信息，存放在STDERR或指定的CURLOPT_STDERR中。
     */
    public function setopt()
    {
        $resource = curl_init();
    }

    public function get()
    {
        $gets = $this->request->get();
        return json_encode(['status'=>1,'data'=>$gets]);

    }

    public function post()
    {
        $posts = $this->request->post();
        return json_encode(['status'=>1,'data'=>$posts]);
    }

    public function curlGet()
    {
        // 创建一个新cURL资源
        $ch = curl_init();

// 设置URL和相应的选项
        $url = $this->_url_get.'?'.http_build_query(['a'=>1,'b'=>2]);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
//       将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 连接超时（秒）
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 执行超时（秒）

// 抓取URL并把它传递给浏览器
        $res = curl_exec($ch);
        dump($res);

// 关闭cURL资源，并且释放系统资源
        curl_close($ch);
    }

    public function curlPost($header=0)
    {
//        dump(json_decode('{"flag":"error","message":"\u673a\u5668\u7c7b\u578b\u4e0d\u80fd\u4e3a\u7a7a","data":{}}'));
//        // 创建一个新cURL资源
        $ch = curl_init();
//
//// 设置URL和相应的选项
        $url = $this->_url_post;
        $arr = ['a'=>1,'b'=>2];
        $data = empty($header)?$arr:json_encode($arr);
        if(empty($header))
        {
            $data = $arr;
        }
        else
        {
            $headers = ["Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache"];
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $arr['header'] = 1;
           $data = json_encode($arr);


        }

        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//       将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 连接超时（秒）
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 执行超时（秒）
        curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS,$data );




// 抓取URL并把它传递给浏览器
        $res = curl_exec($ch);
        dump($res);
        echo '<BR>';
        $json = json_decode($res);
       dump($json);
       echo '<hr>';
//
//// 关闭cURL资源，并且释放系统资源
//        curl_close($ch);
//        $curlPost = json_encode(['a'=>1,'b'=>2]);
//        $url = $this->_url_post;
//        $curlPost = ['a'=>1,'b'=>2];
//
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_HEADER, false);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_NOBODY, true);
//        curl_setopt($curl, CURLOPT_POST, true);
//        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); // 连接超时（秒）
//        curl_setopt($curl, CURLOPT_TIMEOUT, 5); // 执行超时（秒）
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
//
//        $return_str = curl_exec($curl);
//        curl_close($curl);
//        dump($return_str);
////        return $return_str;


    }
}