<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Estatísticas - Grupo de Cine</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

	<style type="text/css">
		.stats-section {
			min-height: 100vh;
			padding-bottom: 10vh;
		}
		.stats-section .container {
			padding: 2% 5%;
		}
		.progress {
			margin: 1% 5%;
		}
		.progress-nick {
			position: absolute;
			left: 0;
			right: 0;
		}
		.table-rate {
			padding: 0% 2%;
			font-size: 0.9em;
		}
		caption {
			padding: 0em !important;
		}
	</style>

</head>
<body class="bg-light">

<?php
	include $_SERVER['DOCUMENT_ROOT']."/pages/_navbar.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/movie.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/viewer.php";
	if(!isset($_SESSION)) { session_start(); }

	$movieDOM = new Movie();
	$viewerDOM = new Viewer();

	$allMovies = $movieDOM->getAllMovies();
	$allViewers = $viewerDOM->getAllNicks();

	$watchedMovies = 0;
	$scheduledMovies = 0;
	$viewerRecomends = array();
	$viewerScheduled = array();
	
	foreach ($allViewers as $viewer) {
		$viewerRecomends[$viewer] = 0;
		$viewerScheduled[$viewer] = 0;
	}

	$now = time();
	foreach ($allMovies as $movie) {
		if($movie->getChoosedBy() != null) {
			if($movie->getWatchAt() + $ONE_DAY > time()) {
				$scheduledMovies++;
				$viewerScheduled[$movie->getChoosedBy()]++;
			}
			else {
				$watchedMovies++;
				$viewerRecomends[$movie->getChoosedBy()]++;
			}
		}
	}
?>
	<div class="stats-section bg-light">
		<div class="container">
			<h2 class="text-info">Dados Gerais</h2>
			<br>
			<br>
			<h3 class="text-info text-center font-weight-light"><?php echo $watchedMovies; ?> filmes assistidos</h3>
			<br>
<?php 
	foreach ($allViewers as $viewer) {
		$pc = ($watchedMovies == 0) ? 0.0 : ($viewerRecomends[$viewer] * 100.0) / $watchedMovies;
?>
			<div class="progress bg-secondary">
				<div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $pc; ?>%" aria-valuemin="0" aria-valuemax="100">
					<span class="text-light text-center progress-nick"><?php echo $viewerRecomends[$viewer]." / @".$viewer; ?></span>
				</div>
			</div>
<?php } ?>
			<br>
			<br>
			<h3 class="text-info text-center font-weight-light"><?php echo $scheduledMovies; ?> filmes agendados</h3>
			<br>
<?php 
	foreach ($allViewers as $viewer) {
		$pc = ($scheduledMovies == 0) ? 0.0 : ($viewerScheduled[$viewer] * 100.0) / $scheduledMovies;
?>
			<div class="progress bg-secondary">
				<div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $pc; ?>%" aria-valuemin="0" aria-valuemax="100">
					<span class="text-light text-center progress-nick"><?php echo $viewerScheduled[$viewer]." / @".$viewer; ?></span>
				</div>
			</div>
<?php } ?>
		</div>
	</div>


<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/movieRate.php";
	$movieRateDOM = new MovieRate();

	$mostRatedByUsMovies = $movieDOM->getAllMovies($movieDOM::ORDER_BY_OUR_RATE);
	$hasZeroMostRatedByUs = (count($mostRatedByUsMovies) == 0);
?>
	<div class="stats-section bg-info">
		<div class="container">
			<h2 class="text-light">Melhores Notas</h2>
			<br>
			<div class="row">
				<div class="col-lg-6 table-rate">
					<h3 class="text-light text-center font-weight-light">Votados por nós</h3>
					<br>
					<div id="ajax_ourRateTable">
<?php include_once $_SERVER['DOCUMENT_ROOT']."/actions/ajax_ourRateTable.php"; ?>
					</div>
<script type="text/javascript">
	
	function reloadOurTable() {
		var select = document.getElementById("ourRate");
		$.post("actions/ajax_ourRateTable.php", "viewerIndex="+select.value, function( data ) {
			var div = document.getElementById("ajax_ourRateTable");
			div.innerHTML = data;
		});
	}

</script>
				</div>
				<div class="col-lg-6 table-rate">
					<h3 class="text-light text-center font-weight-light">Votados pela crítica</h3>
					<br>
					<div id="ajax_criticRateTable">
<?php include_once $_SERVER['DOCUMENT_ROOT']."/actions/ajax_criticRateTable.php"; ?>
					</div>
<script type="text/javascript">
	
	function reloadCriticTable() {
		var select = document.getElementById("sourceRate");
		$.post("actions/ajax_criticRateTable.php", "sourceIndex="+select.value, function( data ) {
			var div = document.getElementById("ajax_criticRateTable");
			div.innerHTML = data;
		});
	}

</script>
				</div>
				<div class="col text-center" hidden>
					<button type="button" class="btn btn-outline-light">Ver Lista Completa</button>
				</div>
			</div>
		</div>
	</div>
	<div class="stats-section bg-light" hidden>
		<div class="container">
			<h2 class="text-info">Melhores Recomendações</h2>
		</div>
	</div>
	<div class="stats-section bg-info" hidden>
		<div class="container">
		</div>
	</div>


</body>
</html>