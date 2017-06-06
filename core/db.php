<?php

class DB
{
    public static function begin()
    {
        return self::getDB()->beginTransaction();
    }
    public static function commit($commit = true)
    {
        if (!$commit) {
            echo '[db rollback]' . "\n";
            print_callstack();
        }
        return self::getDB()->endTransaction($commit);
    }

    public static function select($query, $bind = null)
    {
        return self::getDB()->select($query, $bind);
    }

    /**
     * 统一format SQL，业务端只用关注数据
     * @param $tableName 更新表名
     * @param $setParams 更新数据
     * @param $whereCondition 更新条件
     */
    public static function componentSelect($tableName,$fileds,$whereCondition)
    {
        $whereSQL = [];
        $params = array_values($whereCondition);
        foreach ($whereCondition as $key => $value){
            $whereSQL[] = $key."=?";
        }
        $whereSQL = implode(" and ",$whereSQL);

        $query = sprintf("SELECT %s FROM %s WHERE %s",$fileds,$tableName,$whereSQL);
        return self::select($query,$params);
    }

    public static function insert($query, $bind = null)
    {
        return self::getDB()->insert($query, $bind);
    }

    /**
     * 统一format SQL，业务端只用关注数据
     * @param $tableName 更新表名
     * @param $setParams 更新数据
     * @param $whereCondition 更新条件
     */
    public static function componentInsert($tableName,$params)
    {
        $query = sprintf("INSERT INTO TABLE %s (%s) VALUES (%s)",
            $tableName, implode(",",array_keys($params)) , implode(",",array_fill(0,$params.length,"?"))
        );
        return self::update($query,$params);
    }

    public static function update($query, $bind = null)
    {
        return self::getDB()->exec($query, $bind);
    }

    /**
     * 统一format SQL，业务端只用关注数据
     * @param $tableName 更新表名
     * @param $setParams 更新数据
     * @param $whereCondition 更新条件
     */
    public static function componentUpdate($tableName,$setParams,$whereCondition)
    {
        $setSQL = "";
        $whereSQL = [];
        $params = [];
        foreach ($setParams as $key => $value){
            if($value == ""){
                continue;
            }
            $setSQL .= $key."=?,";
            $params[] = $value;
        }
        $setSQL = trim($setSQL,",");
        foreach ($whereCondition as $key => $value){
            $whereSQL[] = $key."=?";
            $params[] = $value;
        }
        $whereSQL = implode(" and ",$whereSQL);
        $query = sprintf("UPDATE %s SET %s WHERE %s",$tableName,$setSQL,$whereSQL);
        return self::update($query,$params);
    }

    public static function exec($query, $bind = null)
    {
        return self::getDB()->exec($query, $bind);
    }

    public static function delete($query, $bind = null)
    {
        return self::getDB()->exec($query, $bind);
    }

    private static function getDB()
    {
        if (self::$conn === null) {
            self::$conn = new DBConn(
                Important::DB_ADDR,
                Important::DB_USER,
                Important::DB_PASS,
                Important::DB_NAME,
                Important::DB_PORT
            );
        }
        return self::$conn;
    }

    private static $conn = null;
}
