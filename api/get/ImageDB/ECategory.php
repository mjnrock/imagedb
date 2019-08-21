<?php
require_once "../Model.php";

class ECategory extends Model {
	public $ECategoryID;
	public $Name;
	public $Description;
	public $ParentECategoryID;
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