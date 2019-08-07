<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/DB.php";
    
    class StaxPax extends Database {
        public function __construct() {
            parent::__construct("sqlsrv", "localhost", "StaxPax", "staxpax", "staxpax");
			$this->setSchema("Storyline");
        }
    }
?>