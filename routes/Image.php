<?php
	$Data = API::Image();
?>

<table id="table">
	<thead>
		<tr>
            <th>ImageID</th>
            <th>FilePath</th>
            <th>FileName</th>
            <th>FileExtension</th>
            <th>Width</th>
            <th>Height</th>
            <th>Tags</th>
            <th>UUID</th>
		</tr>
	</thead>
</table>

<?php foreach($Data as $Image): ?>
    <img
        class="db-image"
        src="api/<?= "{$Image[ "FileName" ]}.{$Image[ "FileExtension" ]}"; ?>"
        alt="<?= $Image[ "FileName" ]; ?>"
        uuid="<?= $Image[ "UUID" ]; ?>"
    />
    <ul class="db-image-tags">
        <?php foreach(explode(",", $Image[ "Tags" ]) as $Tag): ?>
            <li><?= $Tag; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>

<script>
	$(document).ready(function() {
    	$("#table").DataTable({
			data: <?= json_encode($Data); ?>,
			columns: [
				{ data: "ImageID" },
				{ data: "FilePath" },
				{ data: "FileName" },
				{ data: "FileExtension" },
				{ data: "Width" },
				{ data: "Height" },
				{ data: "Tags" },
				{ data: "UUID" }
			]
        });
	});
</script>

<form action="api/Image.php" method="post" enctype="multipart/form-data">
    <table width="100%">
        <tr>
            <td>Select Photo (one or multiple):</td>
            <td><input type="file" name="files[]" multiple /></td>
        </tr>
        <tr>
            <td colspan="2" align="center">Note: Supported image format: .jpeg, .jpg, .png, .gif, .svg</td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="submit" value="Upload" id="selectedButton"/></td>
        </tr>
    </table>
</form>