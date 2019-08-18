<?php
    /*
     * To be added:
     * SELECT SERVERPROPERTY('ProductVersion') to get the current version of SQL server (Major.Minor.Build.Revision)
     * Major = 11 or greater allows for OFFSET X ROWS and FETCH NEXT X ROWS ONLY commands (Requires an ORDER BY statement, but can hack with ORDER BY (SELECT 0)
     * Add a functionality check to see if offset is allowed with these commands
     * TSQL:    QUOTENAME('string') should be applied to all queries if is SQL Server
     */

     /*
        echo "<pre>";
            print_r($query);
        echo "</pre>";
      */

    class Database {
        protected $info = Array(
            "driver" => "sqlsrv",
            "server" => "",
            "database" => "",
            "schema" => "",
            "table" => "",
            "user" => "",
            "password" => "",
        );
        protected $config = Array(
            "fetch" => PDO::FETCH_ASSOC
        );
        protected $queries = Array();
        protected $conn = null;
        
        public function __construct($driver, $server, $database, $user, $password) {
            $this->info["driver"] = $driver;
            $this->info["server"] = $server;
            $this->info["database"] = $database;
            $this->info["user"] = $user;
            $this->info["password"] = $password;
        }        
        public function __destruct() {
            try {
                $this->close();
            } catch (Exception $e) {}
        }
        
        
        public function setFetchAssoc() {
            $this->config["fetch"] = PDO::FETCH_ASSOC;
        }
        public function setFetchNum() {
            $this->config["fetch"] = PDO::FETCH_NUM;
        }
        public function setFetchBoth() {
            $this->config["fetch"] = PDO::FETCH_BOTH;
        }
        
        public function useDriver($driver) {
            $this->info["driver"] = $driver;
        }
        /**
         * Options
         * -----------------------------
         * ODBC
         * MS || SQL Server || Microsoft
         * OCI || Oracle
         * MY || MySQL
         * LITE | SQLite
         * POST || POSTGRE || PGSQL
         * DBLIB || Sybase || FreeTDS
         * IBM
         * Informix
         * Cubrid
         * Firebird
         * 4D
         * @param type $vendor
         */
        public function setDriver($vendor) {
            $vendor = strtoupper($vendor);
            if($vendor == "MS" || $vendor == "SQL SERVER" || $vendor == "MICROSOFT") {
                $this->info["driver"] = "sqlsrv";
            } else if($vendor == "ORACLE" || $vendor == "OCI") {
                $this->info["driver"] = "oci";
            } else if($vendor == "ODBC") {
                $this->info["driver"] = "odbc";
            } else if($vendor == "MYSQL" || $vendor == "MY") {
                $this->info["driver"] = "mysql";
            } else if($vendor == "SQLITE" || $vendor == "LITE") {
                $this->info["driver"] = "sqlite";
            } else if($vendor == "POST" || $vendor == "POSTGRE" || $vendor == "PGSQL") {
                $this->info["driver"] = "pgsql";
            } else if($vendor == "DBLIB" || $vendor == "SYBASE" || $vendor == "FREETDS") {
                $this->info["driver"] = "dblib";
            } else if($vendor == "IBM") {
                $this->info["driver"] = "ibm";
            } else if($vendor == "INFORMIX") {
                $this->info["driver"] = "informix";
            } else if($vendor == "CUBRID") {
                $this->info["driver"] = "cubrid";
            } else if($vendor == "FIREBIRD") {
                $this->info["driver"] = "firebird";
            } else if($vendor == "4D") {
                $this->info["driver"] = "firebird";
            }
        }
        public function getDriver() {
            return $this->info["driver"];
        }
        public function setServer($server) {
            $this->info["server"] = $server;
        }
        public function getServer() {
            return $this->info["server"];
        }
        public function setDatabase($database) {
            $this->info["database"] = $database;
        }
        public function getDatabase() {
            return $this->info["database"];
        }        
        public function setSchema($schema) {
            $this->info["schema"] = $schema;
        }
        public function getSchema() {
            return $this->info["schema"];
        }     
        public function setTable($table) {
            $this->info["table"] = $table;
        }
        public function getTable() {
            return $this->info["table"];
        }
        public function setUser($user) {
            $this->info["user"] = $user;
        }
        public function getUser() {
            return $this->info["user"];
        }
        public function setPassword($password) {
            $this->info["password"] = $password;
        }
        public function getPassword() {
            return $this->info["password"];
        }
        
        
        public function open() {
            $this->conn = new PDO("{$this->info["driver"]}:Server={$this->info["server"]};Database={$this->info["database"]}", $this->info["user"], $this->info["password"]);
        }
        public function close() {
            $this->conn = "";
        }
        /*public function __sleep() {
            return array("{$this->info["driver"]}:Server={$this->info["server"]};Database={$this->info["database"]}", $this->info["user"], $this->info["password"]);
        }
        public function __wakeup() {
            $this->open();
        }*/
        
        
        public function setQuick($key, $query) {
            $this->queries[$key] = $query;
        }
        public function getQuick($key) {
            return $this->queries[$key];
        }
        public function removeQuick($key) {
            unset($this->queries[$key]);
        }
        public function replaceAllQuick($array) {
            $this->queries = $array;
        }
        public function removeAllQuick() {
            $this->queries = Array();
        }
        public function quick($key, $params = []) {
            return $this->query($this->queries[$key], $params);
        }
        
        public function parse($select, $where = "", $group = "", $order = "") {
            $query = "SELECT {$select} FROM {$this->getSchema()}.{$this->getTable()}";
            if(!empty($where)) {
                $query .= " WHERE {$where}";
            }
            if(!empty($group)) {
                $query .= " GROUP BY {$group}";
            }
            if(!empty($order)) {
                $query .= " ORDER BY {$order}";
            }
            
            return $query;
        }
        
        /**
         * 0 - COLUMNS
         * 1 - TABLES
         * 2 - VIEWS
         * 3 - SCHEMATA
         * @param type $level
         * @param type $where
         * @return type
         */
        public function meta($level = 0, $select = "*", $where = NULL) {
            switch($level) {
                case 0:
                    return $this->query("SELECT {$select} FROM INFORMATION_SCHEMA.COLUMNS" . (!is_null($where) ? " WHERE {$where}" : ""));
                case 1:
                    return $this->query("SELECT {$select} FROM INFORMATION_SCHEMA.TABLES" . (!is_null($where) ? " WHERE {$where}" : ""));
                case 2:
                    return $this->query("SELECT {$select} FROM INFORMATION_SCHEMA.VIEWS" . (!is_null($where) ? " WHERE {$where}" : ""));
                case 3:
                    return $this->query("SELECT {$select} FROM INFORMATION_SCHEMA.SCHEMATA" . (!is_null($where) ? " WHERE {$where}" : ""));
            }
        }
        public function msdb($table, $select = "*", $where = NULL) {
            if($this->info["driver"] == "sqlsrv") {
                return $this->query("SELECT {$select} FROM msdb.dbo.{$table}" . (!is_null($where) ? " WHERE {$where}" : ""));
            } else {
                return false;
            }
        }
        public function getSQLAgentJobs($select = "*", $where = NULL) {
            return $this->msdb("sysjobs", $select, $where);
        }
        public function sys($table, $select = "*", $where = NULL) {
            if($this->info["driver"] == "sqlsrv") {
                return $this->query("SELECT {$select} FROM master.sys.{$table}" . (!is_null($where) ? " WHERE {$where}" : ""));
            } else {
                return false;
            }
        }
        public function getLinkedServers($select = "*", $where = NULL) {
            return $this->sys("sysservers", $select, $where);
        }
        public function dual($select = "*", $where = NULL) {
            if($this->info["driver"] == "oci") {
                return $this->query("SELECT {$select} FROM dual" . (!is_null($where) ? " WHERE {$where}" : ""));
            } else {
                return false;
            }
        }
        
        public function getDataTypes() {
            $types = [];
            $records = $this->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = N'". ($this->getSchema()) ."' AND TABLE_NAME = '" . ($this->getTable()) . "'");
            foreach($records as $record) {
                $types[$record["COLUMN_NAME"]] = $record["DATA_TYPE"];
            }
            
            return $types;
        }
        public function getPrimaryKey() {
            return $this->query(<<<SQL
                    SELECT
                        COLUMN_NAME
                    FROM
                        INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
                        INNER JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE ccu
                            ON tc.CONSTRAINT_NAME = ccu.Constraint_name
                    WHERE
                        tc.CONSTRAINT_TYPE = 'Primary Key' AND tc.CONSTRAINT_SCHEMA = '{$this->getSchema()}' AND ccu.CONSTRAINT_SCHEMA = '{$this->getSchema()}' AND tc.TABLE_SCHEMA = '{$this->getSchema()}' AND tc.TABLE_NAME = '{$this->getTable()}'
SQL
            )[0]["COLUMN_NAME"];
        }
        public function wrap($dataTypes, $column, $value) {
            if(empty($value)) {
                return "NULL";
            }
            $string = "N'{$value}'";
            switch($dataTypes[$column]) {
                case "int":
                    return (int)$value;
                case "datetime":
                    return "CAST({$string} AS DATETIME)";
                case "datetime2":
                    return "CAST({$string} AS DATETIME2)";
                case "date":
                    return "CAST({$string} AS DATE)";
                case "varchar":
                    return "'{$value}'";
                default:
                    return $string;
            }
        }
        
        public function peak($top = 250, $order = NULL) {
            return $this->select("TOP {$top} *", NULL, NULL, $order);
        }
        public function count($count = "*", $where = NULL) {
            return $this->select("COUNT ({$count})", $where);
        }
        public function query($query, $params = []) {
            $this->open();
            
            $query = $this->replaceParams($query, $params);
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll($this->config["fetch"]);
			            
            $this->close();
            
            return $results;
        }
        public function replaceParams($query, $params) {            
            if(!empty($params)) {
                for($i = 0; $i < count($params); $i++) {
                    $query = str_replace("{{$i}}", $params[$i], $query);
                }
            }
            
            return $query;
        }
        /**
         * 
         * @param String $select SELECT statement
         * @param String $where WHERE condition
         * @param String $group GROUP BY
         * @param String $order ORDER BY
         * @return Array
         */
        public function select($select = "*", $where = NULL, $group = NULL, $order = NULL) {            
            $query = "SELECT {$select} FROM [{$this->info["schema"]}].[{$this->info["table"]}] WITH (NOLOCK)";
            if(!is_null($where)) {
                $query .= " WHERE {$where}";
            }
            if(!is_null($group)) {
                $query .= " GROUP BY {$group}";
            }
            if(!is_null($order)) {
                $query .= " ORDER BY {$order}";
            }
            
            $this->open();
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll($this->config["fetch"]);
            
            $this->close();
            
            return $results;
        }
        /**
         * Create a bounded query to retrieve a subset of data with a start and end row number
         * @param String $select    SELECT statement
         * @param String $where     WHERE condition
         * @param Int $start        Query OFFSET
         * @param Int $end          Query FETCH NEXT
         * @return Array
         */
        public function select2($select = "*", $where = NULL, $start = -1, $end = -1) {            
            $query = "SELECT {$select} FROM [{$this->info["schema"]}].[{$this->info["table"]}]";
            if(!is_null($where)) {
                $query .= " WHERE {$where}";
            }
            if($start == -1 && $end > -1) {
                $query = str_replace("SELECT", "SELECT TOP {$end} ", $query);
            } else {
                if($start > -1 || $end > -1) {
                    $query .= " ORDER BY (SELECT 0)";
                }
                if($start > -1) {
                    $query .= " OFFSET {$start} ROWS";
                }
                if($end > -1) {
                    $query .= " FETCH NEXT {$end} ROWS ONLY";
                }
            }
            
            $this->open();
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll($this->config["fetch"]);
            
            $this->close();
            
            return $results;
        }
        /**
         * Use array KEYS as COLUMNS and VALUES as VALUES
         * @param Array $array
         * @return Array Results
         */
        public function update($array, $where = NULL) {
            $update = [];
            foreach($array as $key => $value) {
                array_push($update, "[{$key}]={$value}");
            }
            $update = implode($update, ",");
            
            $query = "UPDATE [{$this->info["schema"]}].[{$this->info["table"]}] SET {$update} OUTPUT DELETED." . $this->getPrimaryKey();
            if(strlen($where) > 0) {
                $query .= " WHERE {$where}";
            }
            
            print_r($query);
            
            $this->open();
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll($this->config["fetch"]);
            
            $this->close();
            
            return $results;
        }
        /**
         * Use array KEYS as COLUMNS and VALUES as VALUES
         * Will currently ONLY insert NVARCHAR
         * @param Array $array
         * @return Array Results
         */
        public function insert($array) {            
            $dataTypes = $this->getDataTypes();
            $columns = array_keys($array);
            $vals = [];
            foreach($array as $key => $value) {
                $vals[] = $this->wrap($dataTypes, $key, $value);
            }
            $vals = implode(",", $vals);
            array_walk($columns, function(&$value, $key) {
                $value = "[{$value}]";
            });
            $columns = implode(",", $columns);
            
            $query = "INSERT INTO
                    [{$this->info["schema"]}].[{$this->info["table"]}]
                    ({$columns})
                VALUES
                    ({$vals})";
                                        
            $this->open();
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll($this->config["fetch"]);
            
            $this->close();
            
            return $results;
        }
        
        /**
         * Pass an array of length 3 arrays (e.g. [[COLUMN_NAME, VALUE, PDO_DATA_TYPE], [COLUMN_NAME, VALUE, PDO_DATA_TYPE], ...]
         * No PDO_DATA_TYPE will result in PDO::PARAM_STR
         * @param type $array
         */
        public function PDOInsert($insert) {
            $data = [
                "Columns" => [],
                "Values" => [],
                "Types" => []
            ];
            foreach($insert as $val) {
                array_push($data["Columns"], $this->br($val[0]));
                array_push($data["Values"], $val[1]);
                array_push($data["Types"], isset($val[2]) ? $val[2] : PDO::PARAM_STR);
            }
            $query = "INSERT INTO [{$this->getSchema()}].[{$this->getTable()}] (" . implode($data["Columns"], ",") . ") OUTPUT INSERTED." . $this->getPrimaryKey() . " VALUES (" . implode(array_fill(0, count($data["Values"]), "?"), ",") . ")";
            
            $this->open();
            $sql = $this->conn->prepare($query);
            foreach($data["Types"] as $i => $type) {
                $sql->bindParam($i + 1, $data["Values"][$i], $type);
            }
            $sql->execute();
            $results = $sql->fetchAll($this->config["fetch"]);
            $this->close();
            
            return $results;
        }
        public function PDOUpdate($set, $where = null) {
            $data = [
                "set" => [
                    "Set" => "",
                    "Columns" => [],
                    "Values" => [],
                    "Types" => []
                ],
                "where" => [
                    "Where" => "",
                    "Columns" => [],
                    "Values" => [],
                    "Types" => []
                ],
            ];
            foreach($set as $val) {
                array_push($data["set"]["Columns"], $this->br($val[0]));
                $data["set"]["Set"] .= $this->br($val[0]) . " = ?, ";
                array_push($data["set"]["Values"], $val[1]);
                array_push($data["set"]["Types"], isset($val[2]) ? $val[2] : PDO::PARAM_STR);
            }
            $data["set"]["Set"] = substr($data["set"]["Set"], 0, strlen($data["set"]["Set"]) - 2);
            foreach($where as $val) {
                array_push($data["where"]["Columns"], $this->br($val[0]));
                $data["set"]["Where"] .= $this->br($val[0]) . " = ? AND ";
                array_push($data["where"]["Values"], $val[1]);
                array_push($data["where"]["Types"], isset($val[2]) ? $val[2] : PDO::PARAM_STR);
            }
            $data["set"]["Where"] = substr($data["set"]["Where"], 0, strlen($data["set"]["Where"]) - 5);
            $query = "UPDATE [{$this->getSchema()}].[{$this->getTable()}] SET {$data["set"]["Set"]} OUTPUT DELETED." . $this->getPrimaryKey();
            if(count($data["where"]["Columns"]) > 0) {
                $query .= " WHERE {$data["set"]["Where"]};";
            }
            
            $this->open();
            $sql = $this->conn->prepare($query);
            foreach($data["set"]["Types"] as $i => $type) {
                $sql->bindParam($i + 1, $data["set"]["Values"][$i], $type);
            }
            foreach($data["where"]["Types"] as $i => $type) {
                $sql->bindParam($i + 1 + (count($data["set"]["Types"])), $data["where"]["Values"][$i], $type);
            }
            $sql->execute();
            
            /*echo "<pre>";
            $sql->debugDumpParams();
            echo "</pre>";*/
            
            $results = $sql->fetchAll($this->config["fetch"]);
            $this->close();
            
            return $results;
        }        
        public function PDOStoredProcedure($name, $params, $schema = null) {
            $data = [
                "Values" => [],
                "Types" => [],
                "Params" => []
            ];
            foreach($params as $val) {
                array_push($data["Values"], $val[0]);
                array_push($data["Types"], isset($val[1]) ? $val[1] : PDO::PARAM_STR);
                array_push($data["Params"], "?");
            }
            $query = "EXEC [" . (is_null($schema) ? $this->getSchema() : $schema) . "].[{$name}] " . (implode(",", $data["Params"]));

            $this->open();
            $sql = $this->conn->prepare($query);
            foreach($data["Types"] as $i => $type) {
                $sql->bindValue($i + 1, $data["Values"][$i], $type);
			}
			// print_r($data);
			$sql->execute();
            
            /*echo "<pre>";
            $sql->debugDumpParams();
            echo "</pre>";*/
            
            $results = $sql->fetchAll($this->config["fetch"]);
            $this->close();
            
            return $results;
        }
        
        public function TVF($name, $params, $orderby = null, $defaultSchema = true) {
            foreach($params as $i => $param) {
                if(is_string($param)) {
                    $params[$i] = "'{$param}'";
                }
            }
            
            $query = "SELECT * FROM " . ($defaultSchema ? $this->getSchema() . "." : "") . $name . "(" . implode(",", $params) . ")";
            
            if($orderby) {
                $query .= " ORDER BY " . implode(",", $orderby);
            }
            return $this->query($query);
        }
        
        public static function str($string) {
            return "N'{$string}'";
        }
        public static function br($string) {
            return "[{$string}]";
        }
    }
?>