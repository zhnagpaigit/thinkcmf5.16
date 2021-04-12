<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/3/8
 * Time: 15:40
 * 图片上传类
 *
 *
 */
namespace app\home\vendor;
class Uploads
{
    private $path = "default";   //文件上传目录
    private $max_size; //上传文件大小限制
    private $errno;  //错误信息号
    private $mime = array('image/jpeg', 'image/png', 'image/gif');//允许上传的文件类型
    private $dir ;
    private $host;
    private $file;

    /**
     * 构造函数,
     * @access public
     * @param $path string 上传的路径
     */
    public function __construct($file='',$path='default')
    {

        $this->max_size = 1000000;
        $this->dir = $_SERVER['DOCUMENT_ROOT'];
        $this->host = $_SERVER['HTTP_HOST'];
        if(!empty($file))
            $this->file= $file;

        $this->path = $path;

    }

    public function setPath($path)
    {
        $this->path = $path;
    }




    /**
     * 文件上传的方法，分目录存放文件
     * @access public
     * @param $file array 包含上传文件信息的数组
     * @return mixed 成功返回上传的文件名，失败返回false
     */
    public function up($file='')
    {
        if(empty($file)) $file = $this->file;
        $file = $file->getInfo();

        //判断是否从浏览器端成功上传到服务器端
        if ($file['error'] == 0) {


            # 上传到临时文件夹成功,对临时文件进行处理
            //上传类型判断
            if (!in_array($file['type'], $this->mime)) {
                # 类型不对
                $this->errno = -1;
                return false;
            }

            //判断文件大小
            if ($file['size'] > $this->max_size) {
                # 大小超出配置文件的中的上传限制
                $this->errno = -2;
                return false;
            }

            $this->path .='/'.date('Y-m-d');




            //获取存放上传文件的目录
            if (!is_dir($this->path)) {
                # 不存在该目录，创建之
                mkdir($this->path,0777,true);
            }

            //文件重命名,由当前日期 + 随机数 + 后缀名
            $file_name = date('YmdHis') . uniqid() . strrchr($file['name'], '.');



            //准备就绪了，开始上传
            if (move_uploaded_file($file['tmp_name'], $this->path . '/' . $file_name)) {
                # 移动成功

                /*
                 * 可 加入 md5_file(),sha1_file() 来判断是否已存在图片
                 * 如果已存在，取原来图片地址，并删除刚上传的文件
                 *   @unlink($strSaveFilePath); // 删除已经上传的文件
                 */
                return $this->host.'/'. $this->path . '/' . $file_name;
            } else {
                # 移动失败
                $this->errno = -3;
                return false;
            }



        } else {
            # 上传到临时文件夹失败，根据其错误号设置错误号
            $this->errno = $file['error'];
            return false;
        }

    }

    /**
     * 多文件上传方法
     * @access public
     * @param $file array 包含上传文件信息的数组，是一个二维数组
     * @return array 成功返回上传的文件名构成的数组, ?如果有失败的则不太好处理了
     */
    public function ups($files='')
    {
        if(empty($file)) $files = $this->file;
        $files = $file->getInfo();
        dump($files);
        //在多文件上传时，上传文件信息 又是一个多维数组，如$_FILES['userfile']['name'][0]，$_FILES['userfile']['name'][1]
        //我们只需要遍历该数组，得到每个上传文件的信息，依次调用up方法即可
        foreach ($files['name'] as $key => $value) {
            # code...
            $file['name'] = $files['name'][$key];
            $file['type'] = $files['type'][$key];
            $file['tmp_name'] = $files['tmp_name'][$key];
            $file['error'] = $files['error'][$key];
            $file['size'] = $files['size'][$key];
            //调用up方法，完成上传
            $filename[] = $this->up($file);
        }
        return $filename;
    }

    /**
     * 获取错误信息,根据错误号获取相应的错误提示
     * @access public
     * @return string 返回错误信息
     */
    public function error()
    {
        switch ($this->errno) {
            case -1:
                return '请检查你的文件类型，目前支持的类型有' . implode(',', $this->mime);
                break;
            case -2:
                return '文件超出系统规定的大小，最大不能超过' . $this->max_size;
                break;
            case -3:
                return '文件移动失败';
                break;
            case 1:
                return '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值,其大小为' . ini_get('upload_max_filesize');
                break;
            case 2:
                return '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值,其大小为' . $_POST['MAX_FILE_SIZE'];
                break;
            case 3:
                return '文件只有部分被上传';
                break;
            case 4:
                return '没有文件被上传';
                break;
            case 5:
                return '非法上传';
                break;
            case 6:
                return '找不到临时文件夹';
                break;
            case 7:
                return '文件写入临时文件夹失败';
                break;
            default:
                return '未知错误,灵异事件';
                break;
        }
    }
}
