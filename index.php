<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/views/peripheral/header.php";
	
	Router::SetServer($_SERVER);

	Router::QuickGet("/image", "Image");
	Router::QuickGet("/dictionary", "dictionary/index");
	Router::QuickGet("/table", "dictionary/table");
	Router::QuickGet("/record", "dictionary/record");
	Router::QuickGet("/sql", "sql/index");

	require_once "{$_SERVER["DOCUMENT_ROOT"]}/views/peripheral/footer.php";
?>