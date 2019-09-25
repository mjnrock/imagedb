<?php
	$Templates = [];

	//	Create ./{Destination}/lib/db if it does not exist
	if(!file_exists($GLOBALS["FileSystem"]["Destination"] . "\\lib\\db\\")) {
		mkdir($GLOBALS["FileSystem"]["Destination"] . "\\lib\\db\\", 0777, true);
	}

	//	Scane ./Templates/ and "require_once" every file
	foreach(scandir("./Templates/") as $filename) {
		$path = "./Templates/" . $filename;
		if (is_file($path)) {
			require_once $path;
		}
	}

	//	Write each $Templates[] child into ./{Destination}/lib/db
	foreach($Templates as $FileName => $FileContent) {
		$file = fopen($GLOBALS["FileSystem"]["Destination"] . "\\lib\\db\\" . $FileName . ".php", "w") or die("Unable to open file");
		fwrite($file, "<?php\n" . $FileContent . "\n?>");
		fclose($file);
	}
?>