<?php	
	abstract class Model {
        public $Table = [
            "Catalog" => null,
            "Schema" => null,
			"Table" => null,
			"UUID" => null
		];

		public function Catalog($catalog = null) {
			if(isset($catalog)) {
				$this->Table["Catalog"] = $catalog;

				return $this;
			}

			return $this->Table["Catalog"];
		}
		public function Schema($schema = null) {
			if(isset($schema)) {
				$this->Table["Schema"] = $schema;

				return $this;
			}

			return $this->Table["Schema"];
		}
		public function Table($table = null) {
			if(isset($table)) {
				$this->Table["Table"] = $table;

				return $this;
			}

			return $this->Table["Table"];
		}		
		public function UUID($uuid = null) {
			if(isset($uuid)) {
				$this->Table["UUID"] = $uuid;

				return $this;
			}

			return $this->Table["UUID"];
		}

		public function Initialize($catalog = null, $schema = null, $table = null, $uuid = null) {
			$this->Table["Catalog"] = $catalog;
			$this->Table["Schema"] = $schema;
			$this->Table["Table"] = $table;
			$this->Table["UUID"] = $uuid;
		}
		
		public function ToJSON() {
			return json_encode($this);
		}

		public function Select($arr = []) {
			$vals = [];

			foreach($arr as $key) {
				$vals[ $key ] = $this->$key;
			}

			return $vals;
		}

		public function Update($arr = []) {
			$keys = array_keys($arr);

			foreach($keys as $key) {
				$this->$key = $arr[ $key ];
			}

			return $this;
		}

		public function SeedFromArray($arr) {
			foreach($arr as $key => $value) {
				$this->$key = $value;
			}

			return $this;
		}
		public function SeedFromModel($model) {
			cout($model);

			foreach(get_object_vars($model) as $key => $value) {
				$this->$key = $model->$key;
			 }

			return $this;
		}
	}
?>