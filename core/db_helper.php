<?php

class DBHelper
{

    public static function filter($filter, &$bind, $table = null)
    {
        $sql = '';
        foreach ($filter as $k => $v) {
            if ($v === null) {
                continue;
            }
            if ($table !== null && $v['table'] !== $table) {
                continue;
            }
            $sql .= ($sql === ''? ' ' : ' AND ')
                . $v['expr'];
            $bind = array_merge($bind, $v['bind']);
        }
        return ($sql === '' ? '' : ' WHERE') . $sql;
    }

    public static function sort($sort)
    {
        $sql = '';
        foreach ($sort as $k => $v) {
            $sql .= ($sql === ''? '' : ', ') . $k . ' ' . ($v ? 'DESC' : 'AES');
        }
        return ' ORDER BY ' . $sql;
    }

    public static function range($range)
    {
        return ' LIMIT ' . $range[1] . ' OFFSET ' . $range[0];
    }
}
