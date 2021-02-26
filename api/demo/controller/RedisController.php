<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------


// https://www.daixiaorui.com
namespace api\demo\controller;

use cmf\controller\RestBaseController;
use think\App;
use think\Cache;
use think\cache\driver\Redis;

class RedisController extends ApiBaseController
{

    private $_config1;
    private $_config2;
    private $_redis;
    private $_redis1;
    private $_redis2;

    public function __construct(App $app = null)
    {
       // phpinfo();

        parent::__construct($app);
        $this->_config1 = [
            'host' => '127.0.0.1',
            'port' => 6379,
            //            'password' => 'admin999',
            'select' => 0,
            'timeout' => 0,
            'expire' => 0,
            'persistent' => false,
            'prefix' => 'pre1_',
        ];
        $this->_config2 = [
            'host' => '127.0.0.1',
            'port' => 6379,
            //            'password' => 'admin999',
            'select' => 0,
            'timeout' => 0,
            'expire' => 0,
            'persistent' => false,
            'prefix' => 'pre2_',
        ];

        $this->_redis = new Redis();
        $this->_redis1 = new Redis($this->_config1);
        $this->_redis2 = new Redis($this->_config2);
    }


    // -- 字符串 ---------------- begin ---------------------------//
    public function typeSet() // 取得与指定的键值相关联的值
    {

        $redis = $this->_redis;
        $redis->set('myname', 'zhangSan');
        echo $redis->get('myname');
    }

    public function setex() //  设置一个有生命周期的KEY-VALUE,对应的时间为秒
    {
        $this->_redis->setex('myage', 10, "30");
    }

    public function getSetex()
    {
        echo $this->_redis->get('myage');
    }

    public function setnx() //  etnx用于设置一个KEY-VALUE，这个函数会先判断Redis中是否有这个KEY，如果没有就SET，有就返回False。
    {
        $redis = $this->_redis;

        echo $redis->get('myname');
        echo "<br/>";
        $redis->setex('myname', 10, "liSi");
        echo $redis->get('myname');
        $redis->setex('mynewname', 10, "liSi");
        echo "<br/>";
        echo $redis->get('mynewname');

    }

    public function psetex() // 设置一个有生命周期的KEY-VALUE,psetex()使用的周期单位为毫秒。
    {
        $redis = $this->_redis;
        $res = $redis->psetex('psetex', 10000, 'psetex');
        echo $res, '<BR>';
    }


    public function getPsetex()
    {
        $redis = $this->_redis;
        echo $redis->get('psetex');
    }

    public function delete()
    {
        $redis = $this->_redis;
        $redis->set('delete', 'delete');
        echo $redis->get('delete'), '<BR>';
        $redis->del('delete');
        echo $redis->get('delete');
    }

    public function getSet() //设置一个VALUE 并且返回该KEY当前的VALUE。
    {
        $redis = $this->_redis;
        echo $redis->set('getSet', 'oldVue'), '<br>';
        echo $redis->getSet('getSet', 'getSet'), '<BR>';
        echo $redis->get('getSet');

    }

    public function multi()// multi()返回一个Redis实例，并且这个实例进入到了事务处理模式（批量处理）。当进入到事务处理模式，所有的方法调用都将返回相同的Redis实例，一直到exec()被调用执行事务处理。
    {
        $ret = $this->_redis->multi()
            ->set('key1', 'val1')
            ->get('key1')
            ->set('key2', 'val2')
            ->get('key2')
            ->exec();
        dump($ret);
    }

    public function exists()//   如果key存在，返回true，否则返回false。
    {
        $redis = $this->_redis;
        echo $redis->exists('myname'); /*  TRUE */
        echo '<br/>';
        echo $redis->exists('myname2'); /*  false */
    }

