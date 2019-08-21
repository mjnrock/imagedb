<?php
	require_once "../Model.php";

	class Command extends Model {
		public $CommandID;
		public $Command;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>