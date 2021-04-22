<?php
namespace api\demo\controller;

use cmf\controller\RestBaseController;
use think\Db;

class ItoiletController extends RestBaseController
{
    public function index()
    {
    $this->success('请求成功!', ['test'=>'test']);
    }
    public function test()
    {
    $this->error('请求失败!', ['test'=>'test']);
    }
    public function show()
    {
        $db = Db::connect(config()['config'] ["DB_SHOW"]);
        $row = $db->name('Data')->find();
        echo json_encode($row);
    }
}