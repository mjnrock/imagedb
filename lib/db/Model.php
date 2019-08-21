<?php	
	abstract class Model {
        public $Meta = [
            "Catalog" => null,
            "Schema" => null,
			"Table" => null,
			"UUID" => null
		];

		public function Catalog($catalog = null) {
			if(isset($catalog)) {
				$this->Meta["Catalog"] = $catalog;

				return $this;
			}

			return $this->Meta["Catalog"];
		}
		public function Schema($schema = null) {
			if(isset($schema)) {
				$this->Meta["Schema"] = $schema;

				return $this;
			}

			return $this->Meta["Schema"];
		}
		public function Table($table = null) {
			if(isset($table)) {
				$this->Meta["Table"] = $table;

				return $this;
			}

			return $this->Meta["Table"];
		}		
		public function UUID($uuid = null) {
			if(isset($uuid)) {
				$this->Meta["UUID"] = $uuid;

				return $this;
			}

			return $this->Meta["UUID"];
		}

		public function Initialize($catalog = null, $schema = null, $table = null, $uuid = null) {
			$this->Meta["Catalog"] = $catalog;
			$this->Meta["Schema"] = $schema;
			$this->Meta["Table"] = $table;
			$this->Meta["UUID"] = $uuid;
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