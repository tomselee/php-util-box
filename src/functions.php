<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Redis\Connections\PhpRedisConnection;

if (!function_exists('m_time')) {
    /**
     * 返回毫秒级时间戳.
     */
    function m_time()
    {
        list($usec, $sec) = explode(' ', microtime());

        return (float) sprintf('%.0f', (floatval($usec) + floatval($sec)) * 1000);
    }
}

if (!function_exists('parse_name')) {
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     *
     * @param string $name    字符串
     * @param int    $type    转换类型
     * @param bool   $ucfirst 首字母是否大写（驼峰规则）
     *
     * @return string
     */
    function parse_name(string $name, $type = 0, bool $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        } else {
            return strtolower(trim(preg_replace('/[A-Z]/', '_\\0', $name), '_'));
        }
    }
}

if (!function_exists('get_csv_lines')) {
    /**
     * 获取csv文件总行数.
     *
     * @param $filePath
     *
     * @return int
     */
    function get_csv_lines($filePath)
    {
        $obj = new SplFileObject($filePath, 'r');
        $obj->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $obj->seek(PHP_INT_MAX);
        $current = trim($obj->current());
        $count   = $obj->key();
        if ($current) {
            ++$count;
        }

        return $count;
    }
}

if (!function_exists('read_csv_file')) {
    /**
     * 读取csv生成迭代器.
     *
     * @param string $filePath
     *
     * @return Generator
     */
    function read_csv_file(string $filePath)
    {
        $handler = new SplFileObject($filePath, 'r');
        $handler->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE
        );

        $detectCharacter = ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'];
        $encoding        = null;
        foreach ($handler as $row) {
            if (!$encoding) {
                $encoding = mb_detect_encoding($row[0], $detectCharacter);
            }

            yield array_map('trim', array_map(function ($item) use ($encoding, $detectCharacter) {
                try {
                    return iconv($encoding ?: 'GB2312', 'UTF-8', $item);
                } catch (Throwable $exception) {
                    // 若其他单元的格式编码跟第一个不一样，则iconv函数报错，需要重新获取单元内容编码
                    return iconv(mb_detect_encoding($item, $detectCharacter), 'UTF-8', $item);
                }
            }, $row));
        }
        // 释放文件句柄
        $handler = null;
    }
}

if (!function_exists('error_msg')) {
    /**
     * 错信信息.
     *
     * @param Throwable $e
     * @param int       $count
     * @param string[]  $contents
     *
     * @return array
     */
    function error_msg(Throwable $e, $count = 3, $contents = ['file', 'line'])
    {
        $stackTrace = collect(array_slice((array) $e->getTrace(), 0, $count))->map(function ($item) use ($contents) {
            return Arr::only($item, $contents);
        })->toArray();

        return $errorMessage = [
            'class' => get_class($e),
            'msg'   => $e->getMessage(),
            'code'  => $e->getCode(),
            'line'  => $e->getLine(),
            'file'  => $e->getFile(),
            'trace' => $stackTrace,
        ];
    }
}

if (!function_exists('redis')) {
    /**
     * 获取redis连接实例.
     *
     * @param string $collectionName
     *
     * @return Connection|PhpRedisConnection
     */
    function redis($collectionName = '')
    {
        return \Illuminate\Support\Facades\Redis::connection($collectionName);
    }
}

if (!function_exists('f_date')) {
    /**
     * 时间转换.
     *
     * @param $time
     *
     * @return false|string
     */
    function f_date($time)
    {
        if (!is_numeric($time)) {
            return $time;
        }

        return $time ? date('Y-m-d H:i:s', $time) : '';
    }
}

if (!function_exists('str_filter')) {
    /**
     * 字符串过滤.
     *
     * @param $string
     *
     * @return string|string[]
     */
    function str_filter($string)
    {
        return str_replace([
            "\r\n",
            "\r",
            "\n",
            "\t",
        ], '', trim($string));
    }
}

