<?php
	class EFrame extends Model {
		const COLUMNS = [ "EFrameID", "ETrackID", "Name", "Description", "Tags", "UUID" ];

		public $EFrameID;
		public $ETrackID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "EFrame", $uuid);
		}
	}
?>