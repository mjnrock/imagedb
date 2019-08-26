<?php
	namespace ImageDB;
	class Scene extends \Model {
		const COLUMNS = [ "SceneID", "Name", "Description", "Tags", "UUID" ];

		public $SceneID;
		public $Name;
		public $Description;
		public $Tags;
		public $UUID;

		public function __construct($uuid = null) {
			parent::Initialize("FuzzyKnights", "ImageDB", "Scene", $uuid);
		}
	}
?>