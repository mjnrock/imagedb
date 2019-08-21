<?php
	require_once "../Model.php";

	class Sequence extends Model {
		public $SequenceID;
		public $ESequenceID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>