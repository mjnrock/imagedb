<?php
	require_once "../Model.php";

	class Frame extends Model {
		public $FrameID;
		public $TrackID;
		public $ECategoryID;
		public $Duration;
		public $Ordinality;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>