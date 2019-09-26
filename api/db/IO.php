<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
	
	// // print_r($_GET);
	if(isset($_GET["Query"]) && strlen($_GET["Query"]) > 0) {
		Execute([
			"Query" => $_GET["Query"]
		]);
	}

	function Execute($Payload) {
        $ResultSet = API::query($Payload[ "Query" ]);

        echo json_encode($ResultSet);
	}
	
	// echo json_encode($_GET);
?>