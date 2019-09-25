<?php
	header("Content-Type: text/plain");
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
	
	//?	Recursively copy ALL directories and files from ./lib into ./{Destination}/{Schema}/lib
	//?	<START BLOCK>
		//@	Setup iterator and create output directories
		$source = "./lib";
		$dest = "out/" . $GLOBALS["FileSystem"]["Destination"];

		if(!file_exists($dest)) {
			mkdir($dest, 0777, true);
		}
		$dest = $dest . "/lib";	

		if(!file_exists($dest)) {
			mkdir($dest, 0777);
		}

		foreach($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
			if($item->isDir()) {
				if(!file_exists($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName())) {
					mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
				}
			} else {
				copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			}
		}


		//@	Create an index file that loads ./lib contents
		$indexFile = <<<PHP
	foreach(\$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(\$source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as \$item) {
		if(!\$item->isDir()) {
			require_once \$item;
		}
	}
PHP;
		$file = fopen($dest . "/index.php", "w") or die("Unable to open file");
		fwrite($file, "<?php\n" . $indexFile . "\n?>");
		fclose($file);
	//? <END BLOCK>



	// //	Create database-specific files (e.g. API.php, FuzzyKnights.php, etc.)
	// require "DatabaseConnectors.php";

	// //	Create PHP and JS models from the database connection
	// require "ModelGenerator.php";
?>