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
	}
?>