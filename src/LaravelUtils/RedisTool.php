<?php

namespace UtilBox\LaravelUtils;

use Illuminate\Support\Facades\Redis;

/**
 * Class RedisTool.
 */
class RedisTool
{
    /************** Key ***************/

    /**
     * 查找符合给定模式的key.
     *
     * @param string $pattern
     *
     * @return array
     */
    public function keys(string $pattern = '*'): array
    {
        return Redis::keys($pattern);
    }

    /**
     * 从当前数据库中随机返回(不删除)一个key。
     *
     * @return array
     */
    public function randomKey(): array
    {
        $arr = Redis::randomkey();

        return false === $arr ? [] : $arr;
    }

    /**
     * 返回给定key的剩余生存时间(以秒为单位)。
     * 当key不存在或没有设置生存时间时，返回-1 。
     *
     * @param string $key
     *
     * @return int
     */
    public function ttl(string $key): int
    {
        return (int) Redis::ttl($key);
    }

    /**
     * 将当前数据库(默认为0)的key移动到给定的数据库db当中。
     *
     * @param string $key
     * @param string $db
     * @param int    $useDb 声明使用什么数据库
     *
     * @return bool
     */
    public function move(string $key, string $db, int $useDb = 0): bool
    {
        if ($useDb > 0) {
            Redis::select($useDb);
        }

        return Redis::move($key, $db);
    }

    /**
     * 重命名key 当newkey已经存在时，RENAME命令将覆盖旧值。
     *
     * @param string $key
     * @param string $newKey
     *
     * @return bool
     */
    public function reName(string $key, string $newKey): bool
    {
        return Redis::rename($key, $newKey);
    }

    /**
     * 重命名key，只能当newkey不存在时，才将key改为newkey。
     *
     * @param string $key
     * @param string $newKey
     *
     * @return bool
     */
    public function reNameNx(string $key, string $newKey): bool
    {
        return Redis::renamenx($key, $newKey);
    }

    /**
     * 给key设置过期时间.
     *
     * @param string $key
     * @param int    $seconds 单位（秒）
     *
     * @return bool
     */
    public function expire(string $key, int $seconds): bool
    {
        return Redis::expire($key, $seconds);
    }

    /**
     * 给key设置过期时间.
     *
     * @param string $key
     * @param int    $timestamp UNIX时间戳
     *
     * @return bool
     */
    public function expireAt(string $key, int $timestamp): bool
    {
        return Redis::expireat($key, $timestamp);
    }

    /**
     * 从内部察看给定key的Redis对象。使用方法【object("REFCOUNT", 'str_01')】
     * OBJECT REFCOUNT <key>返回给定key引用所储存的值的次数。此命令主要用于除错。
     * OBJECT ENCODING <key>返回给定key锁储存的值所使用的内部表示(representation)。
     * OBJECT IDLETIME <key>返回给定key自储存以来的空转时间(idle， 没有被读取也没有被写入)，以秒为单位。
     *
     * @param string $subCommand
     * @param string $arguments
     *
     * @return mixed
     */
    public function object(string $subCommand, string $arguments)
    {
        return Redis::object($subCommand, $arguments);
    }

    /**
     * 当生存时间移除成功时.
     *
     * @param string $key
     *
     * @return bool
     */
    public function persist(string $key): bool
    {
        return Redis::persist($key);
    }

    /**
     * 判断是否存在.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function exists(string $key): bool
    {
        return Redis::exists($key);
    }

    /**
     * 返回类型
     * none(key不存在) int(0)
     * string(字符串) int(1)
     * list(列表) int(3)
     * set(集合) int(2)
     * zset(有序集) int(4)
     * hash(哈希表) int(5).
     *
     * @param string $key
     *
     * @return int
     */
    public function type(string $key): int
    {
        return Redis::type($key);
    }

