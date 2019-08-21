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
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($Data as $record): ?>
				<tr uuid="<?= $record->UUID; ?>">
					<?php foreach($record as $key => $value): ?>
						<?php if($key !== "Meta"): ?>
							<td><?= $value; ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
					<td>
						<button class="button warning button-edit">E</button>
						<button class="button alert button-delete">X</button>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<script>
		$(document).ready(function() {
			$("#table").DataTable();

			$(".button-edit").on("click", function(e) {
				location.href = "/record?name=<?= $Table; ?>&uuid=" + $(this).parent().parent().attr("uuid");
			});
		});
	</script>
<?php else: ?>
	<h3>"name" parameter must be set</h3>
<?php endif; ?>