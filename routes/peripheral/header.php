<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/index.php";
	// require_once "{$_SERVER["DOCUMENT_ROOT"]}/models/index.php";
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/metro/4.2.47/js/metro.min.js" integrity="sha256-31Bt4IwI5yK4yhDFvGuH0rfwlGP1yUlTN72Ulg1NEyQ=" crossorigin="anonymous"></script>

		<title>ImageDB</title>
		
		<script>			
			function AJAX(domain, action, content, callback) {
				callback = !!callback ? callback : function(e){};
				$.ajax({
					url: `/api/${domain}.php`,
					data: {
						Action: action,
						Payload: JSON.stringify(content)
					},
					success: callback
				});
			}
		</script>
	</head>
	<body>
		<?php require_once "{$_SERVER["DOCUMENT_ROOT"]}/routes/peripheral/navbar.php"; ?>
		
		<div class="container">