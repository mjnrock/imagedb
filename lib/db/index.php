<?php
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/TableConnector.php";
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/Model.php";
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/ModelFactory.php";
	
	foreach (scandir("{$_SERVER["DOCUMENT_ROOT"]}/lib/db/ImageDB") as $filename) {
		$path = "{$_SERVER["DOCUMENT_ROOT"]}/lib/db/ImageDB" . "/" . $filename;
		if (is_file($path)) {
			require $path;
		}
	}
?>