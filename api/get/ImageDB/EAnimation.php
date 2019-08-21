<?php
	require_once "../Model.php";

	class EAnimation extends Model {
		public $EAnimationID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>