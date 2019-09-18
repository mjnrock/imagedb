<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/DB.php";
    
    class KiKiPups extends Database {
        public function __construct() {
            parent::__construct("sqlsrv", "localhost", "KiKiPups", "kikipups", "kikipups");
			$this->setSchema("PlatformDB");
        }
    }
?>