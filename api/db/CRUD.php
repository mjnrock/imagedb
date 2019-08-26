<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
	
	CRUD([
		"TableName" => $_GET["TableName"],
		"Action" => $_GET["Action"],
		"Payload" => $_GET["Payload"],
		"Condition" => $_GET["Condition"]
	]);

	function CRUD($Payload) {
        $ResultSet = API::$DB->PDOStoredProcedure("CRUD", [
            [ isset($Payload[ "TableName" ]) ? $Payload[ "TableName" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "Action" ]) ? $Payload[ "Action" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "Payload" ]) ? json_encode($Payload[ "Payload" ]) : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "Condition" ]) ? $Payload[ "Condition" ] : "NULL", PDO::PARAM_STR ]
        ]);

        echo json_encode($ResultSet);
    }
?>