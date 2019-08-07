<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";

abstract class API {
    public static $DB;
    public static $Tables = [];
    public static $schema;
            
    static function __callStatic($func, $params) {
        if(empty(API::$DB)) {
            API::constructor();
        }
        if(in_array($func, self::$Tables)){
            self::$DB->setTable($func);
            if(isset($params)) {
                $select = empty($params[0]) ? "*" : $params[0];
                $where = empty($params[1]) ? NULL : $params[1];
                $group = empty($params[2]) ? NULL : $params[2];
                $order = empty($params[3]) ? NULL : $params[3];
            }
            return self::$DB->select($select, $where, $group, $order);
        } else {
            return false;
        }
    }
    
    public static function _constructor() {
        self::$schema = "ImageDB";
        self::$DB = new FuzzyKnights();
        self::$DB->setSchema(API::$schema);
        self::$DB->setFetchAssoc();
        foreach(self::$DB->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE '" . API::$schema . "' ORDER BY TABLE_NAME") as $record) {
            self::$Tables[] = $record["TABLE_NAME"];
        }
    }
    
    public static function _preset($key, $params = []) {
        return self::$DB->quick($key, $params);
    }
    
    public static function query($query, $params = []) {
        return self::$DB->query($query, $params);
    }
    
    public static function parse($query, $params = []) {
        return self::$DB->replaceParams($query, $params);
    }
}

API::_constructor();