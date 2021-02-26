<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/11/30
 * Time: 10:03
 */

namespace api\demo\controller;

class DataController extends ApiBaseController{


    //String


    public function getArr()
    {
        return  [
            ['name'=>'zhangsan','gender'=>1,'age'=>114],
            ['name'=>'lisi','gender'=>0,'age'=>84],
            ['name'=>'wangwu','gender'=>0,'age'=>18],
            ['name'=>'zhaoliu','gender'=>1,'age'=>24],
            ['name'=>'liqi','gender'=>1,'age'=>54],
            ['name'=>'qianba','gender'=>1,'age'=>24],
            ['name'=>'zengjiu','gender'=>2,'age'=>4],
            ['name'=>'wushi','gender'=>1,'age'=>34],
        ];
    }


    public function getArr2()
    {
        return  [
            ['face'=>18,'wuli'=>78,'zhili'=>86,'name'=>'zhangsan'],
            ['face'=>28,'wuli'=>45,'zhili'=>66,'name'=>'lisi'],
            ['face'=>48,'wuli'=>33,'zhili'=>46,'name'=>'zhaoliu'],
            ['face'=>68,'wuli'=>30,'zhili'=>55,'name'=>'wangwu'],
            ['face'=>58,'wuli'=>70,'zhili'=>23,'name'=>'qianba'],
            ['face'=>32,'wuli'=>50,'zhili'=>12,'name'=>'wushi'],

        ];
    }

    //array
    public function filter()
    {
        $arr = $this->getArr() ;

       $arr = array_filter($arr,function ($item){
            return $item['age']>14;
        });
       dump($arr);
    }

    public function sort()
    {
        $arr = $this->getArr() ;
        sort($arr);
        dump($arr);
    }

    public function sort2()
    {
        $arr = $this->getArr() ;

        usort($arr,function ($item1,$item2){
           return strlen($item2['name']) - strlen($item1['name']);
        });
        dump($arr);
    }

    public function sort3()
    {
        $arr = $this->getArr() ;

//        $tmp = [];
//        foreach ($arr as $key=>$val)
//        {
//            $tmp[$key] = $val['age'];
//        }

        $tmp = array_column($arr,'age');
        array_multisort($tmp,SORT_ASC,$arr);
        dump($tmp);
        dump($arr);
    }


    public function map()
    {
        $arr = $this->getArr2();
        $arr2 =    array_map(function ($item){
            arsort($item);
            return $item;
        },$arr);
    dump($arr2);
    }
}