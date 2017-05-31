<?php

class Param
{
    private static $runtime_cache = [];

    // expr define
    // const IS_STRING = '|@is_string|';
    // const IS_INT = '|@is_int|';
    const IS_INT_RANGE = '|@check_int_range|';
    const IS_DATE_RANGE = '|@check_date_range|';

    /**
     * @param mixed $callback
     * @return string <expr>
     */
    public static function func($callback)
    {
        return self::wrap('|@callback:$|', $callback);
    }

    /**
     * @param int $min -1 for not limit
     * @param int $max -1 for not limit
     * @return string <expr>
     */
    public static function isString($min = -1, $max = -1)
    {
        return self::wrap('|@check_string:$,$|', $min, $max);
    }

    /**
     * @param int $min -1 for not limit
     * @param int $max -1 for not limit
     * @return string <expr>
     */
    public static function isInt($min = -1, $max = -1)
    {
        return self::wrap('|@check_int:$,$|', $min, $max);
    }

    public static function isSort($fields)
    {
        return self::wrap('@check_sort:$', $fields);
    }

    /**
     * @param string $expr
     * @param ...
     * @return string <expr>
     */
    private static function wrap($expr)
    {
        $num = func_num_args();
        $needle = substr_count($expr, '$');
        assert($num > 1 && $num - 1 == $needle, 'wrong usage of wrap method');
        for ($i = 1; $i < $num; $i++) {
            $idx = count(self::$runtime_cache);
            self::$runtime_cache[] = func_get_arg($i);
            $expr = preg_replace('/\$/', $idx, $expr, 1);
        }
        return $expr;
    }

    /**
     * @param array $rule
     * @param array $param
     */
    public static function checkAndDie($rule, $param, $debug = false)
    {
        $status = self::check($rule, $param, $debug);
        if ($status != ERROR_SUCCESS) {
            Base::dieWithError($status);
        }
    }

    /**
     * @param $rule
     * @param $param
     * @return int =0 success
     *             >0 error status
     */
    public static function check($rule, $param, $debug = false)
    {
        if ($debug) {
            dump([
                'msg' => 'following will be check',
                'rule' => $rule,
                'param' => $param,
            ]);
        }
        if (is_array($rule)) {
            if (!is_array($param)) {
                return ERROR_INVALID_REQUEST;
            }
            foreach ($rule as $key => $value) {
                $status = self::check($value, $param[$key], $debug);
                if ($status != ERROR_SUCCESS) {
                    return $status;
                }
            }
        } elseif (is_string($rule)) {
            $rule_info = self::process($rule);

            // a lost a must param
            if (!$rule_info['optional'] && !isset($param)) {
                if ($debug) {
                    dump([
                        'msg' => 'check faild',
                        'rule' => $rule,
                        'value' => $param,
                        'rule_info' => $rule_info,
                    ]);
                }
                return $rule_info['code'];
            }

            // a function check
            if ($rule_info['func'] !== null) {
                if (!$rule_info['func']($debug, $param, $rule_info['args'])) {
                    return $rule_info['code'];
                }
            }

        }
        return ERROR_SUCCESS;
    }

    /**
     * @param string $rule
     * @return int =0 success
     *             >0 error status
     */
    private static function process($rule)
    {
        $rule_info = [
            'optional' => false,
            'func' => null,
            'args' => [],
            'code' => ERROR_SUCCESS,
        ];
        foreach (explode('|', $rule) as $expr) {
            if ($expr == '') {
                continue;
            }
            switch ($expr[0]) {
            case 'o':
                $rule_info['optional'] = true;
                break;
            case '@':
                $wrap_pos = strpos($expr, ':');
                if ($wrap_pos !== false) {
                    $rule_info['func'] = substr($expr, 1, $wrap_pos - 1);
                    foreach (explode(',', substr($expr, $wrap_pos + 1)) as $idx) {
                        $rule_info['args'][] = self::$runtime_cache[(int)$idx];
                    }
                } else {
                    $rule_info['func'] = substr($expr, 1);
                }
                break;
            default :
                $rule_info['code'] = (int)$expr;
            }
        }
        return $rule_info;
    }

}
