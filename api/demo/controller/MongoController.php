<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/3/10
 * Time: 13:56
 */
namespace api\demo\controller;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;
use think\App;

class MongoController extends ApiBaseController
{
    public $_db;
    public $_mongo;
    public $_manager;
    public $_bulk;
    public $_writeConcern;
//    public $_isoTime;
//    public $_query;
//    public $_cursor;
//    public $_writeResult;
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        /*
         * 对象
         *  MongoDB\Driver\BulkWrite	收集要发送到服务器的一个或多个写操作
         *  MongoDB\Driver\Query	构造查询对象
         *  MongoDB\Driver\Cursor	封装MongoDB命令或查询的结果
         *  MongoDB\Driver\WriteResult	封装执行结果
         *  MongoDB\Driver\WriteConcern 描述在向单独的mongod服务器、集群或者分片请求写数据库时的确认程度。在分片中，mongos实例会把write concern传递给分片。
         *  \MongoDB\Driver\Command  主要用于执行命令
         *
         * 方法：
         *  BulkWrite->insert(array1); BulkWrite->insert(array2);BulkWrite->insert(array3);  ... //插入数据
         *  $result = $manager->executeBulkWrite('db.product', BulkWrite);//执行插入
         *
         *  $query = new MongoDB\Driver\Query($filter, $options);
         *  $manager->executeQuery('db.product', $query); //执行查询
         *
         *  $bulk = new MongoDB\Driver\BulkWrite();
         *  $bulk->update(array('product_id' => 123), array('$set' => array('product_price' => 1999.99)),...);//更新
         *  $bulk->delete(array('product_id' => 125));//--删除
         *
         */

