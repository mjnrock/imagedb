<?php
	class ImageECategory extends Model {
		const COLUMNS = [ "MappingID", "ECategoryID", "ImageID", "UUID" ];

		public $MappingID;
		public $ECategoryID;
		public $ImageID;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "ImageECategory", $uuid);
		}
	}
?>