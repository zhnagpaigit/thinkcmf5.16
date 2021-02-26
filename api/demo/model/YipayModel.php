<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27
 * Time: 11:09
 */

namespace api\demo\model;

use think\Model;
use think\Db;
use think;

class YipayModel extends Model
{
    //-- 公用数据
    private $_month_arr = [];
    /*
     * 配置文件相关数据：
     *  1： 最少门店数量： $mim_shop_num, :50 (小于50个时，使用新门店号)
     *  2:  使用次数小于20 的门店 占全部 使用门店的占比 $redix_lt20
     *      $radix_lt20_min :0.01  (小于0.01时，新建 门店号)
     *      $radix_lt20_max:0.20(大于等于0.2时，从原有有数据的门店号选择)
     *  3：新建门店号阈值 $new_shop_radix:0.7:新建 门店号的概率 (0.7-$redix_lt20)的平方
     *  4：从原有有数据门店号中选择概率：(已经产生33笔订单或以上的门店号将不再产生订单)
     *       $lt_10:5%  ---  1-9次的概率 为 0.02
     *       $lt_16:10%  ---  10-15次的概率 为 0.03
     *       $lt_20:80%  ---  16-19次的概率 为 0.91
     *       $lt_33:5%  ---  26-32次的概率 为 0.02
     *
     * 规则：
     * 1：当月已使用的门店号 总数据 小于 50个， 或 使用次数小于20次的门店号 占总使用门店号 比例小于0.01，使用新门店号
     * 2：如果 小于20次的 占比 大于0.2，则使用 已产生数据的门店号
     * 3：否则，使用新门店号的可能 百分比比为 (0.7-占比)*（0.7-占比）
     * 4：新门店号规则： 从 yj_000001 --- yj_010000 中随机生成一个未使用的门店号
     * 6：从已使用门店号 选取门门号规则：
     *  1-9次：0.05，10-15次：0.10，16-19次：0.80，20-25次：0.04，26-33次：0.01
     */

    public function test()
    {
        echo "取一万次数据，1-100 各数据的出现的次数：", '<HR/>';
        $tmp = [];
        for ($i = 0; $i < 10000; $i++) {
            $rand = rand();
            $tmp[] = $rand % 100 + 1;
        }
        sort($tmp);

        $arr = array_count_values($tmp);
        dump($arr);
    }



    //-- 选取门店号
    public function selectShop($param = [])
    {
        // 如果 月统计表中没有本月数据，清空所有数据，并生成使用门店号 yj_000001
        $currMonth = date("Y-m-01");
        $monthTime = strtotime($currMonth);
        $list = Db::name('YipayShopNumber')
            ->field('count(*) as len,num')
            ->where('update_time', 'egt', $monthTime)
            ->where('num','gt',0)
            ->group('num')
            ->select();


        $shop_id = $this->getShopId($list->toArray());


     //   return $shop_id;

        $param['shop_id'] = $shop_id;
        Db::name('YipayShopOrder')->strict(false)->insert($param);
        $len = Db::name('YipayShopOrder')->where(['shop_id' => $shop_id, 'is_pay' => 1])->where('create_time', '>=', $monthTime)->count();
        //  echo Db::name('YipayShopOrder')->getLastSql();
        Db::name('YipayShopNumber')->where(['shop_id' => $shop_id])->update(['num' => $len, 'update_time' => $param['create_time']]);



    }

