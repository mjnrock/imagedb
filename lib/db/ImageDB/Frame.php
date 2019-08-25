<?php
	class Frame extends Model {
		const COLUMNS = [ "FrameID", "TrackID", "EFrameID", "Duration", "Ordinality", "Tags", "UUID" ];

		public $FrameID;
		public $TrackID;
		public $EFrameID;
		public $Duration;
		public $Ordinality;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Frame", $uuid);
		}
	}
?>