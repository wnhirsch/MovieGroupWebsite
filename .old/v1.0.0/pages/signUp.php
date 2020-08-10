<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Cadastro - Grupo de Cine</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

	<style type="text/css">
		.content {
			padding-top: 10%;
			text-align: center;
		}
		.form-control {
			width: 75%;
			max-width: 400px;
		}
		#errorMsg {
			font-size: 0.9em;
		}
	</style>

</head>
<body class="bg-light">
	
<?php
	include $_SERVER['DOCUMENT_ROOT']."/pages/_navbar.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/token.php";

	if(!isset($_SESSION)) { session_start(); }

	if(!isset($_GET["token"])) {
		header("Location: /index.php");
	}

	$tokenDOM = new Token();
	$token = $_GET["token"];
	if(!$tokenDOM->validateToken($token)) {
		header("Location: /index.php?error=504");
	}
	
	$class = "form-control mx-auto my-1";
	$errorMsg = "";
	if(isset($_POST['nick']) && isset($_POST['pass'])) {
		$nick = $_POST['nick'];
		$pass = $_POST['pass'];
		$remember = isset($_POST['remember']);
		unset($_POST);

		$matches = array();
		$matchReturn = preg_match($NICK_REGEX, $nick, $matches);
		if($matchReturn != 1 || count($matches) < 1 || $matches[0] != $nick) {
			$class .= " is-invalid";
			$errorMsg = "Apelido inválido! Escolha outro dentro do seguinte padrão:<br>
			* Entre 5 a 20 caracteres<br>
			* Pode conter letras de A a Z, maiúsculas ou minúsculas<br>
			* Pode conter números de 0 a 9, exceto no início<br>
			* Pode conter os caractéres especiais '@', '_' e '-'";
		}

		$matches = array();
		$matchReturn = preg_match($PASS_REGEX, $pass, $matches);
		if($matchReturn != 1 || count($matches) < 1 || $matches[0] != $pass) {
			$class .= " is-invalid";
			$errorMsg = "Senha inválida! Escolha outra dentro do seguinte padrão:<br>
			* Entre 8 a 16 caracteres<br>
			* Pode conter letras de A a Z, maiúsculas ou minúsculas<br>
			* Pode conter números de 0 a 9<br>
			* Pode conter o caractere especial '_'";
		}

		if($errorMsg == "") {
			$viewer = new Viewer();
			if($viewer->signUp($nick, $pass, $token)) {
				$_SESSION['viewer'] = $viewer;
				if($remember) {
					setcookie("user", $viewer->getCookie(), time() + COOKIE_LIMIT, "/");
				}

				header("Location: /index.php?section=main");
			}
			else {
				header("Location: /index.php?error=504");
			}
		}
	}
?>

	<div class="container">
	<div class="row">
	<div class="col-lg-12">
	<div class="content">
	<h5 class="text-dark">Informe um Apelido e Senha:</h5><br>
	<form method="POST">
		<input type="text" class="<?php echo $class; ?>" name="nick" placeholder="Apelido" required>
    	<input type="password" class="<?php echo $class; ?>" name="pass" placeholder="Senha" required>
    	<div class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" name="remember" id="remember">
			<label class="custom-control-label text-dark" for="remember">Mantenha-me conectado</label>
		</div>
		<br><button id="invalidLogin" type="submit" class="btn btn-primary">Entrar</button>
	</form>
	<br><span class="text-danger font-weight-light" id="errorMsg"><?php echo $errorMsg; ?></span>
 	</div>
	</div>
	</div>
	</div>




</body>
</html>