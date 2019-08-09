<?php
	$Data = API::Camera();
?>

<table id="table">
	<thead>
		<tr>
			<th>CameraID</th>
			<th>Name</th>
			<th>X</th>
			<th>Y</th>
			<th>Z</th>
			<th>Pitch</th>
			<th>Yaw</th>
			<th>Roll</th>
			<th>Tags</th>
			<th>UUID</th>
		</tr>
	</thead>
</table>

<script>
	$(document).ready(function() {
    	$("#table").DataTable({
			data: <?= json_encode($Data); ?>,
			columns: [
				{ data: "CameraID" },
				{ data: "Name" },
				{ data: "X" },
				{ data: "Y" },
				{ data: "Z" },
				{ data: "Pitch" },
				{ data: "Yaw" },
				{ data: "Roll" },
				{ data: "Tags" },
				{ data: "UUID" }
			]
		});
	});
</script>

<button class="btn">Click Me</button>

<script>
	$(document).ready(function() {
		$("button.btn").on("click", function(e) {
			AJAX("Image", "MergeImage", {
				FilePath: "C:\\temp",
				FileName: "name",
				FileExtension: "png",
				Width: 15,
				Height: 20,
				Tags: "bob,cat"
			}, (e) => {
				if(e !== null && e !== void 0) {
					console.log(e);
					// window.location.href = `/scene/1?uuid=${ $(this).closest("p").attr("uuid") }`;
				}
			});
		});
	});
</script>


<form action="api/Image.php" method="post" enctype="multipart/form-data">
    <table width="100%">
        <tr>
            <td>Select Photo (one or multiple):</td>
            <td><input type="file" name="files[]" multiple/></td>
        </tr>
        <tr>
            <td colspan="2" align="center">Note: Supported image format: .jpeg, .jpg, .png, .gif</td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="submit" value="Create Gallery" id="selectedButton"/></td>
        </tr>
    </table>
</form>