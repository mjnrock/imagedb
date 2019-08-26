<?php
	namespace ImageDB;
	class AnimationSequence extends \Model {
		const COLUMNS = [ "MappingID", "AnimationID", "SequenceID", "Ordinality", "UUID" ];

		public $MappingID;
		public $AnimationID;
		public $SequenceID;
		public $Ordinality;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "AnimationSequence", $uuid);
		}
	}
?>