<?php if(isset($_GET["name"]) && isset($_GET["uuid"])): ?>
	<?php
		$Table = $_GET["name"];
		$UUID = $_GET["uuid"];

		${"Factory$Table"} = (new ModelFactory("FuzzyKnights", "ImageDB", $Table))->Connect(API::$DB);
		$RawData = ${"Factory$Table"}->CreateFromFetch([ $UUID ]);
		$Data = array_values($RawData);
	?>

	<div class="remark info">
		<span class="text-bold">Bold</span> labels cannot be <code>NULL</code>
	</div>
	<form class="grid" uuid="<?= $UUID; ?>">
		<div class="mb-4 row text-center">
			<span class="cell-10">Current Value</span>
			<span class="cell-2">Default</span>
		</div>
		<?php foreach(${"Factory$Table"}->TableConnector->Columns as $i => $column): ?>
			<div class="mb-4 row text-center">
				<div class="cell-2">
					<span class='<?= $column["meta"]->isNullable ? "" : "text-bold"; ?>'><?= $column["name"]; ?></span>
				</div>
				<input
					<?= $i === 0 || $column["name"] === "UUID" ? "disabled" : null; ?>
					type="text"
					class="cell-8 text-center"
					name=<?= $column["name"]; ?>
					value="<?= $Data[0]->$column["name"]; ?>"
					state="-1"
					<?= $column["meta"]->isNullable ? null : "x-not-null"; ?>
					data-role="input"
				/>
				<div class="cell-2">
					<?php if(strlen($column["meta"]->default) > 0): ?>
						<code><?= $column["meta"]->default; ?></code>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="row">
			<input type="button" id="record-save" class="cell button mr-2" value="Save" />
			<input type="button" id="record-cancel" class="cell button success" value="Cancel" />
		</div>
	</form>

	<a class="mt-4 button primary" href="/table?name=<?= $Table; ?>">Back to <?= $Table; ?></a>

	<script>
		$(document).ready(function() {
			//TODO: ModelGenerator.php should also make ES6 JS Classes for use here
			let data = {};	//TODO: Translate PHP data into JSON object/JS Model of Table

			$("#record-Save").on("click", function(e) {
				//TODO: Send API call to CRUD the changes
			});

			$("#record-cancel").on("click", function(e) {
				location.href = "/table?name=<?= $Table; ?>";
			});
		});
	</script>
<?php else: ?>
	<h3>"name", "uuid" parameters must be set</h3>
<?php endif; ?>