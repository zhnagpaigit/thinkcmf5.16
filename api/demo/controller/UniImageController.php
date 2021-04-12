<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/3/8
 * Time: 10:50
 * uni-app uni-image 接口文档
 */

namespace api\demo\controller;

use api\user\controller\UploadController;
use cmf\lib\Upload;
use think\App;
use think\Cache;
use think\Db;


class UniImageController extends ApiBaseController
{
    public $_db;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $config = Config()['config']['DB_UNIIMAGE'];
        $this->_db = DB::connect($config);
    }

    public function reg()
    {
        $config = Config()['config']['DB_UNIIMAGE'];
        $db = DB::connect($config);


        $request = $this->request;
        $ip = get_client_ip();

        $password = $request->param('password');
        $account = $request->param('account');
        $confirm = $request->param('confirm');


        if(empty($password ) || empty($account) || empty($confirm))
        {
            return $this->error('账号 和 密码 不能为空！');
        }

        if( $password != $confirm)
        {
            return $this->error('两次密码不一致');
        }



        $find = $db->name('Member')->where(['account'=>$account,'status'=>1])->find();
        if(!empty($find))
        {
            $this->error('此账号已存在！');
        }


        $res = $db->name('Member')->insertGetId(['account'=>$account,'password'=>$password,'icon'=>'/demo/picture/website/static/logo.png','create_time'=>time()]);

        if($res)
        {
            return $this->success('添加成功！',['a'=>1,'b'=>2]);
        }
        else
        {
            return $this->error('添加失败！');
        }


    }

    public function login()
    {
        $config = Config()['config']['DB_UNIIMAGE'];
        $db = DB::connect($config);

        $request = $this->request;
        $ip = get_client_ip();

        $password = $request->param('password');
        $account = $request->param('account');
        $res = $db->name('Member')->where(['account'=>$account,'password'=>$password,'status'=>1])->find();

        if($res)
        {

            // 写缓存：
            return $this->success('登陆成功！',$res);
        }
        else
        {
            return $this->error('登陆失败！'.$db->getLastSql());
        }

    }



    public function test()
    {
        $dirname = 'Upload/api/20210309';
        if (!is_dir($dirname)) {
            # 不存在该目录，创建之
            mkdir($dirname,0777,true);
        }


        return;
    }
    //-- 上传单张图片
    public function uploadImg($filename='file')
    {

//        $upload = new \api\demo\vendor\Upload();
      //  $file = new File();
       $upload = new Upload();
//      $res =   $upload->upload();

        $request = $this->request;
        $file = $_FILES[$filename];
//        $file = $_FILES;


       // dump($request->file());
       // $file = $request->file('file');

      $res = $upload->up($file);

        $this->success('上传图片',['res'=>$res]);
    }


}