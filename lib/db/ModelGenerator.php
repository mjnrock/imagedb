<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
	
	const CATALOG = "FuzzyKnights";
	const SCHEMA = "ImageDB";
	
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
    c.TABLE_CATALOG = '%s'
    AND c.TABLE_SCHEMA = '%s'
ORDER BY
    "table",
    ordinality
SQL;

	$SQL = sprintf($SQL, CATALOG, SCHEMA);	//* This replaces the "%s" in the $SQL HEREDOC
    $TableData = API::query($SQL);
    $SchemaTables = [];

    foreach($TableData as $Record) {
        if(!isset($SchemaTables[$Record["table"]])) {
            $SchemaTables[$Record["table"]] = [];
        }
        
        $SchemaTables[$Record["table"]][] = $Record["name"];
    }

	$SchemaTables["TABLE_NAMES"] = array_keys($SchemaTables);

    foreach($SchemaTables["TABLE_NAMES"] as $Table) {
        ${"Model$Table"} = new TableConnector(API::$DB, CATALOG, SCHEMA, $Table);

		$variables = [];
		$columns = [];
        foreach(${"Model$Table"}->Columns as $Column) {
			$variables[] = "\t\tpublic $" . $Column["name"] . ";";
			$columns[] = "\"{$Column["name"]}\"";
        }

		$class = "\tclass " . ${"Model$Table"}->Table["Table"] . " extends Model {\n"
			. "\t\tconst COLUMNS = [ " . join(", ", $columns) . " ];\n\n"
            . join("\n", $variables)
			. "\n
		public function __construct(\$uuid = null) {
			parent::Initialize(\"" . CATALOG . "\", \"" . SCHEMA . "\", \"" . $Table . "\", \$uuid);
		}"
            . "\n\t}";

		if(!file_exists(SCHEMA)) {
			mkdir(SCHEMA, 0777, true);
		}

		$file = fopen(SCHEMA . "\\" . $Table . ".php", "w") or die("Unable to open file");
		fwrite($file, "<?php\n" . $class . "\n?>");
		// fwrite($file, "<?php\n\trequire_once \"../Model.php\";\n\n" . $class . "\n? >");
		fclose($file);

        cout("[INFO]: \"" . SCHEMA . "/" . $Table . ".php\" was updated");
    }
?>