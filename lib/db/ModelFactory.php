<?php
	//*	Debugging/Testing Code
	//*	----------------------------	
	// $CameraFactory = (new ModelFactory("FuzzyKnights", "ImageDB", "Camera"))->Connect(API::$DB);
	// $models = $CameraFactory->CreateFromFetch([
	// 	3
	// ], true);
	// cout($models);

	class ModelFactory {
		public $TableConnector;
        public $Table = [
            "Catalog" => null,
            "Schema" => null,
            "Table" => null
		];
		
		public function __construct($catalog, $schema, $table) {			
            $this->Table["Catalog"] = $catalog;
            $this->Table["Schema"] = $schema;
			$this->Table["Table"] = $table;
		}

		public function Connect(&$db) {
			$this->TableConnector = new TableConnector($db, $this->Table["Catalog"], $this->Table["Schema"], $this->Table["Table"]);

			return $this;
		}

		public function Get($params, $asJSON = false) {
			return $this->TableConnector->Fetch($params, $asJSON);
		}

		protected function CreateFrom($results, $asJSON = false) {
			$arr = [];

			foreach($results as $row) {
				$col1Value = $row[$this->TableConnector->Columns[0]["name"]];

				$arr[$col1Value] = (new $this->Table["Table"](isset($row["UUID"]) ? $row["UUID"] : null))
					->SeedFromArray($row);
			}
			
			if($asJSON === true) {
				return json_encode($arr);
			}

			return $arr;
		}
		public function CreateFromFetch($params = null, $asJSON = false) {
			return $this->CreateFrom($this->TableConnector->Fetch($params, false), $asJSON);
		}
		public function CreateFromCRUD($action, $payload = null, $condition = null, $asJSON = false) {
			return $this->CreateFrom($this->TableConnector->CRUD($action, $payload, $condition, false), $asJSON);
		}
	}
?>