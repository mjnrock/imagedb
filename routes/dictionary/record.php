<?php if(isset($_GET["name"]) && isset($_GET["uuid"])): ?>
	<?php
		$Table = $_GET["name"];
		$UUID = $_GET["uuid"];

		${"Factory$Table"} = (new ModelFactory("FuzzyKnights", "ImageDB", $Table))->Connect(API::$DB);
		$RawData = ${"Factory$Table"}->CreateFromFetch([ $UUID ]);
		$Data = array_values($RawData);
	?>

	<div>
		<div class="remark info">
			<span class="text-bold">Bold</span> labels cannot be <code>NULL</code>
		</div>
		<div class="remark info">
			Data types are: 
			<code class="fg-crimson">String</code>,
			<code class="fg-cobalt">Number</code>,
			<code class="fg-indigo">Boolean</code>, and
			<code class="fg-steel">Datetime</code>
		</div>
	</div>
	
	<form class="grid" uuid="<?= $UUID; ?>">
		<script>
			function onClearClick(curr, next) {
				$(this).attr("state", -1);
				$(this).removeClass("bg-cyan fg-white");
			}
		</script>
		<div class="mb-4 row text-center">
			<span class="cell-10">Current Value</span>
			<span class="cell-2">Default</span>
		</div>
		<?php foreach(${"Factory$Table"}->TableConnector->Columns as $i => $column): ?>
			<div class="mb-4 row text-center">
				<div class="cell-2">
					<span class="
						<?= $column["meta"]->isNullable ? "" : "text-bold"; ?>
						<?= $column["meta"]->isString ? "fg-crimson" : null; ?>
						<?= $column["meta"]->isNumber ? "fg-cobalt" : null; ?>
						<?= $column["meta"]->isBoolean ? "fg-indigo" : null; ?>
						<?= $column["meta"]->isDatetime ? "fg-steel" : null; ?>
					"><?= $column["name"]; ?></span>
				</div>
				<input
					<?= $i === 0 || $column["name"] === "UUID" ? "disabled" : null; ?>
					type="<?= $column["meta"]->isString ? "text" : ($column["meta"]->isNumber ? "number" : ($column["meta"]->isBoolean ? "text" : ($column["meta"]->isDatetime ? "datetime" : "text"))); ?>"
					class="cell-8 text-center"
					name=<?= $column["name"]; ?>
					value="<?= $Data[0]->$column["name"]; ?>"
					state="-1"
					data-role="input"
					data-default-value="<?= $Data[0]->$column["name"]; ?>"
					data-on-clear-click="onClearClick"
				/>
				<div class="cell-2">
					<?php if(strlen($column["meta"]->default) > 0): ?>
						<code class="fg-steel text-bold"><?= $column["meta"]->default; ?></code>
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
			const COLUMNS = (() => {
				let cols = <?= json_encode(${"Factory$Table"}->TableConnector->Columns); ?>,
					res = {};

				cols.forEach((v, i) => {
					res[ cols[i]["name"] ] = cols[ i ];
				});

				return res;
			})();
			
			let data = {};

			$("form input").on("change", function(e) {
				let state = 1;
				$(this).attr("state", state);
				
				if(state === 1) {
					$(this).addClass("bg-cyan fg-white");
				} else {
					$(this).removeClass("bg-cyan fg-white");
				}
			});

			$("#record-save").on("click", function(e) {
				let data = {};
				$("input[state=1]").each((i, v) => {
					let [ name, value ] = [ $(v).attr("name"), $(v).val() ];

					if(COLUMNS[ name ]["meta"]["isString"] == 1) {
						data[ name ] = `'${ value}'`;
					} else {
						data[ name ] = value;
					}
				});

				CRUD_AJAX(`<?= $Table; ?>`, 2, data, `UUID='<?= $UUID; ?>'`, (data) => {
					location.reload();
				});
			});

			$("#record-cancel").on("click", function(e) {
				location.href = "/table?name=<?= $Table; ?>";
			});
		});
	</script>
<?php else: ?>
	<h3>"name", "uuid" parameters must be set</h3>
<?php endif; ?>