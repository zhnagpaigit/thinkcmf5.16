<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/28
 * Time: 11:11
 */
namespace app\common\model;

class MongoModel
{
    private $_manager;
    private $_host;
    private $_username;
    private $_password;
    private $_db;

    public function __construct($param = array()){
        if(!empty($param['hostname'])){
            $this->_host = $param['hostname'] ? $param['hostname'] : '';
            $this->_username = $param['username'] ? $param['username'] . ':' : '';
            $this->_password = $param['password'] ? $param['password'] : '';
            $this->_db = $param['database'] ? $param['database'] : '';
            $mongo = "mongodb://" . $this->_username . $this->_password . '@' . $this->_host . '/';
           echo $mongo;
        }else{
//            $mongo ="mongodb://localhost:27017";
            // 公网
                 $mongo ="mongodb://root:Ali_Mg_2020_!#$*)(233@dds-uf62dea4453453842606-pub.mongodb.rds.aliyuncs.com:3717/admin?replicaSet=mgset-36315791";
            //内网
           // $mongo = "mongodb://root:Ali_Mg_2020_!#$*)(233@dds-uf62dea4453453841.mongodb.rds.aliyuncs.com:3717,dds-uf62dea4453453842.mongodb.rds.aliyuncs.com:3717/admin?replicaSet=mgset-36315791";
            // mongodb://pyiotuser:txpyiotuser@39.108.212.120:27017/pyiot
            //   $mongo ="mongodb://pyiotuser:txpyiotuser@localhost:27017";
            $this->_db = "pyiot";
        }

       //echo $mongo;
        $this->_manager = new \MongoDB\Driver\Manager($mongo);
    }

    public function getInstense(){
        return $this->_manager;
    }
    public function getDB(){
        return $this->_db;
    }
    public function getBulk(){
        return  new \MongoDB\Driver\BulkWrite;
    }
    public function getWriteConcern(){
        new \MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
    }


    /**
     * 插入数据
     * @param $db 数据库名
     * @param $collection 集合名
     * @param $document 数据 json格式
     * @return
     */
    public function insert($collection, $document){
        $bulk = $this->getBulk();
        $write_concern = $this->getWriteConcern();

        $document = json_decode($document);
        if( count($document) == 1 ){
            $document['_id'] = new \MongoDB\BSON\ObjectID;
            $bulk->insert($document);
        }else{
            foreach ($document as $val){
                $val['_id'] = new \MongoDB\BSON\ObjectID;
                $bulk->insert($val);
            }
        }

        $this->_manager->executeBulkWrite($this->_db.$collection, $bulk);

        return $this->_manager->executeBulkWrite($this->_db. '.' .$collection, $bulk, $write_concern);
    }

    /**
     * 删除数据
     * @param array $where
     * @param array $option
     * @param string $db
     * @param string $collection
     * @return mixed
     */
    public function delete($collection, $where = array(), $option = array()){
        $bulk = $this->getBulk();
        $bulk->delete($where, $option);
        return $this->_manager->executeBulkWrite($this->_db. '.' .$collection, $bulk);
    }

    /**
     * 更新数据
     * @param array $where 类似where条件
     * @param array $field  要更新的字段
     * @param bool $upsert 如果不存在是否插入，默认为false不插入
     * @param bool $multi 是否更新全量，默认为false
     * @param string $db   数据库
     * @param string $collection 集合
     * @return mixed
     */
    public function update($collection, $where = array(), $field = array(), $upsert = false, $multi = false){
        if(empty($where)){
            return 'filter is null';
        }
        if(isset($where['_id'])){
            $where['_id'] = new \MongoDB\BSON\ObjectId($where['_id']);
        }
        $bulk = $this->getBulk();
        $write_concern = $this->getWriteConcern();
        $bulk->update($where, $field, $upsert, $multi);
        $res = $this->_manager->executeBulkWrite($this->_db. '.' .$collection, $bulk, $write_concern);
        if(empty($res->getWriteErrors())){
            return true;
        }else{
            return false;
        }
    }


