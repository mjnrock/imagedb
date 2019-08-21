<?php
	class Frame extends Model {
		const COLUMNS = [ "FrameID", "TrackID", "ECategoryID", "Duration", "Ordinality", "Tags", "UUID" ];

		public $FrameID;
		public $TrackID;
		public $ECategoryID;
		public $Duration;
		public $Ordinality;
		public $Tags;
		public $UUID;
	}
?>