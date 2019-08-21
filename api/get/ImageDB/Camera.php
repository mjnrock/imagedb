<?php
	require_once "../Model.php";

	class Camera extends Model {
		public $CameraID;
		public $Name;
		public $X;
		public $Y;
		public $Z;
		public $Pitch;
		public $Yaw;
		public $Roll;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>