    /*
     * incr:默认对指定的KEY的值自增1,如果填写了第二个参数，将把第二个参数自增给KEY的值。
     *  incrBy : 相当于 incr 带了第二个参数
     *  incrByFloat:自增一个浮点型的数值
     *  decr,decrBy
     */
    public function mathFun()
    {
        $redis = $this->_redis;
        $redis->set('vue1', 2);
        $redis->incr('vue1');
        echo 'vue1=', $redis->get('vue1'), '<BR/>';
        $redis->incr('vue1', 2);
        echo 'vue1=', $redis->get('vue1'), '<BR/>';
        $redis->incrBy('vue1', 2);
        echo 'vue1=', $redis->get('vue1'), '<BR/>';
        $redis->incrByFloat('vue1', 2.5);
        echo 'vue1=', $redis->get('vue1'), '<BR/>';
        $redis->set('vue2', 5);
        $redis->decr('vue2', 2);
        echo 'vue2=', $redis->get('vue2'), '<BR/>';

    }


    public function mGet() //数组：返回相应的KEYS的值
    {
        $redis = $this->_redis;
        $redis->set('key1', 'value1');
        $redis->set('key2', 'value2');
        $redis->set('key3', 'value3');
        $redis1 = $redis->mGet(array('key1', 'key2', 'key3')); /* array('value1', 'value2', 'value3');*/
        $redis2 = $redis->mGet(array('key0', 'key1', 'key5')); /* array(`FALSE`, 'value2', `FALSE`);*/

        dump($redis1);
        dump($redis2);
    }

    public function mset()
    {
        $redis = $this->_redis;
        $redis->mSeT(array('key0' => 'value0', 'key1' => 'value1'));// 好像大小写不敏感
        var_dump($redis->get('key0'));
        var_dump($redis->get('key1'));
    }

    /*
     *  append:   添加指定的字符串到指定的字符串KEY---组合成一个新字符串。
     *  getRange  返回字符串的一部分
     *  setRange  修改字符串的一部分。
     *  strlen    返回字符串的长度。
     */

    public function arrFun()
    {
        $redis = $this->_redis;
        $redis->set('key', 'vaule');
        $redis->append('key', 'value2');
        echo $redis->get('key'), '<br/>';

        $redis->set('key', 'string value');
        $res1 = $redis->getRange('key', 0, 5); /* 'string' */
        $res2 = $redis->getRange('key', -5, -2); /* 'value' */
        echo 'res1=', $redis->get('key'), ',res2=', $res2, '<br/>';

        $redis->set('key', 'Hello world');
        $redis->setRange('key', 6, "redis"); /* returns 11 */
        $res = $redis->get('key'); /* "Hello redis" */
        echo $res, '<br/>';

        $redis->set('key', 'value');
        $res = $redis->strlen('key'); /* 5 */
        echo $res, '<br/>';
    }


    /*
     *  getBit 返回对应的位值
     *  setBit 设置位值
     *
     * bitop: and or not xor :位操作
     */

    public function bitFun()
    {
        $redis = $this->_redis;
        $redis->set('key', "\x6a"); //  j  this is 0101 1010
        $res1 = $redis->get('key'); /* 0 */
        $res2 = $redis->getBit('key', 0); /* 0 */
        $res3 = $redis->getBit('key', 1); /* 1 */
        $res4 = $redis->getBit('key', 2); /* 1 */
        echo $res1, '<br/>';
        echo $res2, '<br/>';
        echo $res3, '<br/>';
        echo $res4, '<br/>';

        $redis->set('key', "*");    // ord("*") = 42 = 0x2f = "0010 1010"
        $res1 = $redis->get('key'); /* chr(0x2f) = "/" = b("0010 1111") */
        $redis->setBit('key', 5, 1); /* returns 0 */
        $redis->setBit('key', 7, 1); /* returns 0 */
        $res2 = $redis->get('key'); /* chr(0x2f) = "/" = b("0010 1111") */
        echo $res1, '<br/>';
        echo $res2, '<br/>';
    }



    // -- 字符串 ---------------- end ---------------------------//


    //----数组 ------------------- begin -------------------------//