        $this->_db = 'pyiot'; //数据库
       // $this->_mongo ="mongodb://root:Ali_Mg_2020_!#$*)(233@dds-uf62dea4453453842606-pub.mongodb.rds.aliyuncs.com:3717/admin?replicaSet=mgset-36315791";
        $this->_mongo ="mongodb://localhost:27017";
        $this->_manager = new \MongoDB\Driver\Manager($this->_mongo);// 入口类，负责维护与MongoDB的连接，执行读写和命令
        $this->_bulk = new BulkWrite();//插入数据
        $this->_writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);



    }

    //增  -- 时间格式 怎么 插入和更新？
    public function insert()
    {
        $manager = $this->_manager;
        $bulk = $this->_bulk;
        $writeConcern = $this->_writeConcern;  // -- $writeConcern 有什么作用？
        $utdTime = new UTCDateTime(time()*1000);
//        dump($utdTime);
//        return;
//        $now = time();
//        $nowDate = date('Y-m-d H:i:s');
    //    $isoDate = new Simple
        $bulk->insert(['name'=>'zhangsan','age'=>40,'address'=>'hb','time'=>time(),'isoTime'=>date('Y-m-d H:i:s'),'utdTime'=>$utdTime]);
        $bulk->insert(['name'=>'lisi','age'=>20,'address'=>'hb','time'=>time(),'isoTime'=>date('Y-m-d H:i:s')]);
        $bulk->insert(['name'=>'wangwu','age'=>31,'address'=>'hb','time'=>time(),'isoTime'=>date('Y-m-d H:i:s')]);

        $result =  $manager->executeBulkWrite('test.sites', $bulk,$writeConcern)->getInsertedCount();
        dump($result);
    }

    // 删 -- 如何按ID 删除
    public function delete()
    {
        $manager = $this->_manager;
        $bulk = $this->_bulk;
        $writeConcern = $this->_writeConcern;


        $bulk->delete(['age'=>31,'_id'=>new ObjectId("60488f38d3680000ae001450")]);
        $bulk->delete(['name'=>'lisi','_id'=> new ObjectId("60488f38d3680000ae00144f")],['limit'=>1]);//0:默认，删除所有数据，1 删除第一条数据
        $result =  $manager->executeBulkWrite('test.sites', $bulk,$writeConcern)->getDeletedCount();
        dump($result);
    }

    //改
    public function update()
    {
        $manager = $this->_manager;
        $bulk = $this->_bulk;
        $writeConcern = $this->_writeConcern;
        $bulk->update(
            ['age' =>65],//更新条件
            ['$set' => ['age' => 20, 'name' => '返老还童']],//更新字段
            ['multi' => true, 'upsert' => false] // multi:是否指更新（false 只更新第一个），upsert 为true 时，不存在，就新增数据
        );
        $result =  $manager->executeBulkWrite('test.sites', $bulk,$writeConcern)->getModifiedCount();
        dump($result);
    }

    //查
    public function search()
    {
        $manager = $this->_manager;
        $filter = ['age' => ['$gt' => 1]]; //查询条件
        $options = [
            'projection' => ['_id' => 0], // 查询字段：默认全显示，0 不显示
            'sort' => ['time' => -1], //排序
            'limit'=>5
        ];
        $query = new \MongoDB\Driver\Query($filter, $options);
        $res = $manager->executeQuery('test.sites', $query);


//        $res = iterator_to_array($res);
        $res = $res->toArray();
        $item = (string) $res[0]->utdTime/1000;
        dump($item);
        echo date('Y-m-d H:i:s',$item);
    }

    //排序
    public function order()
    {

    }

    //条件
    public function where()
    {

    }

    //条数
    public function count()
    {



        $cmd = [
            'count' => 'sites', //文件名
            'query'=>['name'=>'zhangsan']//查询条件
        ];
     //   $arr = $this->command($cmd)->toArray();

        $cmd = new \MongoDB\Driver\Command($cmd);
        $res = $this->_manager->executeCommand('test', $cmd);
        $arr = $res->toArray();
        dump($arr[0]->n);
    }

    //聚合
    public function collect()
    {

       // $group =['_id'=>['name'=>'$name'], 'count'=>['$sum'=>1]];
        $group =['_id'=>['name'=>'$name'], 'sum'=>['$sum'=>'$age'],'count'=>['$sum'=>1]];
        $pipeline =[
//            ['$match' => []],
            ['$match' => ['age'=> ['$gt' => 20]]],
            ['$group' => $group],
            ['$sort'=>['_id'=>1]]
        ];
        $cmd = [
            'aggregate' => 'sites',
            'pipeline' => $pipeline,
            'cursor' => new \stdClass  ,

        ];

        $cmd = new \MongoDB\Driver\Command($cmd);
        $res = $this->_manager->executeCommand('test', $cmd);
        $arr = $res->toArray();

        dump($arr);
    }

    // 取字段唯一值
    public function distinct()
    {
        $key = 'age';

        $result = [];
        $cmd = [
            'distinct' => 'sites',
            'key' => $key,
            'query' => ['age'=>['&gt',20]]
        ];
        $cmd = new \MongoDB\Driver\Command($cmd);
        $res = $this->_manager->executeCommand('test', $cmd);


        dump($res->toArray());
    }



    // -- command 增 删 改 查
    public function cAdd()
    {
        $manager = $this->_manager;
        $utdTime = new UTCDateTime(time()*1000);
        $cmd = ['insert'=>'sites',
            'documents'=>[
                ['name'=>'lisiyu','age'=>22,'address'=>'hrb','time'=>time(),'isoTime'=>date('Y-m-d H:i:s'),'utdTime'=>$utdTime],
                ['name'=>'wangji','age'=>24,'address'=>'gd','time'=>time(),'isoTime'=>date('Y-m-d H:i:s'),'utdTime'=>$utdTime],
                ['name'=>'chengsiwu','age'=>32,'address'=>'sh','time'=>time(),'isoTime'=>date('Y-m-d H:i:s'),'utdTime'=>$utdTime]
            ],
            'ordered'=>true
            ];
        $cmd = new \MongoDB\Driver\Command($cmd);
        $res = $manager->executeCommand('test', $cmd);


        dump($res->toArray());
    }
    public function cDel()
    {
        $manager = $this->_manager;
        $q = new \stdClass();
     //   $q->address='hrb';

        $cmd = [
            'delete'=>'sites',
            'deletes'=>[
//                ['q'=> ['name'=>'lisi'],'limit'=>1]//limit:1:只删除一条，0：删除所有
                ['q'=> $q,'limit'=>0]//limit:1:只删除一条，0：删除所有
            ]

        ];
        $cmd = new \MongoDB\Driver\Command($cmd);
        $res = $manager->executeCommand('test', $cmd);


        dump($res->toArray());
    }




    // 改---
    /*
     * 不太好用。
     * multi=>true,会报错 multi update only works with $ operators
     * multi->false,没赋值的字段都弄没掉。
     */
    public function cUpdate()
    {
        $manager = $this->_manager;
        $cmd = [
            'update'=>'sites',
            'updates'=>[
                ['q'=>['time'=>['$gt'=>1]],'u'=>['name'=>'lisiyu','isoTime'=>date('Y-m-d H:i:s')],'upsert'=>false,'multi'=>false],
//               ['q'=>['name'=>'wangji'],'u'=>['time'=>time(),'isoTime'=>date('Y-m-d H:i:is')],'upsert'=>false,'multi'=>true]
            ],
            'ordered' => true, // 是否依次执行updates语句，true表示执行失败后继续后面的语句，false 表示一旦失败立即返回

        ];
        $cmd = new \MongoDB\Driver\Command($cmd);
        $res = $manager->executeCommand('test', $cmd);


        dump($res->toArray());
    }

}
