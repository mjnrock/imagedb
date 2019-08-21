<?php
	require_once "../Model.php";

	class ESequence extends Model {
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