<?php
	namespace ImageDB;
	class Sequence extends \Model {
		const COLUMNS = [ "SequenceID", "ESequenceID", "Name", "Description", "Tags", "UUID" ];

		public $SequenceID;
		public $ESequenceID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Sequence", $uuid);
		}
	}
?>