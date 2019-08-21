<?php
	require_once "../Model.php";

	class Scene extends Model {
		public $SceneID;
		public $SequenceID;
		public $CameraID;
		public $TrackID;
		public $ZIndex;
		public $IsRequired;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>