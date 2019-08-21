<?php
	class ECategory extends Model {
		const COLUMNS = [ "ECategoryID", "Name", "Description", "ParentECategoryID", "Tags", "UUID" ];

		public $ECategoryID;
		public $Name;
		public $Description;
		public $ParentECategoryID;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "ECategory", $uuid);
		}
	}
?>