<?php
	require_once "../Model.php";

	class ImageECategory extends Model {
		public $MappingID;
		public $ECategoryID;
		public $ImageID;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>