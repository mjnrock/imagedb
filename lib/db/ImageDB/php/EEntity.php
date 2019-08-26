<?php
	namespace ImageDB;
	class EEntity extends \Model {
		const COLUMNS = [ "EEntityID", "Name", "Description", "ParentEEntityID", "Tags", "UUID" ];

		public $EEntityID;
		public $Name;
		public $Description;
		public $ParentEEntityID;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "EEntity", $uuid);
		}
	}
?>