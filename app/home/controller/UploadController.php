<?php
/**
//// * Created by PhpStorm.
//// * User: zw
//// * Date: 2021/3/18
//// * Time: 11:56
//// */
namespace app\home\controller;

use api\demo\vendor\Page;
use app\common\model\MongoModel;
use cmf\controller\MyBaseController;
use think\cache\driver\Redis;
use think\Db;
use think\File;
use think\Request;
use zdy\Upload;

//
class UploadController extends MyBaseController
{
////    public $_db;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }
////
    public function test()
    {
        $file1 = 'D:/wamp/www/ThinkCMF/public/Upload/default/20210319/20210319170340605468ec3e623.jpg';
      ;
        echo sha1_file( $file1),'->1<BR>';
        echo md5_file($file1),'->2<BR>';

        $file2 = 'D:/wamp/www/ThinkCMF/public/Upload/default/20210319/2021031917113060546ac2d9c15.jpg';

        echo sha1_file($file2),'->1<BR>';
        echo md5_file($file2),'->2<BR>';
    }
        public function _empty()
        {
          ;
           return $this->fetch('/404');
        }
//
////    public function index()
////    {
////        return $this->fetch(':ws');
////    }
////

    public function img()
    {
        $this->assign('title','php单图上传');
//        $this->view->engine->layout(true);
        return $this->fetch();
    }
    public function imgVue()
    {
        $this->assign('title','php单图上传');
//        $this->view->engine->layout(true);
        return $this->fetch();
    }


    public function imgFormSignal()
    {
//        dump($this->request->param());
        $file = $this->request->file('file');



        $upload = new  \app\home\vendor\Uploads();


//        dump($upload);
//           $upload->upload();
      echo  $res =   $upload->up($file);

        return json_encode($file);


    }
    public function imgFormSignal2()
    {

        $file = $this->request->file('file')->getInfo();

        $upload = new \cmf\lib\Upload();
        $upload->upload($file);
        return json_encode($file);


    }


    // -- 多图
    public function imgFormMulti()
    {

        $files = $this->request->file();

        dump($files);


        $upload = new  \app\home\vendor\Uploads($files,'multi');

        $res =   $upload->ups();
        dump($res);

    }

}
