<?php
require_once "../Model.php";

class Scene extends Model {
	public $SceneID;
	public $SequenceID;
	public $CameraID;
	public $TrackID;
	public $ZIndex;
	public $IsRequired;
	public $Tags;
	public $UUID;

	public function Update($arr = []) {
            $keys = array_keys($arr);
    
            foreach($keys as $key) {
                ${"this"}->$key = $arr[$key];
            }
    
            return $this;
        }
}
?>