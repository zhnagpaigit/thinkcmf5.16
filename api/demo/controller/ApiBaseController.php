<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace Api\demo\controller;

use app\common\model\MongoModel;
use cmf\controller\RestBaseController;
use think\App;
use think\Db;

class ApiBaseController extends RestBaseController
{
    public $_db;
    public $_mongo_db;

    public function __construct(App $app = null)
    {
        parent::__construct($app);


        $config = config();
        $data = $config['config']["DB_MYSQL_LOCAL"];
        $this->_db = Db::connect($data);

      //  $data = $config['config']["DB_MONGO"];
      //  $this->_mongo_db =  new MongoModel($data);
    }

    public function index()
    {
        $this->success('请求成功!', ['test'=>'test']);
    }
    public function test()
    {
        $this->error('请求失败!', ['test'=>'test']);
    }
}
