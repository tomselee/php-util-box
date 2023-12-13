<?php

namespace UtilBox\LaravelUtils;

use Illuminate\Contracts\Validation\Factory;

trait Validation
{
    /**
     * 验证方法
     *
     * @param array $params
     * @param array $rules
     * @param array $messages
     * @return array
     */
    public function validateParams(array $params = [], array $rules = [], array $messages = [])
    {
        $validator = app(Factory::class)->make($params, $rules, $messages);

        return $validator->validate();
    }
}
