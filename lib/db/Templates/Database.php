<?php
	$Templates[$GLOBALS["Database"]["Database"]] = <<<PHP
	require_once "./DB.php";
	
	class {$GLOBALS["Database"]["Database"]} extends Database {
		public function __construct() {
			parent::__construct(
				"{$GLOBALS["Database"]["Driver"]}",
				"{$GLOBALS["Database"]["Server"]}",
				"{$GLOBALS["Database"]["Database"]}",
				"{$GLOBALS["Database"]["User"]}",
				"{$GLOBALS["Database"]["Password"]}"
			);
			\$this->setSchema("{$GLOBALS["Database"]["Schema"]}");
		}
	}
PHP;
?>