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
    foreach($TableData as $Column) {
        $vars[] = "\tpublic $" . $Column["name"] . ";";
    }

    $class = "class {$Table["Table"]} extends Model {\n" . join("\n", $vars) . "\n}";

    cout($vars);
    cout($class);

    // class Model {
    //     protected $info = Array(
    //         "driver" => "sqlsrv",
    //         "server" => "",
    //         "database" => "",
    //         "schema" => "",
    //         "table" => "",
    //         "user" => "",
    //         "password" => "",
    //     );

    //     public function __construct($driver, $server, $database, $user, $password) {
    //         $this->info["driver"] = $driver;
    //         $this->info["server"] = $server;
    //         $this->info["database"] = $database;
    //         $this->info["user"] = $user;
    //         $this->info["password"] = $password;
    //     }        
    //     public function __destruct() {
    //         try {
    //             $this->close();
    //         } catch (Exception $e) {}
    //     }
    // }
















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