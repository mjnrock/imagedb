<?php
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/TableConnector.php";
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/Model.php";
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/ModelFactory.php";
	
	foreach (scandir("{$_SERVER["DOCUMENT_ROOT"]}/lib/db/ImageDB/php") as $filename) {
		$path = "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/ImageDB/php/" . $filename;
		if (is_file($path)) {
			require $path;
		}
	}
?>