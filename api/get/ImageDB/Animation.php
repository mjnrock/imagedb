<?php
require_once "../Model.php";

class Animation extends Model {
	public $AnimationID;
	public $EAnimationID;
	public $SequenceID;
	public $Name;
	public $Description;
	public $Value;
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