    /*
     *  lPush:添加一个字符串值到LIST容器的顶部（左侧），如果KEY不存在，曾创建一个LIST容器，如果KEY存在并且不是一个LIST容器，那么返回FLASE。
     *   rPush
     *   lPushx 添加一个VALUE到LIST容器的顶部（左侧）如果这个LIST存在的话。如果ADD成功， 返回LIST容器最新的长度。失败则返回FALSE。
     *   rPushx
     *   iPop 返回LIST顶部（左侧）的VALUE，并且从LIST中把该VALUE弹出。 取得VALUE成功，返回TURE。如果是一个空LIST则返回FLASE。
     *   rPop
     *
     *    blPop,brPop: lpop命令的block版本即阻塞版本。如果LIST容器中有VAULE，将会返回ARRAY（'listName''element'）。如果TIMEOUT参数为空，那么如果LIST为空，blPop或者brPop将或结束调用。
     *      如果设置了timeout参数，blPop或者brPop将被挂起暂停运行TIMEOUT参数的时间，在此期间如果LIST被PUSH了元素，将在TIMEOUT时间结束后，被POP出来。
     *
     *      lLen：根据KEY返回该KEY代表的LIST的长度，如果这个LIST不存在或者为空，那么ISIZE返回0，如果指定的KEY的数据类型不是LIST或者不为空，那么返回FALSE。
     *              所以在这里多说一句，当用ISize返回判断值的时候，===就有用处了，这里FLASE和0是两个概念了。
     *
     *      rpoplpush | brpoplpush(阻塞版):从源LIST的最后弹出一个元素，并且把这个元素从目标LIST的顶部（左侧）压入目标LIST。
     */
    public function push()
    {
        $redis = $this->_redis;
        $redis->del('key1');
        echo 'lPush:添加一个字符串值到LIST容器的顶部（左侧），如果KEY不存在，曾创建一个LIST容器，如果KEY存在并且不是一个LIST容器，那么返回FLASE。<br/>';
        echo 'rPush:向底部增加。<br/>';

        $redis->lPush('key1', 'C'); // returns 1
        $redis->lPush('key1', 'B');  // returns 3
        $redis->lPush('key1', 'C'); // returns 1
        $redis->lPush('key1', 'B'); // returns 2
        $redis->rPush('key1', 'A'); // returns 3
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        $redis->lPushx('key1', 'a'); // returns 3
        $redis->rPush('key1', 'b'); // returns 3
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        echo 'lPop 返回LIST顶部（左侧）的VALUE，并且从LIST中把该VALUE弹出。 取得VALUE成功，返回TURE。如果是一个空LIST则返回FLASE。<br/>';
        $res = $redis->lPop('key1');
        echo $res, '<BR>';
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        echo 'rPop 返回LIST底部（右侧）的VALUE，并且从LIST中把该VALUE弹出。 取得VALUE成功，返回TURE。如果是一个空LIST则返回FLASE。<br/>';
        $res = $redis->rPop('key1');
        echo $res, '<BR>';
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        echo '根据KEY返回该KEY代表的LIST的长度，如果这个LIST不存在或者为空，那么ISIZE返回0，如果指定的KEY的数据类型不是LIST或者不为空，那么返回FALSE。<br/>';
        $res = $redis->lLen('key1');
        echo $res, '<BR>';
        echo "rpoplpush:从源LIST的最后弹出一个元素，并且把这个元素从目标LIST的顶部（左侧）压入目标LIST。<br>";
        $res = $redis->rpoplpush('key1', 'key1');
        echo $res, '<BR>';
        $res = $redis->lRange('key1', 0, -1);
        dump($res);

    }

