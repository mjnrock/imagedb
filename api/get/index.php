<?php
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";

    $Table = [
        "Catalog" => "FuzzyKnights",
        "Schema" => "ImageDB",
        "Table" => "Camera"
    ];
    $SQL = <<<SQL
SELECT
    c.DATA_TYPE AS 'type',
    c.COLUMN_NAME AS name,
    c.ORDINAL_POSITION AS ordinality,
    CONCAT(
        '{',
            '"precision": ', COALESCE(CAST(c.CHARACTER_MAXIMUM_LENGTH AS VARCHAR), CAST(c.NUMERIC_PRECISION AS VARCHAR), 'null'), ', ',
            '"scale": ', COALESCE(CAST(c.NUMERIC_SCALE AS VARCHAR), 'null'), ',' ,
            '"isUnicode": ', CASE WHEN c.CHARACTER_SET_NAME = 'UNICODE' THEN 1 ELSE 0 END,
        '}'
    ) AS meta
FROM
    INFORMATION_SCHEMA.COLUMNS c
WHERE
    c.TABLE_CATALOG = '{$Table["Catalog"]}'
    AND c.TABLE_SCHEMA = '{$Table["Schema"]}'
    AND c.TABLE_NAME = '{$Table["Table"]}'
ORDER BY
    ordinality
SQL;

    $TableData = API::query($SQL);
    $vars = [];
    $fns = [];
    foreach($TableData as $Column) {
        $vars[] = "\tpublic $" . $Column["name"] . ";";
        $fns[] = "\tpublic function Get" . $Column["name"] . "() {
\t\treturn \$this->" . $Column["name"] . ";
\t}\n";
        $fns[] = "\tpublic function Set" . $Column["name"] . "(value) {
\t\t\$this->" . $Column["name"] . " = value;
        
\t\treturn \$this;
\t}\n\n";
    }

    $class = "class {$Table["Table"]} extends Model {\n" . join("\n", $vars) . "\n\n" . join("\n", $fns) . "}";

    // cout($fns);
    // cout($vars);
    // cout($class);

    $model = new Model(API::$DB, "FuzzyKnights", "ImageDB", "Camera");

    $crud = $model->CRUD(1, null, "X = 99 AND Z = 87");
    cout($crud);

    class Model {
        protected $Database;
        protected $Table = [
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
        public function __destruct() {
            try {
                $this->close();
            } catch (Exception $e) {}
        }

        public function CRUD($action, $payload = null, $condition = null) {
            $params = [];

            if(is_array($payload)) {
                $payload = json_encode($payload);
            }
            $cols = array_keys($payload);

            return $this->Database->PDOStoredProcedure("CRUD", [
                [ $this->Table["Table"], PDO::PARAM_STR ],
                [ $action, PDO::PARAM_INT ],
                [ $payload, isset($payload) ? PDO::PARAM_STR : PDO::PARAM_NULL ],
                [ $condition, isset($condition) ? PDO::PARAM_STR : PDO::PARAM_NULL ]
            ], $this->Table["Schema"]);
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