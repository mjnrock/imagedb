<?php	
	abstract class Model {
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