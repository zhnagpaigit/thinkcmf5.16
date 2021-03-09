<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/2/26
 * Time: 14:59
 */
namespace app\demo\controller;


use app\demo\vendor\PageClass;
use cmf\controller\HomeBaseController;
use page\Page;
use page\Pageturn;
use tree\Tree;


class TestController extends HomeBaseController
{
    public function test()
    {
        echo 'test';

    }
    public function game1()
    {
        $this->assign('title','游戏一');
        return $this->fetch();

    }

    public function ws()
    {
        return $this->fetch(':ws');
    }

    public function index()
    {

        $page = new \app\demo\vendor\Page();
        $page->args = ['a'=>1,'b'=>2];
        $page->totalPage = $this->request->param('totalPage',10,'int');
        $page->page = $this->request->param('page',1,'int');
        $page->url='http://www.baidu.com';
        dump($page);
       $res =  $page->show();
       //echo $res;
       dump($res);
    }
}
