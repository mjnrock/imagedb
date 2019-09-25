<?php
	//!	Modify these entries below, as needed, then execute this script

	$GLOBALS = [
		"FileSystem" => [
			// "Root" => "{\$_SERVER[\"DOCUMENT_ROOT\"]}"
			"Root" => "",
			"Destination" => "ImageDB",
			"AddDocRoot" => true
		],
		"Database" => [
			"Driver" => "sqlsrv",
			"Server" => "localhost",
			"Database" => "FuzzyKnights",
			"Schema" => "ImageDB",
			"User" => "fuzzyknights",
			"Password" => "fuzzyknights"
		]
	];
	$Root = $GLOBALS["FileSystem"]["AddDocRoot"] === true ? "{\$_SERVER[\"DOCUMENT_ROOT\"]}/" : "./";
	
	//	Create ./{Destination}/lib if it does not exist
	if(!file_exists($Root . $GLOBALS["FileSystem"]["Destination"] . "\\lib\\")) {
		mkdir($Root . $GLOBALS["FileSystem"]["Destination"] . "\\lib\\", 0777, true);
	}

	//	Copy all ./Core/ files into ./{Destination}/lib
	foreach(scandir($Root . "Core/") as $filename) {
		$path = $Root . "Core/" . $filename;
		if(is_file($path)) {
			copy($path, $Root . $GLOBALS["FileSystem"]["Destination"] . "\\lib\\" . $filename);
		}
	}

	//	Create database-specific files (e.g. API.php, FuzzyKnights.php, etc.)
	require "DatabaseConnectors.php";
?>