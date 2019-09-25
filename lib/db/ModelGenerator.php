<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";

	//	This is just to run this ad hoc, it's real home is "runme.php"
	$GLOBALS = [
		"FileSystem" => [
			// "Root" => "{\$_SERVER[\"DOCUMENT_ROOT\"]}"
			"Root" => "",
			"Destination" => "ImageDB"
		],
		"Database" => [
			"Driver" => "sqlsrv",
			"Server" => "localhost",
			"Catalog" => "FuzzyKnights",
			"Schema" => "ImageDB",
			"User" => "fuzzyknights",
			"Password" => "fuzzyknights"
		]
	];
	
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
            '"default": ', CASE WHEN c.COLUMN_DEFAULT IS NOT NULL THEN CONCAT('"', SUBSTRING(c.COLUMN_DEFAULT, 2, LEN(c.COLUMN_DEFAULT) - 2), '"') ELSE 'null' END, ',' ,
            '"isNullable": ', CASE WHEN c.IS_NULLABLE = 'YES' THEN 1 ELSE 0 END, ',' ,
            '"isUnicode": ', CASE WHEN c.CHARACTER_SET_NAME = 'UNICODE' THEN 1 ELSE 0 END, ',' ,
            '"isString": ', CASE WHEN c.CHARACTER_SET_NAME IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isNumber": ', CASE WHEN c.NUMERIC_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isDatetime": ', CASE WHEN c.DATETIME_PRECISION IS NOT NULL THEN 1 ELSE 0 END, ',' ,
            '"isBoolean": ', CASE WHEN c.DATA_TYPE = 'bit' THEN 1 ELSE 0 END, ',' ,
            '"isUUID": ', CASE WHEN c.DATA_TYPE = 'uniqueidentifier' THEN 1 ELSE 0 END, ',' ,
			'"isPrimaryKey": ', CASE WHEN pk.COLUMN_NAME IS NOT NULL THEN 1 ELSE 0 END,
        '}'
    ) AS meta
FROM
    INFORMATION_SCHEMA.COLUMNS c
	LEFT JOIN (
		SELECT
			ku.TABLE_CATALOG,
			ku.TABLE_SCHEMA,
			ku.TABLE_NAME,
			ku.COLUMN_NAME
		FROM
			INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
			INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE ku
				ON tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
				AND tc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME
	) pk
		ON c.TABLE_CATALOG = pk.TABLE_CATALOG
		AND c.TABLE_SCHEMA = pk.TABLE_SCHEMA
		AND c.TABLE_NAME = pk.TABLE_NAME
		AND c.COLUMN_NAME = pk.COLUMN_NAME
WHERE
    c.TABLE_CATALOG = '%s'
    AND c.TABLE_SCHEMA = '%s'
ORDER BY
    "table",
    ordinality
SQL;

	$SQL = sprintf($SQL, $GLOBALS["Database"]["Catalog"], $GLOBALS["Database"]["Schema"]);	//* This replaces the "%s" in the $SQL HEREDOC
    $TableData = API::query($SQL);	//*	API has a DB connection hard-coded, and it must be changed to work on other DBs (extend DB.php)
    $SchemaTables = [];

    foreach($TableData as $Record) {
        if(!isset($SchemaTables[$Record["table"]])) {
            $SchemaTables[$Record["table"]] = [];
        }
        
        $SchemaTables[$Record["table"]][] = $Record["name"];
    }

	$SchemaTables["TABLE_NAMES"] = array_keys($SchemaTables);
	$JSPackage = [];

    foreach($SchemaTables["TABLE_NAMES"] as $Table) {
		${"Model$Table"} = new TableConnector(API::$DB, $GLOBALS["Database"]["Catalog"], $GLOBALS["Database"]["Schema"], $Table);
		$JSPackage[] = "import " . $Table . " from \"./" . $Table . ".js\"";

		//*	PHP File Creation
		//*	---------------------------------------------------------------------
		$variables = [];
		$columns = [];
        foreach(${"Model$Table"}->Columns as $Column) {
			$variables[] = "\t\tpublic $" . $Column["name"] . ";";
			$columns[] = "\"{$Column["name"]}\"";
        }

		$php = "\tnamespace " . $GLOBALS["Database"]["Schema"] . ";\n"
			. "\tclass " . ${"Model$Table"}->Table["Table"] . " extends \Model {\n"
			. "\t\tconst COLUMNS = [ " . join(", ", $columns) . " ];\n\n"
            . join("\n", $variables)
			. "\n
		public function __construct(\$uuid = null) {
			parent::Initialize(\"" . $GLOBALS["Database"]["Catalog"] . "\", \"" . $GLOBALS["Database"]["Schema"] . "\", \"" . $Table . "\", \$uuid);
		}"
			. "\n\t}";
			
		if(!file_exists($GLOBALS["Database"]["Schema"] . "\\php")) {
			mkdir($GLOBALS["Database"]["Schema"] . "\\php", 0777, true);
		}

		$file = fopen($GLOBALS["Database"]["Schema"] . "\\php" . "\\" . $Table . ".php", "w") or die("Unable to open file");
		fwrite($file, "<?php\n" . $php . "\n?>");
		// fwrite($file, "<?php\n\trequire_once \"../Model.php\";\n\n" . $php . "\n? >");
		fclose($file);
		
        cout("[INFO]: \"" . $GLOBALS["Database"]["Schema"] . "/php/" . $Table . ".php\" was updated");
		//*	---------------------------------------------------------------------



		//*	JS File Creation
		//*	---------------------------------------------------------------------
		$js = "class " . ${"Model$Table"}->Table["Table"] . " {\n"
			. "\tconstructor(obj = {}) {\n";

		foreach(${"Model$Table"}->Columns as $Column) {
			$js .= "\t\tthis." . $Column["name"] . " = null;\n";
		}
			
		$js .= "\n\t\tthis.Set(obj);\n"
			. "\t}\n\n"
			. "\tSet(obj = {}) {\n"
			. "\t\tfor(let key in obj) {\n"
			. "\t\t\tthis[ key ] = obj[ key ];\n"
			. "\t\t}\n"
			. "\n\t\treturn this;\n"
			. "\t}\n"
			. "}\n\n"
			. "export default " . $Table . ";";

		if(!file_exists($GLOBALS["Database"]["Schema"] . "\\js")) {
			mkdir($GLOBALS["Database"]["Schema"] . "\\js", 0777, true);
		}

		$file = fopen($GLOBALS["Database"]["Schema"] . "\\js\\" . $Table . ".js", "w") or die("Unable to open file");
		fwrite($file, $js);
		fclose($file);

        cout("[INFO]: \"" . $GLOBALS["Database"]["Schema"] . "/js/" . $Table . ".js\" was updated");
		//*	---------------------------------------------------------------------
	}
	
	$JSPackage = join("\n", $JSPackage);
	$JSPackage .= "\n\n"
		. "export default {\n\t"
		. join($SchemaTables["TABLE_NAMES"], ",\n\t")
		. "\n};";

	$file = fopen($GLOBALS["Database"]["Schema"] . "\\js\\package.js", "w") or die("Unable to open file");
	fwrite($file, $JSPackage);
?>