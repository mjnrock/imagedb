<h2 class="text-center">Execute SQL</h2>
<hr />
<br />

<div>
	<div class="remark info">
		<div class="row">
			<div class="cell">
				<span>This currently only supports <span class="text-bold">one (1)</span> result set per execution</span>
			</div>		
			<div class="cell">		
				<kbd>Ctrl</kbd> + <kbd>Enter</kbd> : Execute query
				<br />
				<kbd>Ctrl</kbd> + <kbd>Delete</kbd> : Clear textarea
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" href="/assets/codemirror/lib/codemirror.css" />
<script src="/assets/codemirror/lib/codemirror.js"></script>
<script src="/assets/codemirror/addon/edit/matchbrackets.js"></script>
<script src="/assets/codemirror/mode/sql/sql.js"></script>
<link rel="stylesheet" href="/assets/codemirror/addon/hint/show-hint.css" />
<script src="/assets/codemirror/addon/hint/show-hint.js"></script>
<script src="/assets/codemirror/addon/hint/sql-hint.js"></script>
<style>
	.CodeMirror {
		border-top: 1px solid black;
		border-bottom: 1px solid black;
	}
</style>

<div>
	<textarea
		id="code"
		name="code"
	></textarea>
	
	<div class="row mt-2">
		<button id="execute-query" class="cell button light">Execute</button>
	</div>

	<div
		id="results-holder"
		class="mt-2 w-100"
	>
		<code
			id="results-text"
		></code>

		<table
			id="results-table"
		>
		</table>
	</div>
</div>

<script>
	window.onload = function() {
		var mime = 'text/x-mariadb';
		// get mime type
		if (window.location.href.indexOf('mime=') > -1) {
			mime = window.location.href.substr(window.location.href.indexOf('mime=') + 5);
		}

		window.editor = CodeMirror.fromTextArea(document.getElementById('code'), {
			mode: mime,
			indentWithTabs: true,
			smartIndent: true,
			lineNumbers: true,
			matchBrackets : true,
			autofocus: true,
			extraKeys: {
				"Ctrl-Space": "autocomplete"
			},
			hintOptions: {
				tables: {
					users: ["name", "score", "birthDate"],
					countries: ["name", "population", "size"]
				}
			}
		});
	};

	$(document).ready(function() {
		$("#results-text").hide();
		$("#results-table").hide();

		function resetTable() {
			if($.fn.DataTable.isDataTable("#results-table")) {
				$("#results-table").DataTable().destroy(true);
				$("#results-holder").append("<table id='results-table'>");
			}
		}

		function Execute() {
			return IO_AJAX(window.editor.getValue(), (data) => {
				resetTable();

				try {
					let json = JSON.parse(data),
						columns = [],
						rows = [];

					if(Array.isArray(json)) {
						let keys = Object.keys(json[0]);

						columns = keys.map(k => ({
							title: `${ k }`
						}));

						rows = json.map(r => Object.values(r));
					}

					$("#results-table").DataTable({
						data: rows,
						columns
					});
					
					$("#results-text").hide();
					$("#results-table").show();
				} catch(e) {
					console.info(e);

					try {
						let d2 = JSON.stringify(JSON.parse(data), null, 2);

						$("#results-text").text(d2);
					} catch(e) {
						$("#results-text").text(data);
					}

					$("#results-text").show();
					$("#results-table").hide();
				}
			});
		}

		window.editor.addKeyMap({
			"Ctrl-Enter": () => Execute(),
			"Ctrl-Delete": () => window.editor.getDoc().setValue(""),
		});

		$("#execute-query").on("click", function(e) {
			Execute();
		});
	});
</script>