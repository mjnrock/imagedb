<?php
require_once "../Model.php";

class Command extends Model {
	public $CommandID;
	public $Command;
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