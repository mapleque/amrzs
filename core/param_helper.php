<?php

function callback($debug, $value, $args)
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
