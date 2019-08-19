<?php
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
    
    $SQL = <<<SQL
SELECT
    c.TABLE_NAME AS "table",
    c.DATA_TYPE AS "type",
    c.COLUMN_NAME AS "name",
    c.ORDINAL_POSITION AS ordinality,
    CONCAT(
        '{',
            '"precision": ', COALESCE(CAST(c.CHARACTER_MAXIMUM_LENGTH AS VARCHAR), CAST(c.NUMERIC_PRECISION AS VARCHAR), 'null'), ', ',
            '"scale": ', COALESCE(CAST(c.NUMERIC_SCALE AS VARCHAR), 'null'), ',' ,
            '"isUnicode": ', CASE WHEN c.CHARACTER_SET_NAME = 'UNICODE' THEN 1 ELSE 0 END, ',' ,
            '"isString": ', CASE WHEN c.CHARACTER_SET_NAME IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isNumber": ', CASE WHEN c.NUMERIC_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isDatetime": ', CASE WHEN c.DATETIME_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isBoolean": ', CASE WHEN c.DATA_TYPE = 'bit' THEN 1 ELSE 0 END,
        '}'
    ) AS meta
FROM
    INFORMATION_SCHEMA.COLUMNS c
WHERE
    c.TABLE_CATALOG = 'FuzzyKnights'
    AND c.TABLE_SCHEMA = 'ImageDB'
ORDER BY
    "table",
    ordinality
SQL;

    $TableData = API::query($SQL);
    $SchemaTables = [];

    foreach($TableData as $Record) {
        if(!isset($SchemaTables[$Record["table"]])) {
            $SchemaTables[$Record["table"]] = [];
        }
        
        $SchemaTables[$Record["table"]][] = $Record["name"];
    }

    $SchemaTables["TABLE_NAMES"] = array_keys($SchemaTables);

//     $vars = [];
//     $fns = [];
//     foreach($TableData as $Column) {
//         $vars[] = "\tpublic $" . $Column["name"] . ";";
//         $fns[] = "\tpublic function Get" . $Column["name"] . "() {
// \t\treturn \$this->" . $Column["name"] . ";
// \t}\n";
//         $fns[] = "\tpublic function Set" . $Column["name"] . "(value) {
// \t\t\$this->" . $Column["name"] . " = value;
        
// \t\treturn \$this;
// \t}\n\n";
//     }

