<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\demo\controller;

use cmf\controller\RestBaseController;

class HomeController extends ApiBaseController
{
    public function index()
    {
        $this->success('请求成功!', ['test'=>'test']);
    }
    public function test()
    {

        $db = $this->_db;
        $list = $db->name('File')->limit(10)->select();
//        dump($list);
        $this->error('请求失败!', ['test'=>'test']);
    }

    //取图片列表

    public function getImages($limit = 20)
    {
        $db = $this->_db;
        $list = $db->name('file')->field('id,abs_url,create_time')->limit($limit)->order('id desc')->select();
        $this->success('取图片列表',$list);
    }
}
