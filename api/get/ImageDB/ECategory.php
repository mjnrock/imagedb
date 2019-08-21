<?php
	require_once "../Model.php";

	class ECategory extends Model {
		public $ECategoryID;
		public $Name;
		public $Description;
		public $ParentECategoryID;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>