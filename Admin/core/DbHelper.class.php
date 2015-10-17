<?php
/**
 * Created by PhpStorm.
 * User: zhangedward
 * Date: 14-6-20
 * Time: 上午11:45
 */

class DbHelper {
    private $mysqli;
    private static $_instance;

    private  function __construct(){
        $this->mysqli = new MySQLi(DB_HOST, DB_USER, DB_PW, DB_NAME);
        if ($this->mysqli->connect_error){
            die('Connect Error (' . $this->mysqli->connect_errno . ')' . $this->mysqli->connect_error);
            $this->mysqli = null;
            unset($this->mysqli);
        }
        $this->mysqli->options(MYSQLI_CLIENT_INTERACTIVE, 3592);
        $this->mysqli->set_charset("utf8");
    }

    public static function getInstance(){
        if (!(self::$_instance instanceof self)){
            self::$_instance = new DbHelper();
        }

        return self::$_instance;
    }

    public function __destruct(){
        $this->mysqli->close();
        unset($this->mysqli);
    }

    public function close(){
        $this->mysqli->close();
    }

    public function get_one($sql){
        $resultArray = false;

        $result = $this->mysqli->query($sql);
        if ($result instanceof mysqli_result){
            $resultArray = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
        }

        return $resultArray;
    }

    public function query($sql){
        $result = $this->mysqli->query($sql);
        if ($result instanceof mysqli_result){
            $result->free();
            return true;
        }
        else{
            return $result;
        }
    }

    public function queryNoReturen($sql){
        $result = $this->mysqli->query($sql);
        if ($result instanceof mysqli_result){
            $result->free();
        }
    }

    public  function get_all($sql){
        $resultArray = false;

        $result = $this->mysqli->query($sql);
        if ($result instanceof mysqli_result){
            for ($resultArray = array(); $tmp = $result->fetch_array(MYSQLI_ASSOC);)
                $resultArray[] = $tmp;
            $result->free();
        }

        return $resultArray;
    }

    public function insert($table, $dataArray){
        $field = "";
        $value = "";
        if( !is_array($dataArray) || count($dataArray)<=0) {
            return false;
        }
        while(list($key,$val)=each($dataArray)) {
            $field .= "$key,";
            $value .= "'$val',";
        }
        $field = substr( $field, 0, -1);
        $value = substr( $value, 0, -1);
        $sql = "insert into $table($field) values($value)";
        return $this->query($sql);
    }

    public function update($table, $dataArray, $condition){
        if(!is_array($dataArray) || count($dataArray) <= 0) {
            return false;
        }

        $value = "";
        while( list($key,$val) = each($dataArray)){
            $value .= "$key = '$val',";
        }
        $value .= substr( $value, 0, -1);
        $sql = "update $table set $value where $condition";
        return $this->query($sql);
    }

    public function delete($table, $condition) {
        if(empty($condition) ) {
            return false;
        }

        $sql = "delete from $table where $condition";
        return $this->query($sql);
    }

    public function updatecommodityprice($commid, $price, $pricetype, $specid) {
        return $this->query("call  mall_updatemommodityprice($commid, $price, $pricetype, $specid)");
    }

    public function procedureResturnArray($procedureName, $parameters){
        $procedureSql = $procedureName . "(";

        foreach($parameters as $item){
            if (is_string($item)){
                $procedureSql .= "'" . $item . "'";
            }
            else{
                $procedureSql .= $item;
            }

            $procedureSql .= ",";
        }

        $procedureSql = rtrim($procedureSql, ",");

        $procedureSql .= ")";

        $this->query("set names UTF8");

        $query = $this->mysqli->multi_query("call " . $procedureSql);
        $i = 0;
        $rt = array();
        do{
            if ($result = $this->mysqli->store_result()){
                while($row = $result->fetch_array()) {
                    $rt[$i++] = $row;
                }
                $result->free();
            }
        }while($this->mysqli->next_result());

        if ($query instanceof mysqli_result){
            $query->free();
        }

        return $rt;
    }

    //开始事务
    public function begin_transaction(){
        $this->mysqli->autocommit(false);
        return $this->mysqli->begin_transaction();
    }

    //提交事务
    public function commit(){
        return $this->mysqli->commit();
    }

    //回滚事务
    public function rollback(){
        return $this->mysqli->rollback();
    }

    //结束事务
    public function end_transaction(){
        return $this->mysqli->autocommit(true);
    }
} 