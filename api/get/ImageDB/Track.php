<?php
require_once "../Model.php";

class Track extends Model {
	public $TrackID;
	public $SequenceID;
	public $ETrackID;
	public $Description;
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