if (!function_exists('debug_sql')) {
    /**
     * 调试sql.
     *
     * @param callable $state
     * @param bool     $dd       是否立刻打印
     * @param bool     $withTime
     *
     * @return array
     */
    function debug_sql(callable $state, bool $dd = true, bool $withTime = true)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $state();
        $queryLog = DB::getQueryLog();
        DB::rollBack();
        DB::disableQueryLog();
        $res = collect($queryLog)->map(function ($item) use ($withTime) {
            $str = Str::replaceArray('?', $item['bindings'], $item['query']);
            $str .= $withTime ? ' /*' . $item['time'] . 'ms*/' : '';

            return $str;
        })->toArray();
        $dd && dd($res);

        return $res;
    }
}

if (!function_exists('app_storage_path')) {
    /**
     * 获取本地存储路径.
     *
     * @param $path
     *
     * @return bool
     */
    function app_storage_path($path)
    {
        $path = trim(parse_url($path, PHP_URL_PATH), '/');

        return storage_path('app/' . $path);
    }
}

if (!function_exists('export_csv')) {
    /**
     * 下载csv.
     *
     * @param $file_name string 文件名
     * @param array|Generator $data        数据
     * @param array           $header
     * @param array           $basicHeader
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function export_csv(string $file_name, $data, array $header = [], array $basicHeader = [])
    {
        $handle = fopen('php://output', 'w');
        $header && fputcsv($handle, numeric2str($header));
        foreach ($data as $value) {
            if (is_array($value) && is_array(current($value))) {
                foreach ($value as $row) {
                    fputcsv($handle, numeric2str($row));
                }
            } else {
                fputcsv($handle, numeric2str($value));
            }
        }
        !isset($basicHeader['Content-Type']) &&
        $basicHeader['Content-Type']        = 'application/csv';
        !isset($basicHeader['Content-Disposition']) &&
        $basicHeader['Content-Disposition'] = 'attachment; filename=' . $file_name;

        return response('')->withHeaders($basicHeader);
    }
}

if (!function_exists('numeric2str')) {
    /**
     * 校验科学计数法.
     *
     * @param $data
     *
     * @return array
     */
    function numeric2str($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        foreach ($data as &$item) {
            if (is_numeric($item)) {
                $item = (string) $item;
                strlen($item) > 9 && $item .= "\t";
            }
        }

        return $data;
    }

    if (!function_exists('has_index')) {
        /**
         * 检测某个表中是否存在某个索引.
         *
         * @param $table
         * @param $name
         *
         * @return mixed
         */
        function has_index($table, $name)
        {
            $conn            = \Illuminate\Support\Facades\Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $doctrineTable   = $dbSchemaManager->listTableDetails($table);

            return $doctrineTable->hasIndex($name);
        }
    }
}

if (!function_exists('arr_dimension')) {
    /**
     * 获取数组维度.
     *
     * @param $array
     *
     * @return int
     *
     * @author litongzhi 2022/04/17 17:29
     */
    function arr_dimension($array)
    {
        if (is_array(current($array))) {
            return 1 + arr_dimension(current($array));
        }

        return 1;
    }
}

if (!function_exists('check_pdf')) {
    /**
     * 检测pdf是否完整.
     *
     * @author litongzhi 2022-6-20
     */
    function check_pdf($pdf)
    {
        return false !== strpos($pdf, '%%EOF');
    }
}

if (!function_exists('throw_if_str')) {
    /**
     * throw_if增强.
     *
     * @param $condition
     * @param $exception
     * @param mixed ...$parameters
     *
     * @return bool
     *
     * @throws Throwable
     */
    function throw_if_str($condition, $exception, ...$parameters)
    {
        if (is_string($exception)) {
            $message   = $exception;
            $exception = new \Symfony\Component\HttpKernel\Exception\HttpException(200, $message);
        }

        return throw_if($condition, $exception, ...$parameters);
    }
}
