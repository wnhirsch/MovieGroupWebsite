<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>404 - Grupo de Cine</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	
	<style type="text/css">
		body{
			height: 100%;
		}
		.d-flex {
			padding-top: 5%;
		}
	</style>

</head>
<body class="bg-light">

<?php
	include $_SERVER['DOCUMENT_ROOT']."/pages/_navbar.php";
	if(!isset($_SESSION)) { session_start(); }

	$code = 404;
	$description = "Problema não reconhecido.<br>Contate os desenvolvedores para mais informações: 
		<a href='mailto:wnhirsch@inf.ufrgs.br'><i>wnhirsch@inf.ufrgs.br<i></a>";

	if(isset($_GET["error"])) {
		switch ($_GET["error"]) {
			case 500:
				$code = 500;
				$description = "Problema interno: Algo aconteceu ao efetuar uma requisição com o Banco de Dados.<br>Contate os desenvolvedores para mais informações: <a href='mailto:wnhirsch@inf.ufrgs.br'><i>wnhirsch@inf.ufrgs.br<i></a>";
				break;
			case 502:
				$code = 502;
				$description = "Problema na comunicação com a API: Alguma das requisições a servidores externos não obteve sucesso.<br>Contate os desenvolvedores para mais informações: <a href='mailto:wnhirsch@inf.ufrgs.br'><i>wnhirsch@inf.ufrgs.br<i></a>";
				break;
			case 504:
				$code = 504;
				$description = "Token expirado / não existe: entre em contato com o usuário que lhe enviou o link e pessa para ele gerar outro.";
				break;
		}
	}
?>

	<div class="d-flex justify-content-center">
		<p style="text-align: center;">
			<span style="font-size: 10em;"><?php echo $code; ?></span>
			<br>
			<span><?php echo $description; ?></span>
		</p>
	</div>

</body>
</html>