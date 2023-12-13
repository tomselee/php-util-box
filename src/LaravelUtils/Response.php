<?php

namespace UtilBox\LaravelUtils;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

trait Response
{
    //返回给前端的
    public function successJson($data = null, string $message = 'success')
    {
        $res = [
            'code' => 0,
            'message' => $message
        ];

        if (!is_null($data)) {
            $res['data'] = $data;
        }

        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    //返回给前端的
    public function failJson(string $message = 'error', int $errCode = 600, $data = null)
    {
        $res = [
            'code' => $errCode,
            'message' => $message
        ];

        (!is_null($data)) && $res['data'] = $data;
        
        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /**
     * 组装报错信息
     *
     * @param Throwable $th
     * @return string
     */
    private function assembleLogInfo(Throwable $th): string
    {
        $logInfo =  '错误信息:' . $th->getMessage() . '。发生于:' . $th->getFile() . '(' . $th->getLine().')';
        return $logInfo;
    }
}
