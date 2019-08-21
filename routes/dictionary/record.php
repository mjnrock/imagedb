<?php if(isset($_GET["name"]) && isset($_GET["uuid"])): ?>
	<?php
		$Table = $_GET["name"];
		$UUID = $_GET["uuid"];

		${"Factory$Table"} = (new ModelFactory("FuzzyKnights", "ImageDB", $Table))->Connect(API::$DB);
		$RawData = ${"Factory$Table"}->CreateFromFetch([ $UUID ]);
		$Data = array_values($RawData);
	?>

	<form uuid="<?= $UUID; ?>">
		<?php foreach($Table::COLUMNS as $i => $column): ?>
			<div class="mb-4">
				<input
					<?= $i === 0 ? "disabled" : null; ?>
					type="text"
					class="text-center"
					name=<?= $column; ?>
					value="<?= $Data[0]->$column; ?>"
					state="-1"
					data-role="input"
					data-prepend="<span class='text-bold'><?= $column; ?></span>"
				/>
			</div>
		<?php endforeach; ?>
		<button id="record-save" class="btn">Save</button>
		<button id="record-cancel" class="btn">Cancel</button>
	</form>

	<a class="mt-4 button primary" href="/table?name=<?= $Table; ?>">Back to <?= $Table; ?></a>
<?php else: ?>
	<h3>"name", "uuid" parameters must be set</h3>
<?php endif; ?>