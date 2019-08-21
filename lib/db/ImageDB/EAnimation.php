<?php
	class EAnimation extends Model {
		const COLUMNS = [ "EAnimationID", "Name", "Description", "Tags", "UUID" ];

		public $EAnimationID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;
	}
?>