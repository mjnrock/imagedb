<?php
	$Templates = [];

	//	Scane ./Templates/ and "require_once" every file
	foreach(scandir($Root . "Templates/") as $filename) {
		$path = $Root . "Templates/" . $filename;
		if (is_file($path)) {
			require_once $path;
		}
	}

	//	Write each $Templates[] child into ./{Destination}/lib
	foreach($Templates as $FileName => $FileContent) {
		$file = fopen($Root . $GLOBALS["FileSystem"]["Destination"] . "/lib/" . $FileName . ".php", "w") or die("Unable to open file");
		fwrite($file, "<?php\n" . $FileContent . "\n?>");
		fclose($file);
	}
?>