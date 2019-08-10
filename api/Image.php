<?php
    require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
    
    extract($_POST);
    $error = array();
    $extension = array("png", "gif", "jpeg", "jpg", "svg");
    foreach($_FILES["files"]["tmp_name"] as $key => $tmp_name) {
        $file_name_ext = $_FILES["files"]["name"][$key];
        $file_tmp = $_FILES["files"]["tmp_name"][$key];
        $ext = pathinfo($file_name_ext, PATHINFO_EXTENSION);
        $file_name = pathinfo($_FILES["files"]["name"][$key], PATHINFO_FILENAME);
        $file_details = getimagesize($_FILES["files"]["tmp_name"][$key]);

        if(in_array($ext, $extension)) {
            MergeImage([
                "FilePath" => "{{MAIN}}",
                "FileName" => $file_name,
                "FileExtension" => $ext,
                "Width" => $file_details[0],
                "Height" => $file_details[1],
                "Tags" => $ext,
                "UUID" => null
            ]);
            
            move_uploaded_file($file_tmp = $_FILES["files"]["tmp_name"][$key], "{$file_name_ext}");
        } else {
            array_push($error, "$file_name_ext, ");
        }
    }

	function MergeImage($Payload) {        
        $Image = API::$DB->PDOStoredProcedure("MergeImage", [
            [ isset($Payload[ "FilePath" ]) ? $Payload[ "FilePath" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "FileName" ]) ? $Payload[ "FileName" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "FileExtension" ]) ? $Payload[ "FileExtension" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "Width" ]) ? $Payload[ "Width" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "Height" ]) ? $Payload[ "Height" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "Tags" ]) ? $Payload[ "Tags" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "UUID" ]) ? $Payload[ "UUID" ] : "NULL", PDO::PARAM_STR ]
        ]);

        echo json_encode($Image);
    }

	function UpdateImageTags($Payload) {        
        $Image = API::$DB->PDOStoredProcedure("UpdateImageTags", [
            [ isset($Payload[ "UUID" ]) ? $Payload[ "UUID" ] : "NULL", PDO::PARAM_STR ],
            [ isset($Payload[ "Tags" ]) ? $Payload[ "Tags" ] : "NULL", PDO::PARAM_STR ]
        ]);

        echo json_encode($Image);
    }
    
    header("Location: /image");
?>