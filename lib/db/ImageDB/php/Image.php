<?php
	class Image extends Model {
		const COLUMNS = [ "ImageID", "FilePath", "FileName", "FileExtension", "Width", "Height", "Tags", "UUID" ];

		public $ImageID;
		public $FilePath;
		public $FileName;
		public $FileExtension;
		public $Width;
		public $Height;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Image", $uuid);
		}
	}
?>