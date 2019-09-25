<?php
	//!	Modify these entries below, as needed, then execute this script

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
	
	//	Create ./{Destination}/lib if it does not exist
	if(!file_exists($GLOBALS["FileSystem"]["Destination"] . "\\lib\\")) {
		mkdir($GLOBALS["FileSystem"]["Destination"] . "\\lib\\", 0777, true);
	}

	//	Copy all ./Core/ files into ./{Destination}/lib
	foreach(scandir("./Core/") as $filename) {
		$path = "./Core/" . $filename;
		if(is_file($path)) {
			copy($path, $GLOBALS["FileSystem"]["Destination"] . "\\lib\\" . $filename);
		}
	}

	//	Create database-specific files (e.g. API.php, FuzzyKnights.php, etc.)
	require "DatabaseConnectors.php";

	//	Create PHP and JS models from the database connection
	require "ModelGenerator.php";
?>