    /*
     * lindex: 根据索引值返回指定KEY LIST中的元素。0为第一个元素，1为第二个元素。-1为倒数第一个元素，-2为倒数第二个元素。如果指定了一个不存在的索引值，则返回FLASE。
     * lSet:根据索引值设置新的VAULE
     * lRange:取得指定索引值范围内的所有元素。
     *
     *  lTrim:它将截取LIST中指定范围内的元素组成一个新的LIST并指向KEY。简短解说就是截取LIST。
                这个可不是JS，或者PHP中清空空格的意思。
     *  lrem|lRemove:首先要去判断count参数，如果count参数为0，那么所有符合删除条件的元素都将被移除。
     *                  如果count参数为整数,将从左至右删除count个符合条件的元素，如果为负数则从右至左删除count个符合条件的元素。
     *
     *      lInsert :在指定LIST中的指定中枢VALUE的左侧或者右侧插入VALUE。如果这个LIST不存在，或者这个pivot(key position)不存在，那么这个VALUE不会被插入。
     */
    public function statics()
    {
        $redis = $this->_redis;
        $redis->del('key1');
        $redis->rPush('key1', 'A');
        $redis->rPush('key1', 'B');
        $redis->rPush('key1', 'C');
        $res1 = $redis->lindex('key1', 0); /* 'A' */
        $res2 = $redis->lindex('key1', -1); /* 'C' */
        echo $res1, '<BR>';
        echo $res2, '<BR>';
        $redis->lSet('key1', 0, 'a');
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        $redis->rPush('key1', 'x');
        $redis->rPush('key1', 'y');
        $redis->rPush('key1', 'z');

        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        $redis->lTrim('key1', 1, 3);
        $res = $redis->lRange('key1', 0, -1);
        dump($res);

        $redis->rPush('key1', 'CDEC');
        $redis->rPush('key1', 'C');
        $redis->rPush('key1', 'D');
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        $redis->lRem('key1', 'C', -1); /* 2 */
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        $redis->lInsert('key1', 'BEFORE', 'C', 'Y'); /* 5 */
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
        $redis->lInsert('key1', 'AFTER', 'C', 'Y'); /* 5 */
        $res = $redis->lRange('key1', 0, -1);
        dump($res);
    }

    //----数组 ------------------- end -------------------------//


    //-- 集合------------------------begin-----------------------//

    /*
     * sAdd://添加一个VALUE到SET容器中，如果这个VALUE已经存在于SET中，那么返回FLASE。
     *  sRem 从SET容器中移除指定的VALUE
     * sMembers : 返回SET集合中的所有元素。
     *
     * sMove : 移动一个指定的MEMBER从源SET到指定的另一个SET中。
     *
     * sIsMember :检查VALUE是否是SET容器中的成员。
     *  sCard: 返回SET容器的成员数:
     *  sPop:随机返回一个元素，并且在SET容器中移除该元素。
     */
    public function setFun()
    {
        $redis = $this->_redis;
        $redis->del('key1');
        $redis->del('key2');
        $redis->sAdd('key1', 'member1'); /* TRUE, 'key1' => {'member1'} */
        $redis->sAdd('key1', 'member2'); /* TRUE, 'key1' => {'member1', 'member2'}*/
        $redis->sAdd('key1', 'member2'); /* FALSE, 'key1' => {'member1', 'member2'}*/

        $res = $redis->sMembers('key1');
        dump($res);

        $redis->sRem('key1', 'member1');
        $res = $redis->sMembers('key1');
        dump($res);
        $redis->sAdd('key1', 'member3');
        $redis->sAdd('key1', 'member4');
        $res = $redis->sMembers('key1');
        dump($res);
        $redis->sAdd('key2', 'membera');
        $redis->sAdd('key2', 'memberb');
        $res = $redis->sMembers('key2');
        dump($res);
        $redis->sMove('key1', 'key2', 'member4');
        $res = $redis->sMembers('key1');
        dump($res);
        $res = $redis->sMembers('key2');
        dump($res);
        echo $res = $redis->sIsMember('key1', 'member3'), '<BR/>';
        echo $res = $redis->sCard('key1'), '<BR/>';

        $redis->sAdd('key1', 'member13');
        $redis->sAdd('key1', 'member14');
        $redis->sAdd('key1', 'member23');
        $redis->sAdd('key1', 'member24');
        $redis->sAdd('key1', 'member33');
        $redis->sAdd('key1', 'member34');
        $res = $redis->sMembers('key1');
        dump($res);
        echo $res = $redis->sPop('key1'), '<BR/>';
        $res = $redis->sMembers('key1');
        dump($res);
    }

