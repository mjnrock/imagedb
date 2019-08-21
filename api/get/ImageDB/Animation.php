<?php
	require_once "../Model.php";

	class Animation extends Model {
		public $AnimationID;
		public $EAnimationID;
		public $SequenceID;
		public $Name;
		public $Description;
		public $Value;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>