    /**
     * 删除.
     *
     * @param string $key
     *
     * @return bool
     */
    public function del(string $key): bool
    {
        $res = Redis::del($key);
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 排序 ，返回键值从小到大排序的结果.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function sortAsc(string $key)
    {
        return Redis::sort($key);
    }

    /**
     * 排序 ，返回键值从大到小排序的结果.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function sortDesc(string $key)
    {
        return Redis::sort($key, ['by' => 'desc']);
    }

    /************** String ***************/

    /**
     * 保存.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function set(string $key, string $value): bool
    {
        return Redis::set($key, $value);
    }

    /**
     * 带有效期保存.
     *
     * @param string $key
     * @param string $value
     * @param int    $timeout 有效期（单位秒）
     *
     * @return bool
     */
    public function setEx(string $key, string $value, int $timeout): bool
    {
        return Redis::setex($key, $timeout, $value);
    }

    /**
     * 当key不存在的时候才保存.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function setNx(string $key, string $value): bool
    {
        return 1 === Redis::setnx($key, $value) ? true : false;
    }

    /**
     * 用value参数覆写(Overwrite)给定key所储存的字符串值，从偏移量offset开始。
     *
     * @param string $key
     * @param int    $offset
     * @param string $value
     *
     * @return mixed 返回被SETRANGE修改之后，字符串的长度,如果返回false说明key不是String类型
     */
    public function setRange(string $key, int $offset, string $value)
    {
        return Redis::setrange($key, $offset, $value);
    }

    /**
     * 同时保存多个 key-value.
     *
     * @param array $keyValue
     *
     * @return bool
     */
    public function mSet(array $keyValue): bool
    {
        return Redis::mset($keyValue);
    }

    /**
     * 同时保存多个key-value,当所有key都不存在的时候才保存,即使只有一个key已存在也不行.
     *
     * @param array $keyValue
     *
     * @return bool
     */
    public function mSetNx(array $keyValue): bool
    {
        return Redis::msetnx($keyValue);
    }

    /**
     * 将value追加到原来key的值之后，如果key不存在则创建一个.
     *
     * @param string $key
     * @param string $value
     *
     * @return int 返回字符串长度
     */
    public function appEnd(string $key, string $value): int
    {
        return Redis::append($key, $value);
    }

    /**
     * 获取.
     *
     * @param string $key
     *
     * @return mixed 返回对应的值，如果不存在则返回null
     */
    public function get(string $key)
    {
        return Redis::get($key);
    }

    /**
     * 获取多个key的值 【使用方法 mGet('key1','key2','key3')】.
     *
     * @param string ...$key
     *
     * @return array 当其中某个没有则该值为null
     */
    public function mGet(string ...$key): array
    {
        return Redis::mget($key);
    }

    /**
     * 返回key中字符串值的子字符串，字符串的截取范围由start和end两个偏移量决定(包括start和end在内)。
     * 负数偏移量表示从字符串最后开始计数，-1表示最后一个字符，-2表示倒数第二个，以此类推.
     *
     * @param string $key
     * @param int    $start
     * @param int    $end
     *
     * @return mixed
     */
    public function getRange(string $key, int $start, int $end)
    {
        return Redis::getrange($key, $start, $end);
    }

    /**
     * 将给定key的值设为value，并返回key的旧值。
     *
     * @param string $key
     * @param string $value
     *
     * @return bool|string 没有旧值(表示之前不存在该key，则新创建一个)则返回空字符串(""),如果返回false则是失败的，
     */
    public function getSet(string $key, string $value)
    {
        $type     = $this->type($key);
        $oldValue = Redis::getset($key, $value);
        if (0 === $type) {
            return '';
        }
        if (1 === $type) {
            return $oldValue;
        }

        return false;
    }

    /**
     * 返回字符串字符的长度，返回false则代表没有该key或非string类型.
     *
     * @param string $key
     *
     * @return bool|int
     */
    public function strLen(string $key)
    {
        if (1 === $this->type($key)) {
            return (int) Redis::strlen($key);
        }

        return false;
    }

    /**
     * 将key中储存的数字值 +1 。
     *
     * @param string $key
     *
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function incr(string $key)
    {
        return Redis::incr($key);
    }

    /**
     * 将key中储存的数字值 +{$increment}.
     *
     * @param string $key
     * @param int    $increment
     *
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function incrBy(string $key, int $increment)
    {
        return Redis::incrby($key, $increment);
    }

    /**
     * 将key中储存的数字值 -1.
     *
     * @param string $key
     *
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function decr(string $key)
    {
        return Redis::decr($key);
    }

    /**
     * 将key中储存的数字值 -{$decrement}.
     *
     * @param string $key
     * @param int    $decrement
     *
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function decrBy(string $key, int $decrement)
    {
        return Redis::decrby($key, $decrement);
    }

    /**
     * 对key所储存的字符串值，设置或清除指定偏移量上的位(bit)。
     *
     * @param string $key
     * @param $offset
     * @param $value
     *
     * @return mixed 指定偏移量原来储存的位（"0"或"1"）
     */
    public function setBit(string $key, int $offset, int $value)
    {
        return Redis::setbit($key, $offset, $value);
    }

    /**
     * 对key所储存的字符串值，获取指定偏移量上的位(bit)。
     *
     * @param string $key
     * @param $offset
     *
     * @return mixed 字符串值指定偏移量上的位(bit)
     */
    public function getBit(string $key, int $offset)
    {
        return Redis::getbit($key, $offset);
    }

    /************** Hash ***************/

    /**
     * 将哈希表key中的域field的值设为value (当key不存在则创建，当域field已经存在于哈希表中，则旧值被覆盖).
     *
     * @param string $key
     * @param string $field
     * @param $value
     *
     * @return bool
     */
    public function hSet(string $key, string $field, $value): bool
    {
        $hSetRes = Redis::hset($key, $field, $value);

        return (1 === $hSetRes || 0 === $hSetRes) ? true : false;
    }

    /**
     * 将哈希表key中的域field的值设为value (当key不存在则创建，当域field已经存在于哈希表中，则一样无效).
     *
     * @param string $key
     * @param string $field
     * @param $value
     *
     * @return bool
     */
    public function hSetNx(string $key, string $field, $value): bool
    {
        return 1 === Redis::hsetnx($key, $field, $value) ? true : false;
    }

    /**
     * 同时将多个field-value(域-值)对设置到哈希表key中【使用方法：hMSet('key_name', ['name' => '志在卓越', 'from' => 'MaoMing']))】
     * 如果key不存在,则创建。域field不存在则创建field，field存在则会覆盖哈希表中已存在的域field （记住只是覆盖某个field不是整个key）.
     *
     * @param string $key
     * @param array  $fieldValue
     *
     * @return bool
     */
    public function hMSet(string $key, array $fieldValue): bool
    {
        $type = $this->type($key);
        if (0 === $type || 5 === $type) {
            return Redis::hmset($key, $fieldValue);
        } else {
            return false;
        }
    }

    /**
     * 返回哈希表key中给定域field的值。(当给定域field不存在或是给定key不存在时，返回null).
     *
     * @param string $key
     * @param string $field
     *
     * @return mixed
     */
    public function hGet(string $key, string $field)
    {
        $hGetRes = Redis::hget($key, $field);

        return false === $hGetRes ? null : $hGetRes;
    }

    /**
     * 返回哈希表key中，一个或多个给定域的值【使用方法 hMGet('key_name','field1','field2','field3')】
     * 如果整个key都不存在，则里面的所有值都是null
     * 如果给定的某个域field不存在于哈希表，那么某个就是null值
     *
     * @param string $key
     * @param string ...$field
     *
     * @return mixed
     */
    public function hMGet(string $key, string ...$field): array
    {
        $result = Redis::hmget($key, $field);

        return array_map(function ($value) {
            return false === $value ? null : $value;
        }, $result);
    }

    /**
     * 返回哈希表key中，所有的域和值 （当key不存在则返回空数组）.
     *
     * @param string $key
     *
     * @return array
     */
    public function hGetAll(string $key): array
    {
        return Redis::hgetall($key);
    }

    /**
     * 删除哈希表key中的一个或多个指定域field，不存在的域将被忽略。
     *
     * @param string $key
     * @param string ...$field
     *
     * @return int 返回删除成功的个数
     */
    public function hDel(string $key, string ...$field): int
    {
        return Redis::hdel($key, ...$field);
    }

    /**
     * 返回哈希表key中域的数量.
     *
     * @param string $key
     *
     * @return int
     */
    public function hLen(string $key): int
    {
        return Redis::hlen($key);
    }

    /**
     * 查看哈希表key中，给定域field是否存在.
     *
     * @param string $key
     * @param string $field
     *
     * @return bool
     */
    public function hExists(string $key, string $field): bool
    {
        return Redis::hexists($key, $field);
    }

    /**
     * 为哈希表key中的域field的值 +{$increment} ,$increment可以负数.
     *
     * @param string $key
     * @param string $field
     * @param int    $increment
     *
     * @return int|bool 如果返回false,说明值不是数字类型字符
     */
    public function hIncrBy(string $key, string $field, int $increment)
    {
        return Redis::hincrby($key, $field, $increment);
    }

    /**
     * 返回哈希表key中的所有域field,如果key不存在则返回空数组.
     *
     * @param string $key
     *
     * @return array
     */
    public function hKeys(string $key): array
    {
        return Redis::hkeys($key);
    }

    /**
     * 返回哈希表key中的所有值value,如果key不存在则返回空数组.
     *
     * @param string $key
     *
     * @return array
     */
    public function hValues(string $key): array
    {
        return Redis::hvals($key);
    }

    /************** List ***************/

    /**
     * 将一个或多个值value插入到列表key的表头。如果key不存在,则创建
     * 如果有多个value值，那么各个value值按从左到右的顺序依次插入到表头
     * 也就是说 插入 a b c d ，则是 d c b a.
     *
     * @param string $key
     * @param string ...$value
     *
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function lPush(string $key, string ...$value)
    {
        return Redis::lpush($key, ...$value);
    }

    /**
     * 将值value插入到列表key的表头，当且仅当key存在并且是一个列表(当key不存在时,则无效).
     *
     * @param string $key
     * @param string $value
     *
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function lPushX(string $key, string $value)
    {
        return Redis::lpushx($key, $value);
    }

    /**
     * 将一个或多个值value插入到列表key的表尾,如果key不存在,则创建
     * 如果有多个value值，那么各个value值按从左到右的顺序依次插入到表尾
     * 也就是说 插入 a b c d ,则 a b c d.
     *
     * @param string $key
     * @param string ...$value
     *
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function rPush(string $key, string ...$value)
    {
        return Redis::rpush($key, ...$value);
    }

    /**
     * 将值value插入到列表key的表尾,当且仅当key存在并且是一个列表(当key不存在，则无效).
     *
     * @param string $key
     * @param string $value
     *
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function rPushX(string $key, string $value)
    {
        return Redis::rpushx($key, $value);
    }

    /**
     * 移除并返回列表key的头元素.
     *
     * @param string $key
     *
     * @return mixed 如果返回false则代表该key不存在或不是list类型
     */
    public function lPop(string $key)
    {
        return Redis::lpop($key);
    }

    /**
     * 移除并返回列表key的尾元素.
     *
     * @param string $key
     *
     * @return mixed 如果返回false则代表该key不存在或不是list类型
     */
    public function rPop(string $key)
    {
        return Redis::rpop($key);
    }

    /**
     * 是 lpop 命令的阻塞版本 ，移除并返回列表key的头元素，多个key时候则先后顺序依次检查，即 key1,key2,key3
     * 【使用方法 bLPop(5,'key1','key2','key3')】.
     *
     * @param int    $timeout (单位秒) 0表示阻塞时间可以无限期延长，意思是没有数据的时候等待执行时间
     * @param string ...$key
     *
     * @return mixed 返回null不存在key或者key不是list类型
     */
    public function bLPop(int $timeout, string ...$key)
    {
        return Redis::blpop($key, $timeout);
    }

    /**
     * 是 rpop 命令的阻塞版本，移除并返回列表key的尾元素，多个key时候则先后顺序依次检查，即 key1,key2,key3
     * 【使用方法 bRPop(5,'key1','key2','key3')】.
     *
     * @param int    $timeout (单位秒) 0表示阻塞时间可以无限期延长，意思是没有数据的时候等待执行时间
     * @param string ...$key
     *
     * @return mixed 返回null不存在key或者key不是list类型
     */
    public function bRPop(int $timeout, string ...$key)
    {
        return Redis::brpop($key, $timeout);
    }

    /**
     * 返回list的长度.
     *
     * @param string $key
     *
     * @return int
     */
    public function lLen(string $key): int
    {
        if (3 === $this->type($key)) {
            return (int) Redis::llen($key);
        }

        return 0;
    }

    /**
     * 返回列表key中指定区间内的元素，区间以偏移量start和stop指定 ，索引从0开始
     * 如果 -1表示列表的最后一个元素，-2表示列表的倒数第二个元素.
     *
     * @param string $key
     * @param int    $start
     * @param int    $stop
     *
     * @return array
     */
    public function lRange(string $key, int $start, int $stop): array
    {
        $res = Redis::lrange($key, $start, $stop);

        return !$res ? [] : $res;
    }

    /**
     * 根据参数count的值，移除列表中与参数value相等的元素.
     *
     * @param string $key
     * @param int    $count 移除数量为count的绝对值，count正数则从头到尾搜索，负数则从尾到头搜索
     * @param string $value
     *
     * @return int 被移除元素的数量（不存在的key则是0）
     */
    public function lRem(string $key, int $count, string $value): int
    {
        return (int) Redis::lrem($key, $count, $value);
    }

    /**
     * 将列表key下标为index的元素的值设置为为value.
     *
     * @param string $key
     * @param int    $index
     * @param string $value
     *
     * @return bool
     */
    public function lSet(string $key, int $index, string $value): bool
    {
        return Redis::lset($key, $index, $value);
    }

    /**
     * 让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除
     * 以0表示列表的第一个元素，以1表示列表的第二个元素，以此类推。
     * 以-1表示列表的最后一个元素，-2表示列表的倒数第二个元素，以此类推.
     *
     * @param string $key
     * @param int    $start 开始
     * @param int    $stop  结束
     *
     * @return bool
     */
    public function lTrim(string $key, int $start, int $stop): bool
    {
        return Redis::ltrim($key, $start, $stop);
    }

    /**
     * 返回列表key中，下标为index的元素.
     *
     * @param string $key
     * @param int    $index
     *
     * @return mixed 返回false则获取失败的
     */
    public function lIndex(string $key, int $index)
    {
        return Redis::lindex($key, $index);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之前.
     *
     * @param string $key
     * @param string $pivot
     * @param string $value
     *
     * @return mixed 返回插入操作完成之后，list的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0，如果返回false则不是list类型
     */
    public function lInsertBefore(string $key, string $pivot, string $value)
    {
        return Redis::linsert($key, 'BEFORE', $pivot, $value);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之后.
     *
     * @param string $key
     * @param string $pivot
     * @param string $value
     *
     * @return mixed 返回插入操作完成之后，list的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0，如果返回false则不是list类型
     */
    public function lInsertAfter(string $key, string $pivot, string $value)
    {
        return Redis::linsert($key, 'AFTER', $pivot, $value);
    }

    /**
     * 将列表{$sourceKey}中的最后一个元素(尾元素)弹出，并插入到列表{$destinationKey}里作为头元素
     * 如果{$sourceKey}和{$destinationKey}相同，则列表中的表尾元素被移动到表头，并返回该元素
     * 假如{$sourceKey}列表有元素a, b, c , d {$destinationKey}列表有元素x, y, z,执行完之后{$sourceKey}就变成a, b, c，而{$destinationKey}就变成d, x, y, z.
     *
     * @param string $sourceKey
     * @param string $destinationKey
     *
     * @return mixed 返回{$sourceKey}弹出的元素，如果返回false，可能是{$sourceKey}与{$destinationKey}不是list类型或{$sourceKey}不存在
     */
    public function rPopLPush(string $sourceKey, string $destinationKey)
    {
        return Redis::rpoplpush($sourceKey, $destinationKey);
    }

    /**
     * 是rpoplpush的阻塞版本.
     *
     * @param string $sourceKey
     * @param string $destinationKey
     * @param int    $timeout        等待时间（单位秒），也就是说当列表{$sourceKey}为空时,等待执行时间。0表示阻塞时间可以无限期延长
     *
     * @return mixed
     */
    public function bRPopLPush(string $sourceKey, string $destinationKey, int $timeout)
    {
        return Redis::brpoplpush($sourceKey, $destinationKey, $timeout);
    }

    /************** Set ***************/

    /**
     * 将一个或多个member元素加入到集合key当中，已经存在于集合的{$member}元素将被忽略.
     *
     * @param string $key
     * @param string ...$member
     *
     * @return int|bool 返回添加到集合中的新元素的数量，如果返回false说明key不是Set类型
     */
    public function sAdd(string $key, string ...$member)
    {
        return Redis::sadd($key, ...$member);
    }

    /**
     * 移除集合key中的一个或多个member元素，不存在的member元素会被忽略.
     *
     * @param string $key
     * @param string ...$member
     *
     * @return int|bool 返回成功移除的数量，如果返回false说明key不是Set类型
     */
    public function sRem(string $key, string ...$member)
    {
        return Redis::srem($key, ...$member);
    }

    /**
     * 返回集合key中的所有成员.
     *
     * @param string $key
     *
     * @return array
     */
    public function sMembers(string $key): array
    {
        $res = Redis::smembers($key);

        return !$res ? [] : $res;
    }

    /**
     * 判断member元素是否是集合key的成员.
     *
     * @param string $key
     * @param string $member
     *
     * @return bool
     */
    public function sIsMember(string $key, string $member): bool
    {
        return Redis::sismember($key, $member);
    }

    /**
     * 返回集合中元素的数量.
     *
     * @param string $key
     *
     * @return int 没有该key护着key不是Set类型统一返回0
     */
    public function sCard(string $key): int
    {
        return (int) Redis::scard($key);
    }

    /**
     * 将member元素从{$sourceKey}集合移动到{$destinationKey}集合.
     *
     * @param string $sourceKey
     * @param string $destinationKey
     * @param string $member
     *
     * @return bool
     */
    public function sMove(string $sourceKey, string $destinationKey, string $member): bool
    {
        return Redis::smove($sourceKey, $destinationKey, $member);
    }

    /**
     * 移除并返回集合中的一个随机元素.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function sPop(string $key)
    {
        //return Redis::spop($key);
        return Redis::command('spop', (array) $key);
    }

    /**
     * 返回集合中的一个或多个随机成员元素,返回元素的数量由{$count}决定.
     *
     * @param string $key
     * @param int    $count 可为负数,当为负数的时候则返回个数是{$count}的绝对值，值可能会重复
     *
     * @return mixed
     */
    public function sRandMember(string $key, int $count)
    {
        return Redis::srandmember($key, $count);
    }

    /**
     * 该集合是所有给定集合的交集.
     *
     * @param string ...$key
     *
     * @return mixed
     */
    public function sInter(string ...$key)
    {
        return Redis::sinter($key);
    }

    /**
     * 该集合是所有给定集合的交集，结果保存到{$destinationKey}集合.
     *
     * @param string $destinationKey
     * @param string ...$key
     *
     * @return int 返回结果集中的成员数量
     */
    public function sInterStore(string $destinationKey, string ...$key): int
    {
        return (int) Redis::sinterstore($destinationKey, ...$key);
    }

    /**
     * 返回给定集合的并集.
     *
     * @param string ...$key
     *
     * @return mixed
     */
    public function sUnion(string ...$key)
    {
        return Redis::sunion($key);
    }

    /**
     * 返回给定集合的并集，结果保存到{$destinationKey}集合.
     *
     * @param string $destinationKey
     * @param mixed  ...$key
     *
     * @return int 返回结果集中的成员数量
     */
    public function sUnionStore(string $destinationKey, string ...$key): int
    {
        return (int) Redis::sunionstore($destinationKey, ...$key);
    }

    /**
     * 返回给定集合的差集.
     *
     * @param string ...$key
     *
     * @return mixed
     */
    public function sDiff(string ...$key)
    {
        return Redis::sdiff($key);
    }

    /**
     * 返回给定集合的差集,结果保存到{$destinationKey}集合.
     *
     * @param string $destinationKey
     * @param string ...$key
     *
     * @return int 返回结果集中的成员数量
     */
    public function sDiffStore(string $destinationKey, string ...$key): int
    {
        return (int) Redis::sdiffstore($destinationKey, ...$key);
    }

    /************** Sorted Set ***************/

    /**
     * 将一个member元素及其score值加入到有序集key当中.
     *
     * @param string $key
     * @param float  $score  排序索引
     * @param string $member 元素
     *
     * @return int|bool 返回被成功添加的新成员的数量，不包括那些被更新的、已经存在的成员，当key存在但不是有序集类型时返回false
     */
    public function zAdd(string $key, float $score, string $member)
    {
        return Redis::zadd($key, $score, $member);
    }

    /**
     * 添加多个元素 【使用方法：zAdds('key', 1, 'member1', 2, 'member2', 3, 'member3')】.
     *
     * @param string $key
     * @param mixed  ...$scoreMember
     *
     * @return int 被成功添加的新成员的数量
     */
    public function zAdds(string $key, ...$scoreMember)
    {
        return Redis::zadd($key, ...$scoreMember);
    }

    /**
     * 移除有序集key中的一个或多个成员，不存在的成员将被忽略.
     *
     * @param string $key
     * @param string ...$member
     *
     * @return int 返回成功移除个数
     */
    public function zRem(string $key, string ...$member): int
    {
        return (int) Redis::zrem($key, ...$member);
    }

    /**
     * 返回有序集key的数量.
     *
     * @param string $key
     *
     * @return int
     */
    public function zCard(string $key): int
    {
        return (int) Redis::zcard($key);
    }

    /**
     * 返回score值在min和max之间的成员的数量
     * score值在min和max之间(默认包括score值等于min或max)的成员.
     *
     * @param string $key
     * @param float  $min
     * @param float  $max
     *
     * @return int
     */
    public function zCount(string $key, float $min, float $max): int
    {
        return (int) Redis::zcount($key, $min, $max);
    }

    /**
     * 返回有序集key中，成员{$member}的score值
     *
     * @param string $key
     * @param string $member
     *
     * @return mixed 如果member元素不是有序集key的成员，或key不存在，返回 null
     */
    public function zScore(string $key, string $member)
    {
        $result = Redis::zscore($key, $member);

        return false === $result ? null : (float) $result;
    }

    /**
     * 为有序集key的成员{$member}的score值加上增量{$increment}.
     *
     * @param string $key
     * @param string $member
     * @param float  $increment 如果是负数则是减量
     *
     * @return mixed 返回成员的新score值，如果返回false则是失败的
     */
    public function zIncrBy(string $key, string $member, float $increment)
    {
        return Redis::zincrby($key, $increment, $member);
    }

    /**
     * 返回有序集key中，指定区间内的成员
     * 其中成员的位置按score值递增(从小到大)来排序。
     *
     * @param string $key
     * @param float  $start        为负数则是倒数
     * @param float  $stop         为负数则是倒数
     * @param bool   $isWithScores 是否也一起返回 score 的值
     *
     * @return array 指定区间内，带有score值(可选)的有序集成员的列表
     */
    public function zRange(string $key, float $start, float $stop, bool $isWithScores = false): array
    {
        $result = Redis::zrange($key, $start, $stop, $isWithScores);

        return !$result ? [] : $result;
    }

    /**
     * 返回有序集key中，指定区间内的成员。
     * 其中成员的位置按score值递减(从大到小)来排列。
     *
     * @param string $key
     * @param float  $start
     * @param float  $stop
     * @param bool   $isWithScores
     *
     * @return mixed
     */
    public function zRevRange(string $key, float $start, float $stop, bool $isWithScores = false): array
    {
        $result = Redis::zrevrange($key, $start, $stop, $isWithScores);

        return !$result ? [] : $result;
    }

    /**
     * 返回有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。
     * 有序集成员按score值递增(从小到大)次序排列。
     * {$offset}与{$count}参数类似sql语句的 SELECT LIMIT offset, count.
     *
     * @param string $key
     * @param float  $min
     * @param float  $max
     * @param bool   $isWithScores
     * @param int    $offset
     * @param int    $count
     *
     * @return array
     */
    public function zRangeByScore(string $key, float $min, float $max, bool $isWithScores = false, int $offset = 0, int $count = -1): array
    {
        $result = Redis::zrangebyscore($key, $min, $max, ['withscores' => $isWithScores, 'limit' => ['offset' => $offset, 'count' => $count]]);
        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * 返回有序集key中，score值介于max和min之间(默认包括等于max或min)的所有的成员。
     * 有序集成员按score值递减(从大到小)的次序排列。
     * {$offset}与{$count}参数类似sql语句的 SELECT LIMIT offset, count.
     *
     * @param string $key
     * @param float  $min
     * @param float  $max
     * @param bool   $isWithScores
     * @param int    $offset
     * @param int    $count
     *
     * @return array
     */
    public function zRevRangeByScore(string $key, float $min, float $max, bool $isWithScores = false, int $offset = 0, int $count = -1): array
    {
        $result = Redis::zrevrangebyscore($key, $max, $min, ['withscores' => $isWithScores, 'limit' => ['offset' => $offset, 'count' => $count]]);
        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * 返回有序集key中成员member的排名。其中有序集成员按score值递增(从小到大)顺序排列。
     *
     * @param string $key
     * @param string $member
     *
     * @return int|null 如果member是有序集key的成员，返回member的排名。如果member不是有序集key的成员，返回null
     */
    public function zRank(string $key, string $member)
    {
        $result = Redis::zrank($key, $member);
        if (false === $result) {
            return null;
        }

        return (int) $result;
    }

    /**
     * 返回有序集key中成员member的排名。其中有序集成员按score值递减(从大到小)排序。
     *
     * @param string $key
     * @param string $member
     *
     * @return int|null 如果member是有序集key的成员，返回member的排名。如果member不是有序集key的成员，返回null
     */
    public function zRevRank(string $key, string $member)
    {
        $result = Redis::zrevrank($key, $member);

        return false === $result ? null : (int) $result;
    }

    /**
     * 移除有序集key中，指定排名(rank)区间内的所有成员。
     * 区间分别以下标参数start和stop指出，包含start和stop在内。
     *
     * @param string $key
     * @param float  $start
     * @param float  $stop
     *
     * @return int 被移除成员的数量
     */
    public function zRemRangeByRank(string $key, float $start, float $stop): int
    {
        return (int) Redis::zremrangebyrank($key, $start, $stop);
    }

    /**
     * 移除有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。
     *
     * @param string $key
     * @param float  $min
     * @param float  $max
     *
     * @return int 被移除成员的数量
     */
    public function zRemRangeByScore(string $key, float $min, float $max): int
    {
        return (int) Redis::zremrangebyscore($key, $min, $max);
    }

    /**
     * 计算给定的一个或多个有序集的交集【使用方法 zInterStore("destinationKey", ['z_set_1', 'z_set_2']】
     * 保存到$destinationKey.
     *
     * @param string $destinationKey
     * @param array  $key
     *
     * @return int 返回{$destinationKey}的结果集的数量
     */
    public function zInterStore(string $destinationKey, array $key): int
    {
        return (int) Redis::zinterstore($destinationKey, $key);
    }

    /**
     * 计算给定的一个或多个有序集的并集【使用方法 zUnionStore("destinationKey", ['z_set_1', 'z_set_2']】
     * 保存到$destinationKey.
     *
     * @param string $destinationKey
     * @param array  $key
     *
     * @return int 返回{$destinationKey}的结果集的数量
     */
    public function zUnionStore(string $destinationKey, array $key): int
    {
        return (int) Redis::zunionstore($destinationKey, $key);
    }
}