    public function selectById($collection, $id, $options = array()){
        $filter = ['_id' => new \MongoDB\BSON\ObjectID($id)];
        $res = $this->query($collection, $filter, $options);
        foreach ($res as $item) {
            $data = $this->objToArray($item);
        }
        return $data;
    }

    public function query($collection, $filter,$options =[],$page=0,$pageLen=50){

//dump($options);

        if($page>0)
        {
            $options['limit']=$pageLen;
            $options['skip']=($page-1)*$pageLen;

        }

        $query = new \MongoDB\Driver\Query($filter, $options);
        $res = $this->_manager->executeQuery($this->_db. '.' .$collection, $query);
        $data = array();

        foreach ($res as $item){
            $tmp = $this->objToArray($item);
            // $tmp['_id'] = $tmp['_id']['$oid'];
            $data[] = $tmp;
//            dump($tmp);
        }
        return $data;
    }

    /**
     * 执行MongoDB命令
     * @param array $param
     * @return \MongoDB\Driver\Cursor
     */
    public function command(array $param)
    {
        $cmd = new \MongoDB\Driver\Command($param);
        return $this->_manager->executeCommand($this->_db, $cmd);
    }

    /**
     * 按条件计算个数
     *
     * @param string $collName 集合名
     * @param array $where 条件
     * @return int
     */
    public function count($collName,  $where)
    {
        $result = 0;
        $cmd = [
            'count' => $collName,
            'query' => $where
        ];
        $arr = $this->command($cmd)->toArray();



       // dump($where);
        if (!empty($arr)) {
            $result = $arr[0]->n;
        }
        return $result;
    }


    /**
     * 聚合查询
     * @param $collName
     * @param array $where
     * @param array $group
     * @return \MongoDB\Driver\Cursor
     */
    function aggregate($collName, array $where=[], array $group,$sort=["_id"=>1])
    {

        $pipeline =[
            ['$group' => $group],
            ['$sort'=>$sort]
        ];
        if(!empty($where))
        {
            $pipeline =[
                ['$match' => $where],
                ['$group' => $group],
                ['$sort'=>$sort]
            ];
        }

        $cmd = [
            'aggregate' => $collName,
            'pipeline' => $pipeline,
            'cursor' => new \stdClass  ,
        ];

        $cursor = $this->command($cmd);

        $result =[];
        foreach ($cursor as $document) {
            $result[] = $document;
        }
        return $result;
    }


    /**
     * 聚合查询
     * @param $collName
     * @param array $where
     * @param array $group
     * @param array $project:显示字段
     * @param array $sort:排序
     * @return \MongoDB\Driver\Cursor
     */

    public function aggregate2($collName,$where,$group,$sort=["_id"=>1])
    {
        $cmd = [
            'aggregate' => $collName,
            'pipeline' => [
                ['$match' => $where],
                ['$group' => $group],

                ['$sort'=>$sort]

            ],
            'cursor' => new \stdClass  ,
        ];

        $cursor = $this->command($cmd);

        $result =[];
        foreach ($cursor as $document) {
            $result[] = $document;
        }
        return $result;
    }

    /**
     * 同mysql中的distinct功能
     *
     * @param string $collName collection名
     * @param string $key 要进行distinct的字段名
     * @param array $where 条件
     * @return array
     * Array
     * (
     * [0] => 1.0
     * [1] => 1.1
     * )
     */
    function distinct($collName, $key, array $where)
    {
        $result = [];
        $cmd = [
            'distinct' => $collName,
            'key' => $key,
            'query' => $where
        ];
        $arr = $this->command($cmd)->toArray();
        if (!empty($arr)) {
            $result = $arr[0]->values;
        }
        return $result;
    }

    public function objToArray($data){
        return json_decode(json_encode($data),true);
    }










}
