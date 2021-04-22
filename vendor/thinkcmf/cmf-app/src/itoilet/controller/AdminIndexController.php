<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/4/12
 * Time: 14:49
 * 智能马桶
 */
namespace app\itoilet\controller;

use think\Db;
use cmf\controller\AdminBaseController;

class AdminIndexController extends AdminBaseController
{
    public function _empty()
    {
//        dump($this);
//        $view = $this->view;
//        dump($view);
        $config_template = config();
        $viewBase = config('template.view_base');
        $viewSuffix = config('template.view_suffix');
      //  dump($this->request);
        $pathinfo = $this->request->pathinfo();

        $tpl = $viewBase.$pathinfo.'.'.$viewSuffix;

        $tpl = str_replace('\\','/',$tpl);
        if(is_file($tpl))
        {
            return $this->fetch();
        }
        else
        {
            echo 'hehe';
        }
        
    }
    public function show()
    {
       return $this->fetch();
    }


    //-- 产品录入
    public function record()
    {
        dump($this->request->param());
    }
}