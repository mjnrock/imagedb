<?php
	class Animation extends Model {
		const COLUMNS = [ "AnimationID", "EAnimationID", "Name", "Description", "Value", "Tags", "UUID" ];

		public $AnimationID;
		public $EAnimationID;
		public $Name;
		public $Description;
		public $Value;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Animation", $uuid);
		}
	}
?>