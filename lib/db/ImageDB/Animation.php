<?php
	class Animation extends Model {
		const COLUMNS = [ "AnimationID", "EAnimationID", "SequenceID", "Name", "Description", "Value", "Tags", "UUID" ];

		public $AnimationID;
		public $EAnimationID;
		public $SequenceID;
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