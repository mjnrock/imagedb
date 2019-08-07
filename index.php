<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/routes/peripheral/header.php";
	
	Router::SetServer($_SERVER);

	Router::QuickGet("/image", "Image");

	require_once "{$_SERVER["DOCUMENT_ROOT"]}/routes/peripheral/footer.php";
?>