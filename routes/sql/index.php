<h2 class="text-center">Execute SQL</h2>
<hr />
<br />

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

<form>
	<textarea
		id="code"
		name="code"
	></textarea>
	
	<div class="row mt-2">
		<a class="cell button light" href="#">Execute</a>
	</div>
</form>

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
			extraKeys: {"Ctrl-Space": "autocomplete"},
			hintOptions: {tables: {
			users: ["name", "score", "birthDate"],
			countries: ["name", "population", "size"]
			}}
		});
	};
</script>