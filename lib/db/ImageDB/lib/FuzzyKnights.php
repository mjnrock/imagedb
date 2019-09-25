<?php
	require_once "./DB.php";
	
	class FuzzyKnights extends Database {
		public function __construct() {
			parent::__construct(
				"sqlsrv",
				"localhost",
				"FuzzyKnights",
				"fuzzyknights",
				"fuzzyknights"
			);
			$this->setSchema("ImageDB");
		}
	}
?>