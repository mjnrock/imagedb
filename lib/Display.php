<?php
	abstract class Display {
		public static function Dialog($ViewBag) {
			include "{$_SERVER["DOCUMENT_ROOT"]}/views/Dialog.php";
		}
	}
?>