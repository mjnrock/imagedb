<?php
	require_once "../Model.php";

	class Image extends Model {
		public $ImageID;
		public $FilePath;
		public $FileName;
		public $FileExtension;
		public $Width;
		public $Height;
		public $Tags;
		public $UUID;

		public function __constructor($catalog, $schema) {
			parent::__construct($catalog, $schema, get_class($this));
		}
	}
?>