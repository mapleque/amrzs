<?php

class Param
{
    private static $runtime_cache = [];

    const OPTIONAL = '|o|';

    // expr define
    const IS_ID = '|@check_id|';
    const IS_INT_RANGE = '|@check_int_range|';
    const IS_DATE_RANGE = '|@check_date_range|';
    const IS_BOOLEAN = '|@check_bool|';

    public static function filter($table = null, $alias = null)
    {
        return self::wrap('|#trans_filter:$,$|', $table, $alias);
    }

    /**
     * @param mixed $callback
     * @return string <expr>
     */
    public static function func($callback)
    {
        return self::wrap('|@check_callback:$|', $callback);
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
        return self::wrap('|@check_sort:$|', $fields);
    }

    public static function isEnum($prefix)
    {
        return self::wrap('|@check_enum:$|', $prefix);
    }

    public static function isEnumSubset($prefix)
    {
        return self::wrap('|@check_enum_subset:$|', $prefix);
    }

    public static function isIn($arr)
    {
        return self::wrap('|@check_in:$|', $arr);
    }

    public static function isSubset($arr)
    {
        return self::wrap('|@check_subset:$|', $arr);
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
     * @param bool $debug
     */
    public static function checkAndDie($rule, &$param, $debug = false)
    {
        $status = self::check($rule, $param, $debug);
        if ($status != ERROR_SUCCESS) {
            Base::dieWithError($status);
        }
    }

    /**
     * @param $rule
     * @param $param
     * @param $parentKey
     * @param $debug = false
     * @return int =0 success
     *             >0 error status
     */
    public static function check($rule, &$param, $parentKey = null, $debug = false)
    {
        if ($debug) {
            dump([
                'msg' => 'following will be check',
                'rule' => $rule,
                'param' => $param,
            ]);
        }
        if (is_array($rule)) {
            if (!$param) {
                $param = [];
            }
            if (!is_array($param)) {
                return ERROR_INVALID_REQUEST;
            }
            foreach ($rule as $key => $value) {
                if (array_key_exists($key, $param)) {
                    $status = self::check($value, $param[$key], $key, $debug);
                } else {
                    $tmp = null;
                    $status = self::check($value, $tmp, $key, $debug);
                    unset($tmp);
                }
                if ($status != ERROR_SUCCESS) {
                    return $status;
                }
            }
        } elseif (is_string($rule)) {
            $rule_info = self::process($rule);

            // lost a must param
            if ($rule_info['optional'] && !isset($param)) {
                return ERROR_SUCCESS;
            }

            // a function check
            if ($rule_info['check'] !== null) {
                if (!$rule_info['check']($debug, $param, $rule_info['args'])) {
                    return $rule_info['code'];
                }
            }

            // function transform
            if ($rule_info['trans'] !== null) {
                $rule_info['trans']($debug, $param, $rule_info, $parentKey);
            }

        }
        return ERROR_SUCCESS;
    }

    /**
     * @param string $rule
     * @return array
     */
    private static function process($rule)
    {
        $rule_info = [
            'optional' => false,
            'check' => null,
            'args' => [],
            'code' => ERROR_INVALID_REQUEST,
            'trans_args' => [],
            'trans' => null,
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
                    $rule_info['check'] = substr($expr, 1, $wrap_pos - 1);
                    foreach (explode(',', substr($expr, $wrap_pos + 1)) as $idx) {
                        $rule_info['args'][] = self::$runtime_cache[(int)$idx];
                    }
                } else {
                    $rule_info['check'] = substr($expr, 1);
                }
                break;
            case '#':
                $wrap_pos = strpos($expr, ':');
                if ($wrap_pos !== false) {
                    $rule_info['trans'] = substr($expr, 1, $wrap_pos - 1);
                    foreach (explode(',', substr($expr, $wrap_pos + 1)) as $idx) {
                        $rule_info['trans_args'][] = self::$runtime_cache[(int)$idx];
                    }
                } else {
                    $rule_info['trans'] = substr($expr, 1);
                }
                break;
            default :
                $rule_info['code'] = (int)$expr;
            }
        }
        return $rule_info;
    }

}
