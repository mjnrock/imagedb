<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/routes/peripheral/header.php";
	
	Router::SetServer($_SERVER);

	Router::QuickGet("/image", "Image");
	Router::QuickGet("/dictionary", "dictionary/index");
	Router::QuickGet("/record", "dictionary/record");

	require_once "{$_SERVER["DOCUMENT_ROOT"]}/routes/peripheral/footer.php";
?>