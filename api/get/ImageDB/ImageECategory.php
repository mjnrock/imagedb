<?php
require_once "../Model.php";

class ImageECategory extends Model {
	public $MappingID;
	public $ECategoryID;
	public $ImageID;
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