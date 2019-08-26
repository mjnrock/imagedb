<?php
	namespace ImageDB;
	class Track extends \Model {
		const COLUMNS = [ "TrackID", "SequenceID", "ETrackID", "Description", "Tags", "UUID" ];

		public $TrackID;
		public $SequenceID;
		public $ETrackID;
		public $Description;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Track", $uuid);
		}
	}
?>