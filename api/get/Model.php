<?php
	require_once "TableConnector.php";

	abstract class Model {
		public $TableConnector;
        public $Table = [
            "Catalog" => "FuzzyKnights",
            "Schema" => "ImageDB",
            "Table" => "Camera"
		];
		
		public function __constructor($catalog, $schema, $table) {
            $this->Table["Catalog"] = $catalog;
            $this->Table["Schema"] = $schema;
            $this->Table["Table"] = $table;
		}

		public function Connect(&$db) {
			$this->TableConnector = new TableConnector($db, $this->Table["Catalog"], $this->Table["Schema"], $this->Table["Table"]);

			return $this;
		}

		public function Get() {
			return $this->TableConnector;
		}
		public function Set($tableConn) {
			$this->TableConnector = $tableConn;

			return $this;
		}

		public function Select($arr = []) {
			$vals = [];

			foreach($arr as $col) {
				$vals[$col] = $this->$col;
			}

			return $this;
		}

		public function Update($arr = []) {
			$keys = array_keys($arr);

			foreach($keys as $key) {
				$this->$key = $arr[$key];
			}

			return $this;
		}
	}
?>