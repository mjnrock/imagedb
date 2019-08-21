<?php
require_once "../Model.php";

class EAnimation extends Model {
	public $EAnimationID;
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