<?php
	class SceneDetail extends Model {
		const COLUMNS = [ "SceneDetailID", "SceneID", "CameraID", "TrackID", "ZIndex", "IsRequired", "Tags", "UUID" ];

		public $SceneDetailID;
		public $SceneID;
		public $CameraID;
		public $TrackID;
		public $ZIndex;
		public $IsRequired;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "SceneDetail", $uuid);
		}
	}
?>