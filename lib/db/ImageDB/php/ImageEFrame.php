<?php
	namespace ImageDB;
	class ImageEFrame extends \Model {
		const COLUMNS = [ "MappingID", "ImageID", "EFrameID", "UUID" ];

		public $MappingID;
		public $ImageID;
		public $EFrameID;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "ImageEFrame", $uuid);
		}
	}
?>