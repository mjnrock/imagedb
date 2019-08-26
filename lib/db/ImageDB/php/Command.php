<?php
	namespace ImageDB;
	class Command extends \Model {
		const COLUMNS = [ "CommandID", "Command", "UUID" ];

		public $CommandID;
		public $Command;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Command", $uuid);
		}
	}
?>