    /*
     *  sInter : 返回指定SETS集合的交集结果。如果只是指定了一个SET集合，那么返回该SET集合。如果在参数中有参数错误，那么则返回FLASE。
     *  sInterStore :  执行一个交集操作，并把结果存储到一个新的SET容器中。
     *  sUnion : 执行一个并集操作在N个SET容器之间，并返回结果。
     * sUnionStore :执行一个并集操作就和sUnion()一样，但是结果储存在第一个参数中。
     *
     * sDiff :执行差集操作在N个不同的SET容器之间，并返回结果。这个操作取得结果是第一个SET相对于其他参与计算的SET集合的差集。
     *          (Result = SET0 - (SET1 UNION SET2 UNION ....SET N))
     *  sDiffStore: 与sDiff函数功能一样，只是结果为一个新的SET集合，但是结果储存在第一个参数中。
     */
    public function setFun2()
    {
        $redis = $this->_redis;
        $redis->del('key1');
        $redis->del('key2');
        $redis->del('key3');
        $redis->sAdd('key1', 'member1'); /* TRUE, 'key1' => {'member1'} */
        $redis->sAdd('key1', 'member2'); /* TRUE, 'key1' => {'member1', 'member2'}*/
        $redis->sAdd('key1', 'member3'); /* FALSE, 'key1' => {'member1', 'member2'}*/

        $redis->sAdd('key2', 'member2'); /* TRUE, 'key1' => {'member1'} */
        $redis->sAdd('key2', 'member22'); /* TRUE, 'key1' => {'member1', 'member2'}*/
        $redis->sAdd('key2', 'member222'); /* FALSE, 'key1' => {'member1', 'member2'}*/

        $redis->sAdd('key3', 'member2'); /* TRUE, 'key1' => {'member1'} */
        $redis->sAdd('key3', 'member12'); /* TRUE, 'key1' => {'member1', 'member2'}*/
        $redis->sAdd('key3', 'member123'); /* FALSE, 'key1' => {'member1', 'member2'}*/

        dump($redis->sInter('key1', 'key2', 'key3'));
        $redis->sInterStore('output', 'key1', 'key2', 'key3');
        $res = $redis->sMembers('output');
        dump($res);
        $res = $redis->sUnion('key1', 'key2', 'key3');
        dump($res);
        $redis->del('output');
//        $redis->set('output','123');
        $redis->sUnionStore('output', 'key1', 'key2', 'key3');
        $res = $redis->sMembers('output');
        $redis->set('output', '123');
        dump($res);
        echo $redis->get("output");
        $res = $redis->sDiff('key2', 'key1', 'key3');
        dump($res);
        $redis->sDiffStore('output', 'key2', 'key2', 'key3');
        $res = $redis->sMembers('output');
        dump($res);

    }
    //-- 集合------------------------ end -----------------------//

    //-- 有序集合------------------------ begin -----------------------//

