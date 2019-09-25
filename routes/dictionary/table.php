<?php if(isset($_GET["name"])): ?>
	<?php
		$Table = $_GET["name"];
		${"Factory$Table"} = (new ModelFactory("FuzzyKnights", "ImageDB", $Table))->Connect(API::$DB);
		$RawData = ${"Factory$Table"}->CreateFromCRUD(1);
		$Data = array_values($RawData);
	?>

	<div class="row">
		<a class="cell button primary" href="/record?name=<?= $Table; ?>">Insert New Record</a>
	</div>
	<br />

	<div data-role="accordion" data-one-frame="true" data-show-active="true">
		<div class="frame text-center">
			<div class="heading">Column Definition</div>
			<div class="content">
				<ul class="items-list grid">
					<li>
						<div class="row text-upper">
							<div class="cell text-bold">Column</div>
							<div class="cell text-bold">Data Type</div>
							<div class="cell text-bold">Default</div>
							<div class="cell text-bold">Nullability</div>
						</div>
					</li>
					<?php foreach(${"Factory$Table"}->TableConnector->Columns as $i => $column): ?>
						<li>
							<div class="row">
								<span class="cell
									<?= $column["meta"]->isNullable ? "" : "text-bold"; ?>
								"><?= $column["name"]; ?></span>
								<code class="cell
									<?= $column["meta"]->isString ? "fg-crimson" : null; ?>
									<?= $column["meta"]->isNumber ? "fg-cobalt" : null; ?>
									<?= $column["meta"]->isBoolean ? "fg-indigo" : null; ?>
									<?= $column["meta"]->isDatetime ? "fg-steel" : null; ?>
								" style="font-weight: normal; font-style: italic;"><?= $column["type"]; ?></code>
								<code class="cell fg-steel text-bold"><?= strlen($column["meta"]->default) > 0 ? $column["meta"]->default : null; ?></code>
								<code class="cell fg-steel <?= $column["meta"]->isNullable ? "" : "text-bold"; ?>"><?= $column["meta"]->isNullable ? "NULL" : "NOT NULL"; ?></code>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="frame active">
			<div class="heading">Data</div>        
			<div class="content">
				<table id="table">
					<thead>
						<tr>
							<?php foreach(${"Factory$Table"}->TableConnector->Columns as $i => $column): ?>
								<th>
									<span class="text-bold"><?= $column["name"]; ?></span>
								</th>
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
			</div>
		</div>

	</div>

	<script>
		$(document).ready(function() {
			$("#table").DataTable();

			$(".button-edit").on("click", function(e) {
				location.href = "/record?name=<?= $Table; ?>&uuid=" + $(this).parent().parent().attr("uuid");
			});
			$(".button-delete").on("click", function(e) {
				CRUD_AJAX(`<?= $Table; ?>`, 3, null, `UUID='${ $(this).parent().parent().attr("uuid") }'`, (data) => {
					location.reload();
				});
			});
		});
	</script>
<?php else: ?>
	<h3>"name" parameter must be set</h3>
<?php endif; ?>