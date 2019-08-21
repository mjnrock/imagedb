<?php if(isset($_GET["name"])): ?>
	<?php
		$Table = $_GET["name"];
		${"Factory$Table"} = (new ModelFactory("FuzzyKnights", "ImageDB", $Table))->Connect(API::$DB);
		$RawData = ${"Factory$Table"}->CreateFromCRUD(1);
		$Data = array_values($RawData);
	?>

	<table id="table">
		<thead>
			<tr>
				<?php foreach($Table::COLUMNS as $column): ?>
					<th><?= $column; ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
	</table>

	<script>
		$(document).ready(function() {
			$("#table").DataTable({
				data: <?= json_encode($Data); ?>,
				columns: [
					<?php foreach($Table::COLUMNS as $column): ?>
						<?= "{ data: `$column` },"; ?>
					<?php endforeach; ?>
				]
			});
		});
	</script>
<?php else: ?>
	<h3>"name" parameter must be set</h3>
<?php endif; ?>