<?php
require_once "../Model.php";

class Camera extends Model {
	public $CameraID;
	public $Name;
	public $X;
	public $Y;
	public $Z;
	public $Pitch;
	public $Yaw;
	public $Roll;
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