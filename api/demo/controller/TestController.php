<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/11/21
 * Time: 9:03
 */
namespace api\demo\controller;

use api\demo\model\YipayModel;
use cmf\controller\RestBaseController;
use think\App;
use think\Db;

class TestController extends RestBaseController
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    public function test()
    {
        echo 'hhh';
    }

    //生成门店月统计表
    public function createShopNumber()
    {
        $num =500;

        for($i=0;$i<20;$i++)
        {
            $insert =[];
            for($j=1;$j<=$num;$j++)
            {
                $data = $i*$num +$j;

                $str = str_pad($data,6,0,STR_PAD_LEFT);
                $str = 'yj_'.$str;
                $insert[] =['shop_id'=>$str,'num'=>0,'update_time'=>0];
            }


          Db::name('YipayShopNumber')->insertAll($insert);
        }
    }

    //选取门店号
    public function selectShop()
    {
        $model = new YipayModel();
        //-- 订单编号：
        $order_sn = time().rand(1000,9999);
        $create_time = time();
        $rand = rand();
        $goods_id = $rand%1000000;
        $params =['goods_id'=>$goods_id,
            'goods_number'=>$goods_id,
            'order_sn'=>$order_sn,
            'create_time'=>$create_time,
            'is_pay'=>1
        ];

            echo "<hr/>";
            echo "<a href='".url("test/showCount")."' target='_blank'>查看统计数据</a> | ";
            echo "<a href='".url("test/showOrder")."' target='_blank'>查看订单数据</a> ";



        $model->selectShop($params);
    }



    //模拟订单提交，每次 1000笔

    public function addOrders()
    {
        echo "模拟订单提交，每次 500笔<br/>";
        echo "<a href='".url("test/showCount")."' target='_blank'>查看统计数据</a> | ";
        echo "<a href='".url("test/showOrder")."' target='_blank'>查看订单数据</a>  <hr/>";
        $num = 2000;
        $model = new YipayModel();
        $time = time();
        for($i=0;$i<$num;$i++)
        {
            $order_sn = $time.str_pad($i,4,'0',STR_PAD_LEFT);
            $create_time = $time;
            $rand = $time;
            $goods_id = $rand%1000000;
            $params =['goods_id'=>$goods_id,
                'goods_number'=>$goods_id,
                'order_sn'=>$order_sn,
                'create_time'=>$create_time,
                'is_pay'=>1
            ];
            $model->selectShop($params);
        }

    }



    //页面展示

    //-- 统计数据
    public function showCount()
    {
        echo '当月门店号使用数据<hr/>';
        $currMonth = date("Y-m-01");
        $monthTime = strtotime($currMonth);
        $list = Db::name('YipayShopNumber')
            ->field('shop_id,num,update_time')
            ->where('update_time', 'egt', $monthTime)
            ->where('num', 'between', [1,33])
            ->order('num desc')
            ->select();
        $list = $list->toArray();
        echo Db::name('YipayShopNumber')->getLastSql();
        echo count($list);
        echo "<table> <tr><th>门店号</th><th>使用次数</th><th>最新使用时间</th></tr>";
        foreach ($list as $val)
        {
            $date = date('Y-m-d H:i',$val['update_time']);
            echo "<tr><td>{$val['shop_id']}</td><td  align='center'>{$val['num']}</td><td>{$date}</td></tr>";
        }
        echo "</table>";
    }

    //-- 订单数据
    public function showOrder()
    {
        echo '当月翼支付门店订单数据<hr/>';
        $currMonth = date("Y-m-01");
        $monthTime = strtotime($currMonth);
        $list = Db::name('YipayShopOrder')
            ->field('shop_id,order_sn,goods_id,create_time')
            ->where('create_time', 'egt', $monthTime)
            ->order('id desc')
            ->select();
        $list = $list->toArray();
        echo "<table> <tr><th>门店号</th><th>订单编号</th><th>设备ID</th><th>使用时间</th></tr>";
        foreach ($list as $val)
        {
            $date = date('Y-m-d H:i',$val['create_time']);
            echo "<tr><td>{$val['shop_id']}</td><td  align='center'>{$val['order_sn']}</td><td  align='center'>{$val['goods_id']}</td><td>{$date}</td></tr>";
        }
        echo "</table>";
    }

}