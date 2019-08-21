<?php
	class ESequence extends Model {
		const COLUMNS = [ "ESequenceID", "Name", "Description", "Tags", "UUID" ];

		public $ESequenceID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;
	}
?>