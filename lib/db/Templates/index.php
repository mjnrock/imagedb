<?php
	$Templates["index"] = <<<PHP
	foreach (scandir("./") as \$filename) {
		\$path = \$filename;
		if (is_file(\$path)) {
			require_once \$path;
		}
	}
PHP;
?>