    /*
     *  zAdd:增加一个或多个元素，如果该元素已经存在，更新它的socre值
                虽然有序集合有序，但它也是集合，不能重复元素，添加重复元素只会
                更新原有元素的score值
     *  zRange :取得特定范围内的排序元素,0代表第一个元素,1代表第二个以此类推。-1代表最后一个,-2代表倒数第二个...
     *  zRem:从有序集合中删除指定的成员。
     *  zRevRange:返回key对应的有序集合中指定区间的所有元素。这些元素按照score从高到低的顺序进行排列。
     *              对于具有相同的score的元素而言，将会按照递减的字典顺序进行排列。
     *              该命令与ZRANGE类似，只是该命令中元素的排列顺序与前者不同。
     *  zRangeByScore 返回key对应的有序集合中score介于min和max之间的所有元素（包哈score等于min或者max的元素）。
     *                  元素按照score从低到高的顺序排列。如果元素具有相同的score，那么会按照字典顺序排列。
     *                  可选的选项LIMIT可以用来获取一定范围内的匹配元素。如果偏移值较大，有序集合需要在获得将要返回的元素之前进行遍历，因此会增加O(N)的时间复杂度。
     *                  可选的选项WITHSCORES可以使得在返回元素的同时返回元素的score，该选项自从Redis 2.0版本后可用。
     *  zCount:返回key对应的有序集合中介于min和max间的元素的个数。
     *  zRemRangeByScore:移除key对应的有序集合中scroe位于min和max（包含端点）之间的所哟元素。
     *                      从2.1.6版本后开始，区间端点min和max可以被排除在外，这和ZRANGEBYSCORE的语法一样。
     *  zRemRangeByRank：移除key对应的有序集合中rank值介于start和stop之间的所有元素。start和stop均是从0开始的，并且两者均可以是负值。
     *                      当索引值为负值时，表明偏移值从有序集合中score值最高的元素开始。
     *                      例如：-1表示具有最高score的元素，而-2表示具有次高score的元素，以此类推。
     */
    public function zSetFun()
    {
        $redis = $this->_redis;
        $redis->del('key1', 'key2', 'key3');
        $redis->zAdd('key1', 1, 'v1');
        $redis->zAdd('key1', 3, 'v3');
        $redis->zAdd('key1', 2, 'v2');
        $res = $redis->zRange('key1', 0, -1);
        dump($res);

        $res = $redis->zRange('key1', 1, -1);
        dump($res);

        $redis->zRem('key1', 'v3');
        $res = $redis->zRange('key1', 0, -1);
        dump($res);
        $redis->zAdd('key1', 10, 'v10');
        $redis->zAdd('key1', 20, 'v20');
        $redis->zAdd('key1', 4, 'v4');
        $redis->zAdd('key1', 10, 'v4');
        $res = $redis->zRevRange('key1', 0, -1);
        dump($res);
        $res = $redis->zRevRange('key1', 0, -1, true);
        dump($res);

//        $res = $redis->zRevRange('key1', 0, -1);
//        dump($res);

        $res = $redis->zRange('key1', 0, -1);
        dump($res);
        echo "zRangeByScore:<br/>";
        $res = $redis->zRangeByScore('key1', 0, 3); /* array('val0', 'val2') */
        dump($res);

        $res = $redis->zCount('key1', 0, 10);
        echo $res, '<BR>';

        $res = $redis->zRemRangeByScore('key1', 0, 3);
        echo $res, '<BR>';

        $redis->zAdd('key1', 8, 'v8');
        $redis->zAdd('key1', 30, 'v30');
        $redis->zAdd('key1', 40, 'v40');
        $redis->zAdd('key1', 16, 'v16');
        $res = $redis->zRange('key1', 0, -1);
        dump($res);
        $redis->zRemRangeByRank('key1', 0, 1);
        $res = $redis->zRange('key1', 0, -1);
        dump($res);
    }

