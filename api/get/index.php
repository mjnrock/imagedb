<?php
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
    require_once "TableConnector.php";
    require_once "Model.php";
	require_once "ModelFactory.php";
	
	foreach (scandir("ImageDB") as $filename) {
		$path = "ImageDB" . '/' . $filename;
		if (is_file($path)) {
			require $path;
		}
	}
	
	//!	=========================================================================================================================
	//!	This code will generate .PHP classes that extend Model.php, which it expects to find at this same directory location
	//!	=========================================================================================================================

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
	
	$CameraFactory = (new ModelFactory("FuzzyKnights", "ImageDB", "Camera"))->Connect(API::$DB);
	$models = $CameraFactory->CreateFromFetch([
		3
	], true);
	cout($models);

    foreach($SchemaTables["TABLE_NAMES"] as $Table) {
        ${"Model$Table"} = new TableConnector(API::$DB, "FuzzyKnights", "ImageDB", $Table);

        $columns = [];
        foreach(${"Model$Table"}->Columns as $Column) {
            $columns[] = "\t\tpublic $" . $Column["name"] . ";";
        }

		$class = "\tclass " . ${"Model$Table"}->Table["Table"] . " extends Model {\n"
            . join("\n", $columns)
		// 	. "\n
		// public function __construct(\$catalog, \$schema) {
		// 	parent::__construct(\$catalog, \$schema, get_class(\$this));
		// }"
            . "\n\t}";

		if (!file_exists("ImageDB")) {
			mkdir("ImageDB", 0777, true);
		}
		$file = fopen("ImageDB\\" . $Table . ".php", "w") or die("Unable to open file");
		fwrite($file, "<?php\n" . $class . "\n?>");
		// fwrite($file, "<?php\n\trequire_once \"../Model.php\";\n\n" . $class . "\n? >");
		fclose($file);

        cout("[INFO]: \"ImageDB/" . $Table . ".php\" was updated");
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
    //             $this->$key = $arr[$key];
    //         }
    
    //         return $this;
    //     }

        
    //     public function Select($arr = []) {
    //         if(count($arr) === 0) {
    //             return $this;
    //         }

    //         $vals = [];
    
    //         foreach($arr as $col) {
    //             $vals[$col] = $this->$col;
    //         }
    
    //         return $vals;
    //     }
    // }

    // $ani = new Animation();
    // $ani->Update([
    //     "Tags" => "cat,dog",
    //     "Name" => "bob"
    // ]);
    
    // cout($ani->Select([
    //     "Tags",
    //     "Name"
    // ]));
    // cout($ani->Select());
    
    // // cout($ani);







    // $crud = $model->CRUD(1, null, "X = 99 AND Z = 87");
    // cout($crud);
    // $fetch = $model->Fetch(2);
    // cout($fetch);
    // $fetch = $model->Fetch("43A7EDE2-9233-4477-94D0-B7A67BBE1C4D", true);
    // cout($fetch);

    












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