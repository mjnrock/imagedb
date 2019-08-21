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
		2
	], false);
	// cout($models);

	$thing = (new Camera())->SeedFromModel($models[2]);
	cout($thing);

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
?>