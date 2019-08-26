<?php
	namespace ImageDB;
	class Camera extends \Model {
		const COLUMNS = [ "CameraID", "Name", "X", "Y", "Z", "Pitch", "Yaw", "Roll", "Tags", "UUID" ];

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

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Camera", $uuid);
		}
	}
?>