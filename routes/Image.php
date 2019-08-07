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
    	$("table").DataTable({
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