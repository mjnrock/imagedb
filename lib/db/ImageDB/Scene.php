<?php
	class Scene extends Model {
		const COLUMNS = [ "SceneID", "SequenceID", "CameraID", "TrackID", "ZIndex", "IsRequired", "Tags", "UUID" ];

		public $SceneID;
		public $SequenceID;
		public $CameraID;
		public $TrackID;
		public $ZIndex;
		public $IsRequired;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Scene", $uuid);
		}
	}
?>