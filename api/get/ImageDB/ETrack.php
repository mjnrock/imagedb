<?php
require_once "../Model.php";

class ETrack extends Model {
	public $ETrackID;
	public $ESequenceID;
	public $Name;
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