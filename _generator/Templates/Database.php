<?php
	$Templates[$GLOBALS["Database"]["Catalog"]] = <<<PHP
	require_once "{\$_SERVER["DOCUMENT_ROOT"]}/lib/DB.php";
	
	class {$GLOBALS["Database"]["Catalog"]} extends Database {
		public function __construct() {
			parent::__construct(
				"{$GLOBALS["Database"]["Driver"]}",
				"{$GLOBALS["Database"]["Server"]}",
				"{$GLOBALS["Database"]["Catalog"]}",
				"{$GLOBALS["Database"]["User"]}",
				"{$GLOBALS["Database"]["Password"]}"
			);
			\$this->setSchema("{$GLOBALS["Database"]["Schema"]}");
		}
	}
PHP;
?>