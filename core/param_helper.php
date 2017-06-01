<?php

function trans_filter($debug, &$param, $ruleInfo)
{
    $table = $ruleInfo['trans_args'][0];
    $expr = '<$> = ?';
    $bind = [ $param ];
    if (strpos($ruleInfo['check'], 'subset') !== false) {
        $expr = '<$> IN (' . str_repeat('?', count($param)) . ')';
        $bind = $param;
    } elseif (strpos($ruleInfo['check'], 'range') !== false) {
        $expr = '<$> <= ? AND <$> >= ?';
        $bind = $param;
    }
    $param = [
        'table' => $table,
        'bind' => $bind,
        'expr' => $expr,
    ];
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

function check_sort($debug, $value, $args)
{
    return true;
}

function check_int_range()
{
    return true;
}

function check_date_range()
{
    return true;
}
