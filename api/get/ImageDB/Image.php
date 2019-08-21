<?php
require_once "../Model.php";

class Image extends Model {
	public $ImageID;
	public $FilePath;
	public $FileName;
	public $FileExtension;
	public $Width;
	public $Height;
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