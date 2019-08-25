<?php
	class ImageEEntity extends Model {
		const COLUMNS = [ "MappingID", "ImageID", "EEntityID", "UUID" ];

		public $MappingID;
		public $ImageID;
		public $EEntityID;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "ImageEEntity", $uuid);
		}
	}
?>