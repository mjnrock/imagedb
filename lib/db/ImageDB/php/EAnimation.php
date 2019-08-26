<?php
	namespace ImageDB;
	class EAnimation extends \Model {
		const COLUMNS = [ "EAnimationID", "Name", "Description", "Tags", "UUID" ];

		public $EAnimationID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "EAnimation", $uuid);
		}
	}
?>