    public function getShopId($list)
    {
        if (empty($list))
        {
            $newShopID = $this->selectNewShop();
            echo "本月第一笔数据,使用第一个门店号 {$newShopID}<br/>";
            return $newShopID;
        }


        $total = $lt10 = $lt16 = $lt20 = $lt26 = $lt33 = 0;
        foreach ($list as $val) {
            $len = $val['len'];
            $total += $len;
            $num = $val['num'];
            if ($val['num'] < 10) $lt10 += $len;
            if ($val['num'] < 16) $lt16 += $len;
            if ($val['num'] < 20) $lt20 += $len;
            if ($val['num'] < 26) $lt26 += $len;
            if ($val['num'] < 33) $lt33 += $len;
        }
        $this->_month_arr = ['10' => $lt10, '16' => $lt16, '20' => $lt20, '33' => $lt33, 'total' => $total,];



        $flag = 1;// 是否使用下一个门店号



        //门店号 总数据 小于50个 或者小于20次的门店号小于20个或者占比小于0.02 ，使用下一个门店号
        if (  $total < 50 ||  ($lt20 / $total) < 0.005)
        {

            echo "门店号 总数据 小于50个 或者小于20次的门店号或者占比小于0.005 ，使用新门店号<br/>";
            $flag = 1;
        }
        else
        {
            // 小于20的比例：
            $radix = $lt20/$total;
            if($radix>0.15)
            {
                $flag = 0;
                echo "使用次数小于20次的门店号占比大于0.15,当前订单关联到已使用门店号<br>";
            }
            else{
                echo "total={$total},radix={$radix}<br>";
                $number =  floor((0.7-$radix)*(0.7-$radix) *100);
                $rand = rand()%100;
                $flag = ($rand<=$number)? 1:0;
                echo "使用次数小于20次的门店号占比{$radix},，使用下一个门店号的概率为{$number},此次订单使用结果为{$flag}<br>";

            }



        }

        if($flag ==1)
        {
            $newShopID = $this->selectNewShop();
            echo "下一个门店号为 {$newShopID}";
            return $newShopID;
        }
        else
        {

             $shopID = $this->selecthadShop();
            echo "使用门店号为 {$shopID}";
            return  $shopID;
        }



    }

    //-- 选择新门店号-- 随机选择一条未使用的门店号
    public function selectNewShop()
    {

        $currMonth = date("Y-m-01");
        $monthTime = strtotime($currMonth);

        $rand = rand();
        $rand = $rand%10000;
        $shop_id = "yj_".str_pad($rand,6,0,STR_PAD_LEFT);
        $find = Db::name('YipayShopNumber')->where('update_time', '>=', $monthTime)->where(['shop_id'=>$shop_id])->count();
        if($find ==0) return $shop_id;
        else return $this->selectNewShop();


    }

    //-- 选择原有门店号
    /*
     *  1-9:2%;      1--4
     *  10-15:3%    5-10
     *  16-19:91%    11-191
     *  26-33 ：1%   198--200
     *
     *
     *
     */
    public function selecthadShop()
    {
        $arr = $this->_month_arr;
        $currMonth = date("Y-m-01");
        $monthTime = strtotime($currMonth);
        //-- 按洗衣次数倒序取相关数据
        $list = Db::name('YipayShopNumber')
            ->field('shop_id,num')
            ->where('update_time', 'egt', $monthTime)
            ->where('num', 'between', [1,32])
            ->order('num desc')
            ->select();
        $list = $list->toArray();
        $lt10 =  $lt16= $lt20 = $lt26= $lt33 =[];
        foreach ($list as $val)
        {
            $num = $val['num'];
            if($num<10) $lt10[] = $val;
            elseif($num<16) $lt16[] = $val;
            elseif($num<20) $lt20[] = $val;
            elseif($num<33) $lt33[] = $val;
        }

//        dump($lt10);

        $rand = rand();
        $rand = $rand%200 +1;

        echo 'rand='.$rand,',<br>';
        $flag = 'lt20';

        if($rand<=4) //1-9
        {
            if(count($lt10)>0) $flag='lt10';
            elseif(count($lt16)>0) $flag='lt16';
            elseif(count($lt20)>0) $flag='lt20';
            elseif(count($lt33)>0) $flag='lt33';
        }
        elseif($rand<=10)//10-15
        {
            if(count($lt16)>0) $flag='lt16';
            elseif(count($lt10)>0) $flag='lt10';
            elseif(count($lt20)>0) $flag='lt20';
            elseif(count($lt33)>0) $flag='lt33';


        }
        elseif($rand<=196){//16-19
            if(count($lt20)>0) $flag='lt20';
            elseif(count($lt10)>0) $flag='lt10';
            elseif(count($lt16)>0) $flag='lt16';
            elseif(count($lt33)>0) $flag='lt33';
        }
        else //26-33
        {

            if(count($lt33)>0) $flag='lt33';
            elseif(count($lt20)>0) $flag='lt20';
            elseif(count($lt10)>0) $flag='lt10';
            elseif(count($lt16)>0) $flag='lt16';
        }
        echo 'flag=',$flag,'<br>';
        $count = count($$flag);
        $rand = rand();
        $seat = $rand % $count;
        echo $seat;
        $shop_id = $$flag[$seat]['shop_id'];
        return $shop_id;



    }

}