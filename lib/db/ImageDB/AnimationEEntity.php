<?php
	class AnimationEEntity extends Model {
		const COLUMNS = [ "MappingID", "AnimationID", "EEntityID", "UUID" ];

		public $MappingID;
		public $AnimationID;
		public $EEntityID;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "AnimationEEntity", $uuid);
		}
	}
?>