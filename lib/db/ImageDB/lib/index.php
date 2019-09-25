<?php
	foreach (scandir("{$_SERVER["DOCUMENT_ROOT"]}/lib/") as $filename) {
		$path = $filename;
		if (is_file($path)) {
			require_once $path;
		}
	}
?>