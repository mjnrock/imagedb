<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
	// require_once "{$_SERVER["DOCUMENT_ROOT"]}/models/index.php";

	if(isset($_GET["Action"]) && isset($_GET["Payload"])) {
		$Payload = json_decode($_GET["Payload"]);

		if($_GET["Action"] === "MergeImage") {
			MergeImage($Payload);
			// print_r($Payload);
		}
	}

	function MergeImage($Payload) {
		if(	
			isset($Payload->FilePath)
			&& isset($Payload->FileName)
			&& isset($Payload->FileExtension)
			&& isset($Payload->Width)
			&& isset($Payload->Height)
			&& isset($Payload->Tags)
		) {
			$Image = API::$DB->PDOStoredProcedure("MergeImage", [
				[$Payload->FilePath, PDO::PARAM_STR],
				[$Payload->FileName, PDO::PARAM_STR],
				[$Payload->FileExtension, PDO::PARAM_STR],
				[$Payload->Width, PDO::PARAM_STR],
				[$Payload->Height, PDO::PARAM_STR],
				[$Payload->Tags, PDO::PARAM_STR]
			]);

			echo json_encode($Image);
		}
	}

// 	function UpdateDialogText($Payload) {
// 		$Payload->Name = str_replace("'", "''", $Payload->Name);

// 		$SQL = <<<SQL
// 		EXEC Storyline.UpdateDialogText
// 		SET
// 			Name = '{$Payload->Name}',
// 			ModifiedDateTime = GETDATE()
// 		OUTPUT
// 			Inserted.CardID,
// 			Inserted.Name
// 		WHERE
// 			CardID = {$Payload->CardID}
// SQL;
// 		if(isset($Payload->CardID) && isset($Payload->Name)) {
// 			$result = API::query($SQL);

// 			echo json_encode($result);
// 		}
// 	}
?>