//     //TODO Iterate through all the Tables under the Schema |=> Create <FlyweightModel>.php file for each Table |=> Save to Project's File System
//     $class = "class {$Table["Table"]} {\n"
//         . join("\n", $vars)
//         . "\n\n"
//         . join("\n", $fns)
//     . "}";

    // cout($fns);
    // cout($vars);
    // cout($class);

    foreach($SchemaTables["TABLE_NAMES"] as $Table) {
        ${"Model$Table"} = new TableConnector(API::$DB, "FuzzyKnights", "ImageDB", $Table);

        $columns = [];
        foreach(${"Model$Table"}->Columns as $Column) {
            $columns[] = "\tpublic $" . $Column["name"] . ";";
        }

        $fnUpdate = "\n\tpublic function Update(\$arr = []) {
            \$keys = array_keys(\$arr);
    
            foreach(\$keys as \$key) {
                \${\"this\"}->\$key = \$arr[\$key];
            }
    
            return \$this;
        }";

        $class = "class " . ${"Model$Table"}->Table["Table"] . " {\n"
            . join("\n", $columns)
            . "\n" . $fnUpdate
            . "\n}";

        cout($class);
    }




    //? Struct testing alongside dynamic variables
    // class Animation {
    //     public $AnimationID;
    //     public $EAnimationID;
    //     public $SequenceID;
    //     public $Name;
    //     public $Description;
    //     public $Value;
    //     public $Tags = 8;
    //     public $UUID;
    
    //     public function Update($arr = []) {
    //         $keys = array_keys($arr);
    
    //         foreach($keys as $key) {
    //             ${"this"}->$key = $arr[$key];
    //         }
    
    //         return $this;
    //     }
    // }

    // $ani = new Animation();
    // $ani->Update([
    //     "Tags" => "cat,dog",
    //     "Name" => "bob"
    // ]);
    
    // cout($ani);







    // $crud = $model->CRUD(1, null, "X = 99 AND Z = 87");
    // cout($crud);
    // $fetch = $model->Fetch(2);
    // cout($fetch);
    // $fetch = $model->Fetch("43A7EDE2-9233-4477-94D0-B7A67BBE1C4D", true);
    // cout($fetch);

    class TableConnector {
        protected $Database;
        public $Table = [
            "Catalog" => "FuzzyKnights",
            "Schema" => "ImageDB",
            "Table" => "Camera"
        ];
        public $Columns = [];

        public function __construct(&$database, $catalog, $schema, $table) {
            $this->Database = $database;
            $this->Table["Catalog"] = $catalog;
            $this->Table["Schema"] = $schema;
            $this->Table["Table"] = $table;

            $this->Columns = $this->Database->query($this->MetaQuery());

            foreach($this->Columns as $k => $Column) {
                $this->Columns[$k]["meta"] = json_decode($this->Columns[$k]["meta"]);

                //? Convert SQL BIT into true Boolean values
                // foreach($this->Columns[$k]["meta"] as $flag => $value) {
                //     if(substr($flag, 0, 2) === "is") {
                //         $this->Columns[$k]["meta"]->$flag = !!$this->Columns[$k]["meta"]->$flag;
                //     }
                // }
            }
        }
        public function __destruct() {}

        public function CRUD($action, $payload = null, $condition = null, $asJSON = false) {
            $params = [];

            if(is_array($payload)) {
                $payload = json_encode($payload);
            }
            $cols = array_keys($payload);

            $results = $this->Database->PDOStoredProcedure("CRUD", [
                [ $this->Table["Table"], PDO::PARAM_STR ],
                [ $action, PDO::PARAM_INT ],
                [ $payload, isset($payload) ? PDO::PARAM_STR : PDO::PARAM_NULL ],
                [ $condition, isset($condition) ? PDO::PARAM_STR : PDO::PARAM_NULL ]
            ], $this->Table["Schema"]);

            return $asJSON ? json_encode($results) : $results;
        }

        public function Fetch($input, $asJSON = false) {
            $results = $this->Database->TVF("Get" . $this->Table["Table"], [ $input ]);

            return $asJSON ? json_encode($results) : $results;
        }

        public function MetaQuery() {
            return <<<SQL
SELECT
    c.DATA_TYPE AS 'type',
    c.COLUMN_NAME AS name,
    c.ORDINAL_POSITION AS ordinality,
    CONCAT(
        '{',
            '"precision": ', COALESCE(CAST(c.CHARACTER_MAXIMUM_LENGTH AS VARCHAR), CAST(c.NUMERIC_PRECISION AS VARCHAR), 'null'), ', ',
            '"scale": ', COALESCE(CAST(c.NUMERIC_SCALE AS VARCHAR), 'null'), ',' ,
            '"isUnicode": ', CASE WHEN c.CHARACTER_SET_NAME = 'UNICODE' THEN 1 ELSE 0 END, ',' ,
            '"isString": ', CASE WHEN c.CHARACTER_SET_NAME IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isNumber": ', CASE WHEN c.NUMERIC_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isDatetime": ', CASE WHEN c.DATETIME_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isBoolean": ', CASE WHEN c.DATA_TYPE = 'bit' THEN 1 ELSE 0 END,
        '}'
    ) AS meta
FROM
    INFORMATION_SCHEMA.COLUMNS c
WHERE
    c.TABLE_CATALOG = '{$this->Table["Catalog"]}'
    AND c.TABLE_SCHEMA = '{$this->Table["Schema"]}'
    AND c.TABLE_NAME = '{$this->Table["Table"]}'
ORDER BY
    ordinality
SQL;
        }
    }
















    // if(isset($_GET["Endpoint"]) && isset($_GET["Payload"])) {
    //     $msg = [
    //         "ep" => $_GET["Endpoint"],
    //         "data" => $_GET["Payload"]
    //     ];

    //     API::$DB->PDOStoredProcedure(
    //         $msg["ep"],
    //         $msg["data"],
    //         "ImageDB"
    //     );

    //     API::$DB->TVF(
    //         "Name",
    //         "Params"
    //     );
    // }
?>