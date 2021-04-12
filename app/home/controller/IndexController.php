<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Released under the MIT License.
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------

namespace app\home\controller;

use app\common\model\MongoModel;
use cmf\controller\HomeBaseController;
use think\cache\driver\Redis;
use think\Db;

class IndexController extends HomeBaseController
{
    public function index()
    {

        return $this->fetch();
    }

    public function ws()
    {
//        dump($this->request);
        return $this->fetch(':ws');
    }

    public function upload()
    {

        $request = $this->request;
       $file = $request->file('file');

       dump($file);
       if(!empty($file))
       {
           $info = $file->getInfo();
           dump($info);
       }

       echo WEB_ROOT;
        return $this->fetch();
    }

    public function ajax()
    {
        return json_encode(['status'=>1,'message'=>'xxxx']);
    }


    public function test()
    {
//        phpinfo();
        //  PartnerCode(平台编码) 3516
        // ServiceCode(业务编码) 	zic3l7
        //Password(接口密码)： 3516zic3l7

//        $partnercode = '3516';
//        $servicecode = "zic3l7";
//        $requesttime = date('YmdHis').'700111';
//      //  $key = '89860407111840309778';
//        $key= "1440072699778";
//        $password = "3516zic3l7";
//        $sign = Md5($password.$requesttime.$key);
//       dump([$partnercode,$servicecode,$requesttime,$sign,$key]);



        $config = config();


        $data = $config['config']["DB_MYSQL_LOCAL"];
        $db = Db::connect($data);
        $list =  $db->name('file')->limit(10)->select();

        dump($list);
        $data = $config['config']["DB_MONGO"];

        $mongo = new MongoModel($data);

        $options =  [
            'projection' => ['_id'=>0,'device_id'=>1,'work_status.process'=>1,'work_status.prog'=>1]
        ];
        $where =['online.online'=>true,'work_status.status'=>'Run','work_status.fault'=>""];
        $mongoList = $mongo->query("device_info",$where  ,$options);

        dump($mongoList);

        $list = Db::name('Hook')->select();
        dump($list);
//        phpinfo();
            $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        echo "Connection to server successfully";
        //查看服务是否运行
        echo "Server is running: " . $redis->ping();


    }


}
