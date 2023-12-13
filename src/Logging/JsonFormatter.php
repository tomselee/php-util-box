<?php

namespace UtilBox\Logging;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

/**
 *  自定义日志Json格式化.
 */
class JsonFormatter extends BaseJsonFormatter
{
    /**
     * 字符限定异常的长度.
     */
    const EXCEPTION_LEN = 50;

    /**
     * 重写格式化日志方法.
     *
     * @param array $record
     *
     * @return string
     */
    public function format(array $record)
    {
        $newRecord = $this->logTransform($record);

        return $this->toJson($this->normalize($newRecord), true) . ($this->appendNewline ? "\n" : '');
    }

    /**
     * 日志转换.
     *
     * @param array $record
     *
     * @return array
     */
    private function logTransform(array $record)
    {
        // 这个就是最终要记录的数组
        $record['context']['message'] = $record['message'];
        $newRecord                    = [
            'log_time'  => $record['datetime']->format('Y-m-d H:i:s'),
            'message'   => strlen($record['message']) < self::EXCEPTION_LEN ? $record['message'] : 'exception', // 捕获异常情况日志logTag
        ];
        unset($record['message'], $record['datetime']);

        return array_merge($newRecord, $record);
    }
}
