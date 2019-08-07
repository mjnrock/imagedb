<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/DB.php";
    
    class FuzzyKnights extends Database {
        public function __construct() {
            parent::__construct("sqlsrv", "localhost", "FuzzyKnights", "fuzzyknights", "fuzzyknights");
			$this->setSchema("FuzzyKnights");
        }
    }
?>