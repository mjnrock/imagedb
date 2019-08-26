<?php
	namespace ImageDB;
	class ETrack extends \Model {
		const COLUMNS = [ "ETrackID", "ESequenceID", "Name", "Description", "Tags", "UUID" ];

		public $ETrackID;
		public $ESequenceID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "ETrack", $uuid);
		}
	}
?>