<?php
	class Sequence extends Model {
		const COLUMNS = [ "SequenceID", "ESequenceID", "Name", "Description", "Tags", "UUID" ];

		public $SequenceID;
		public $ESequenceID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;
	}
?>