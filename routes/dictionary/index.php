<h2>Select a Table</h2>
<hr />

<ul>
	<?php foreach(API::$Tables as $table): ?>
		<li>
			<a href="/table?name=<?= $table; ?>"><?= $table; ?></a>
		</li>
	<?php endforeach; ?>
</ul>