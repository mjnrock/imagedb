<?php
require_once "../Model.php";

class Frame extends Model {
	public $FrameID;
	public $TrackID;
	public $ECategoryID;
	public $Duration;
	public $Ordinality;
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