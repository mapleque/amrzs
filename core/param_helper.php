<?php

function trans_filter($debug, &$param, $ruleInfo, $parentKey)
{
    $table = $ruleInfo['trans_args'][0];
    $alias = $ruleInfo['trans_args'][1];
    $row = $alias === null ? $parentKey : $alias;
    $field = $table === null ? $row : ( $table . '.' . $row);
    $expr = $field . ' = ?';
    $bind = [ $param ];
    if (strpos($ruleInfo['check'], 'subset') !== false) {
        $expr = $field . ' IN (' . str_repeat('?', count($param)) . ')';
        $bind = $param;
    } elseif (strpos($ruleInfo['check'], 'range') !== false) {
        $expr = $field . ' >= ? AND ' . $field . ' <= ?';
        $bind = $param;
    }
    $param = [
        'bind' => $bind,
        'expr' => $expr,
        'table' => $table,
        'alias' => $alias,
        'key' => $parentKey,
        'value' => $param,
    ];
}

function decode_filter($filter)
{
    $ret = [];
    foreach ($filter as $key => $value) {
        if (isset($value)) {
            $ret[$key] = $value['value'];
        }
    }
    return $ret;
}

function check_callback($debug, $value, $args)
{
    $ret = $args[0]($value);
    if (!$ret && $debug) {
        dump([
            'msg' => 'check func fail',
            'value' => $value,
        ]);
    }
    return $ret;
}

function check_string($debug, $value, $args)
{
    if (!is_string($value)) {
        if ($debug) {
            dump([
                'msg' => 'check string fail',
                'reason' => 'value is not string',
                'value' => $value,
            ]);
        }
        return false;
    }
    $len = strlen($value);
    $min = (int)$args[0];
    $max = (int)$args[1];
    if ($min >= 0 && $len < $min) {
        if ($debug) {
            dump([
                'msg' => 'check string fail',
                'min' => $min,
                'max' => $max,
                'value' => $value,
            ]);
        }
        return false;
    }
    if ($max >= 0 && $len > $max) {
        if ($debug) {
            dump([
                'msg' => 'check string fail',
                'min' => $min,
                'max' => $max,
                'value' => $value,
            ]);
        }
        return false;
    }
    return true;
}

function check_int($debug, $value, $args)
{
    if (!is_int($value)) {
        if ($debug) {
            dump([
                'msg' => 'check int fail',
                'reason' => 'value is not int',
                'value' => $value,
            ]);
        }
        return false;
    }
    $min = (int)$args[0];
    $max = (int)$args[1];
    if ($min >= 0 && $value < $min) {
        if ($debug) {
            dump([
                'msg' => 'check int fail',
                'min' => $min,
                'max' => $max,
                'value' => $value,
            ]);
        }
        return false;
    }
    if ($max >= 0 && $value > $max) {
        if ($debug) {
            dump([
                'msg' => 'check int fail',
                'min' => $min,
                'max' => $max,
                'value' => $value,
            ]);
        }
        return false;
    }
    return true;
}

function check_bool($debug, $value, $args)
{
    return $value === 1 || $value === 0;
}

function check_id($debug, $value, $args)
{
    return is_int($value) && $value > 0;
}

function check_enum($debug, $value, $args)
{
    $constants = get_defined_constants(true)['user'];
    $prefix = $args[0];
    foreach ($constants as $k => $v) {
        if (strpos($k, $prefix) === 0 && $v === $value) {
            return true;
        }
    }
    if ($debug) {
        dump([
            'msg' => 'check enum fail',
            'prefix' => $prefix,
            'value' => $value,
        ]);
    }
    return false;
}

function check_enum_subset($debug, $value, $args)
{
    $constants = get_defined_constants(true)['user'];
    $prefix = $args[0];
    if (!is_array($value)) {
        if ($debug) {
            dump([
                'msg' => 'check enum subset fail',
                'prefix' => $prefix,
                'value' => $value,
            ]);
        }
        return false;
    }

    foreach ($constants as $k => $v) {
        if (strpos($k, $prefix) === 0) {
            foreach ($value as $vk => $vv) {
                if ($v === $vv) {
                    unset($value[$vk]);
                }
            }
        }

    }
    if (empty($value)) {
        return true;
    }
    if ($debug) {
        dump([
            'msg' => 'check enum subset fail',
            'prefix' => $prefix,
            'value' => $value,
        ]);
    }
    return false;
}

function check_in($debug, $value, $args)
{
    $arr = $args[0];
    return in_array($value, $arr);
}

function check_subset($debug, $value, $args)
{
    $arr = $args[0];
    $intersect = array_uintersect_uassoc($arr, $value, function($a, $b){
        return $a === $b;
    }, function($a, $b){
        return true;
    });
    if (count($intersect) === count($value)) {
        return true;
    }
    if ($debug) {
        dump([
            'msg' => 'check subset fail',
            'arr' => $arr,
            'value' => $value,
        ]);
    }
    return false;
}

function check_sort($debug, $value, $args)
{
    $fields = $args[0];
    foreach ($value as $k => $v) {
        if (!in_array($k, $fields) || !is_bool($v)) {
            if ($debug) {
                dump([
                    'msg' => 'check sort fail',
                    'fields' => $fields,
                    'value' => $value,
                ]);
            }
            return false;
        }
    }
    return true;
}

function check_int_range($debug, $value, $args)
{
    if (is_array($value)
        && count($value) === 2
        && is_int($value[0])
        && is_int($value[1])
    ) {
        return true;
    }
    if ($debug) {
        dump([
            'msg' => 'check range fail',
            'value' => $value,
        ]);
    }
    return false;
}

function check_date_range($debug, $value, $args)
{
    $dateFormat = '/^\d{4}-\d{2}-\d{2}$/';
    if (is_array($value)
        && count($value) === 2
        && is_string($value[0])
        && is_string($value[1])
        && preg_match($dateFormat, $value[0])
        && preg_match($dateFormat, $value[1])
    ) {
        return true;
    }
    if ($debug) {
        dump([
            'msg' => 'check date range fail',
            'value' => $value,
        ]);
    }
    return false;
}
