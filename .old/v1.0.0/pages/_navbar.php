<?php
	if(!isset($_SESSION)) { session_start(); }

	$hidden = "";
	$nick = "";
	if(!isset($_SESSION["viewer"])) {
		$hidden = " hidden ";
	}
	else {
		$nick = $_SESSION["viewer"]->getNick();
	}
?>

<nav class="navbar sticky-top navbar-dark bg-dark">
	<div class="container">
		<a class="navbar-brand" href="/">
			<img src="media/discord_icon.svg" width="30" height="30" class="d-inline-block align-top" alt="" loading="lazy">
			Grupo de Cine
		</a>

		<div class="form-inline navbar-right">
			<a class="nav-item nav-link text-white-50 disabled" href="#" <?php echo $hidden; ?> role="button">Olá, <?php echo $nick; ?></a>
			<a class="nav-item nav-link text-info" href="https://discord.com/app" role="button" target="blank">Discord</a>
			<a class="nav-item nav-link text-white" href="?section=invite" <?php echo $hidden; ?> role="button">Convidar</a>
			<a class="nav-item nav-link text-white" href="?section=statistics" <?php echo $hidden; ?> role="button">Estatísticas</a>
			<a class="nav-item nav-link text-danger" href="?exit=true" <?php echo $hidden; ?> role="button">Sair</a>
		</div>
	</div>
</nav>