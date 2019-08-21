<?php
class Model {
	public $TableConnector;

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