    /*
     * zSize: 返回存储在key对应的有序集合中的元素的个数。
     * zScore : 返回key对应的有序集合中member的score值。如果member在有序集合中不存在，那么将会返回nil。
     * zRank|zRevRank:返回key对应的有序集合中member元素的索引值，元素按照score从低到高进行排列。
     *                  rank值（或index）是从0开始的，这意味着具有最低score值的元素的rank值为0。
     *                  使用ZREVRANK可以获得从高到低排列的元素的rank（或index）。
     * zIncrBy:将key对应的有序集合中member元素的scroe加上increment。如果指定的member不存在，那么将会添加该元素，并且其score的初始值为increment。
     *              如果key不存在，那么将会创建一个新的有序列表，其中包含member这一唯一的元素。
     *              如果key对应的值不是有序列表，那么将会发生错误。
     *              指定的score的值应该是能够转换为数字值的字符串，并且接收双精度浮点数。同时，你也可用提供一个负值，这样将减少score的值。
     *
     *  zunionstore:对keys对应的numkeys个有序集合计算合集，并将结果存储在destination中。在传递输入keys之前必须提供输入keys的个数和其它可选参数。
     *          在默认情况下，元素的结果score是包含该元素的所有有序集合中score的和。
     *          如果使用WEIGHTS选项，你可以对每一个有序集合指定一个操作因子。
     *              这意味着每一个有序集合中的每个元素的score在传递给聚合函数之前均会被乘以该因子。当WEIGHTS没有指定时，操作因子默认为1。
     *          使用AGGREGATE选项，你可以指定交集中的结果如何被聚合。
     *              该选项默认值为SUM，在这种情况下，一个元素的所有score值均会被相加。
     *              当选项被设置为MIN或MAX时，结果集合中将会包含一个元素的最大或者最小的score值。
     *          如果destination已经存在，那么它将会被重写。
     *  zinterstore:计算numkeys个由keys指定的有序集合的交集，并且将结果存储在destination中。在该命令中，在你传递输入keys之前，必须提供输入keys的个数和其它可选的参数。
     *          在默认情况下，一个元素的结果score是具有该元素的所有有序集合的score的和。
     *          关于WEIGHTS和AGGREGATE选项，可以参看ZUNIONSTORE命令。如果目标已经存在，那么它将会被重写。
     *
     *
     *
     */
    public function zSetFun2()
    {
        $redis = $this->_redis;
        $redis->del('key1', 'key2', 'key3');
        $redis->zAdd('key1', 1, 'v1');
        $redis->zAdd('key1', 3, 'v3');
        $redis->zAdd('key1', 2, 'v2');
        $redis->zAdd('key1', 0, 'v0');
        $redis->zAdd('key1', 9, 'v9');
        $res = $redis->zRange('key1', 0, -1);
        dump($res);

        $res = $redis->zCard('key1');
        echo $res, '<BR>';
        $res = $redis->zScore('key1', 'v1');
        echo $res, '<BR>';
        $res = $redis->zRank('key1', 'v2'); /* 1 */
        echo 'zRank:', $res, '<BR>';
        $res = $redis->zRank('key1', 'v0'); /* 1 */
        echo 'zRank:', $res, '<BR>';
        $res = $redis->zRevRank('key1', 'v0'); /* 1 */
        echo 'zRevRank:', $res, '<BR>';

        $redis->zIncrBy('key1', 10, 'v1'); /* 3.5 */
        $res = $redis->zScore('key1', 'v1');
        echo $res, '<BR>';
        $res = $redis->zRange('key1', 0, -1);
        dump($res);

        $redis->zAdd('key2', 2, 'val2');
        $redis->zAdd('key2', 3, 'val3');
        $redis->zAdd('key2', 10, 'v1');
        $redis->zAdd('key2', 5, 'v2');
        $redis->zunionstore("key3", ['key1', 'key2']);
        $res = $redis->zRange('key3', 0, -1);
        dump($res);


        $res = $redis->zRange('key1', 0, -1);
        dump($res);

        $res = $redis->zRange('key2', 0, -1);
        dump($res);


        $redis->zinterstore("key4", ['key1', 'key2']);
        $res = $redis->zRange('key4', 0, -1);
        dump($res);
    }
    //-- 有序集合------------------------ end -----------------------//

    //-- Hash ------------------------ begin -----------------------//

    /*
     *  hSet : 添加一个VALUE到HASH中。如果VALUE已经存在于HASH中，则返回FALSE。
     * hSetNx :添加一个VALUE到HASH STORE中，如果FIELD不存在。
     * hLen:取得HASH表的长度。
     * hDel :删除 元素
     * hKeys:取得HASH表中的KEYS，以数组形式返回。
     * hVals:取得HASH表中所有的VALUE，以数组形式返回。
     * hGetAll:取得整个HASH表的信息，返回一个以KEY为索引VALUE为内容的数组。
     */

