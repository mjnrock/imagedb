<?php
	class Command extends Model {
		const COLUMNS = [ "CommandID", "Command", "UUID" ];

		public $CommandID;
		public $Command;
		public $UUID;
	}
?>