<?php if(isset($_GET["name"]) && isset($_GET["uuid"])): ?>
	<?php
		$Table = $_GET["name"];
		$UUID = $_GET["uuid"];

		${"Factory$Table"} = (new ModelFactory("FuzzyKnights", "ImageDB", $Table))->Connect(API::$DB);
		$RawData = ${"Factory$Table"}->CreateFromFetch([ $UUID ]);
		$Data = array_values($RawData);
	?>

	<form>
		<?php foreach($Table::COLUMNS as $column): ?>
			<div>
				<span><?= $column; ?></span>
				<input type="text" name=<?= $column; ?> value="<?= $Data[0]->$column; ?>" state="-1" />
			</div>
		<?php endforeach; ?>
		<button id="record-save" class="btn">Save</button>
		<button id="record-cancel" class="btn">Cancel</button>
	</form>
<?php else: ?>
	<h3>"name", "uuid" parameters must be set</h3>
<?php endif; ?>