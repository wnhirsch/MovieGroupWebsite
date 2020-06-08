<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Convite - Grupo de Cine</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	
	<style type="text/css">
		body{
			height: 100%;
		}
		#token-container {
			margin-top: 20vh;
		}
	</style>

</head>
<body class="bg-light">

<?php
	include $_SERVER['DOCUMENT_ROOT']."/pages/_navbar.php";
	if(!isset($_SESSION)) { session_start(); }
?>

	<div class="container" id="token-container">
		<p id="infoText" style="text-align: center;">Para convidar novos usuários, você precisa criar um novo Token de convite.<br>Clique no botão abaixo para gerar um Token:</p>
		<form class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
			<div class="input-group mb-3">
				<input id="tokenURL" type="text" class="form-control" value="" readonly>
				<div class="input-group-append">
					<button id="copyButton" disabled="true" class="btn btn-outline-secondary" onclick="copyToken();" type="button">Copiar Link</button>
				</div>
			</div>
			<div class="input-group mb-3">
				<button id="generateButton" class="btn btn-secondary mx-auto" onclick="generateToken();" type="button">Gerar Token</button>
			</div>
		</form>
	</div>

	<script>

		var isFirstToken = true;

		function generateToken() {
			$.post("actions/ajax_generateToken.php", "", function( data ) {
				if(isFirstToken) {
					isFirstToken = false;

					var generateButton = document.getElementById("generateButton");
					var infoText = document.getElementById("infoText");

					generateButton.innerHTML = "Gerar novo Token";
					infoText.innerHTML = "Novo token gerado! Compartilhe esse link com quem você queira dentro do nosso grupo:";
				}

				var tokenURL = document.getElementById("tokenURL");
				var copyButton = document.getElementById("copyButton");

				tokenURL.value = data;
				copyButton.disabled = false;
			});
		}

		function copyToken() {
			var copyText = document.getElementById("tokenURL");
			var copyButton = document.getElementById("copyButton");

			copyText.select();
			copyText.setSelectionRange(0, 99999)
			document.execCommand("copy");

			copyButton.innerHTML = "Copiado!";
			setTimeout(
				function() {
					var copyButton = document.getElementById("copyButton");
					copyButton.innerHTML = "Copiar Link";
				}, 3000);
		}

	</script>




</body>
</html>