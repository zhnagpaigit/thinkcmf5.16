<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/2/26
 * Time: 14:59
 */
namespace app\demo\controller;

use cmf\controller\HomeBaseController;

class FlexController extends HomeBaseController
{

//    public function direction()
//    {
//      return $this->fetch();
//    }

    public function _empty()
    {
       $action = $this->request->action();
        $this->assign('template',$action);
        return $this->fetch();
    }



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
}