    public function hashFun1()
    {
        $redis = $this->_redis;
        $redis->del('h', 'h2');
        $redis->hset('h', 'key1', 'hello');
        $res = $redis->hGet('h', 'key1');
        echo $res, '<BR>';

        $redis->hset('h2', 'key1', 'helloworld');
        $res = $redis->hGet('h2', 'key1');
        echo $res, '<BR>';
        $res = $redis->hGet('h', 'key1');
        echo 'res1= ', $res, '<BR>';

        $redis->del('h', 'h2');
        $redis->hSetNx('h', 'key1', 'hello'); /* TRUE, 'key1' => 'hello' in the hash at "h" */
        $redis->hSetNx('h', 'key1', 'world'); // /* FALSE, 'key1' => 'hello' in the hash at "h". No change since the field

        $redis->del('h');
        $redis->hSet('h', 'key1', 'hello');
        $redis->hSet('h', 'key2', 'plop');
        $resLen = $redis->hLen('h'); /* returns 2 */
        echo 'resLen= ', $resLen, '<BR>';
        $redis->hDel('h', 'key2');
        $resDelKey2 = $redis->hGet('h', 'key2');
        echo 'resDelKey2= ', $resDelKey2, '<BR>';
        $resH = $redis->hGet('h', 'key1');
        dump($resH);
        $redis->del('h');
        $redis->hSet('h', 'a', 'x');
        $redis->hSet('h', 'b', 'y');
        $redis->hSet('h', 'c', 'z');
        $redis->hSet('h', 'd', 't');
        var_dump($redis->hKeys('h'));
        var_dump($redis->hVals('h'));
        dump($redis->hGetAll('h'));
    }


    /*
     * hExists:验证HASH表中是否存在指定的KEY-VALUE
     *hIncrBy: 根据HASH表的KEY，为KEY对应的VALUE自增参数VALUE。
     * hIncrByFloat:根据HASH表的KEY，为KEY对应的VALUE自增参数VALUE。浮点型
     *
     * hMset:批量填充HASH表。不是字符串类型的VALUE，自动转换成字符串类型。使用标准的值。NULL值将被储存为一个空的字符串。
     *  hMGet:批量取得HASH表中的VALUE。
     */
    public function hashFun2()
    {
        $redis = $this->_redis;
        $redis->del('h');
        $res1 = $redis->hExists('h','a');
        echo $res1,'<BR>';
        $redis->hSet('h','a','x');
        $res1 = $redis->hExists('h','a');
        echo $res1,'<BR>';
        $redis->hIncrByFloat('h','b', 1.5); /* returns 1.5: h[x] = 1.5 now */
        $res1 = $redis->hGet('h','b');
        echo $res1,'<BR>';
        $redis->hIncrBy('h','c', 5); /* returns 1.5: h[x] = 1.5 now */
        $res1 = $redis->hGet('h','c');
        echo $res1,'<BR>';

        $redis->del('h');
        $redis->hMset('h', array('name' => 'Joe', 'salary' => 2000));
        $redis->hIncrBy('h', 'salary', 100); // Joe earns 100 more now.
        $res = $redis->hmGet('h', array('name', 'salary')); /* returns array('field1' => 'value1', 'field2' => 'value2') */
        dump($res);
       // $res = $redis->hmGet('h'); /* 错误，需要 两个参数 */
       // dump($res);

        $redis->del('h');
        $redis->hMset('h:1000', array('name' => 'Joe', 'salary' => 2000));
        $redis->hMset('h:1001', array('name' => 'jane', 'salary' => 3000));
        $res = $redis->hmGet('h:1001',['name','salary']);
        dump($res);
    }




    //-- Hash ------------------------ end -----------------------//


    //-- session
    public function session()
    {
        $redis = new \think\session\driver\Redis();
        $arr = ['name'=>'zhangsan','age'=>21];
     //   $redis->open('../data/runtime/session/session.txt','test');
        $redis->write('user:1000',json_encode($arr));
        $tmp = $redis->read('user:1000');
        dump($tmp);
        $redis->close();

    }
}