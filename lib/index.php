<?php	
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/DB.php";
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/FuzzyKnights.php";
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/API.php";

	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/Display.php";
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/Router.php";

	function cout($data) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
?>