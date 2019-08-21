<?php
	require_once "../Model.php";

	class Track extends Model {
		public $TrackID;
		public $SequenceID;
		public $ETrackID;
		public $Description;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>