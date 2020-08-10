<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/movie.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/criticRate.php";
	if(!isset($_SESSION)) { session_start(); }

	$criticRateDOM = new CriticRate();

	$movie = null;
	if (isset($_GET['id']) && $_GET['id'] != "") {
		$movieId = $_GET['id'];

		$movie = new Movie();
		$movie = $movie->getMovie($movieId);
		if ($movie == null) {
			header("Location: /index.php?error=502");
		}
		if($movie->criticRateList == null){
			$movie->criticRateList = $criticRateDOM->getAllCriticRates($movie->getID());
		}
	}
	else {
		header("Location: /index.php");
	}
?>

	<title><?php echo $movie->getTitle(); ?> - Grupo de Cine</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

	<style type="text/css">
		.modal, .container-movie {
			font-size: 0.8em !important;
		}
		.img-movie {
			min-height: 50vh;
			background-size: contain;
			background-repeat: no-repeat;
			background-position: 50% 50%;
		}
		#rateValue {
			margin-top: 5%;
			margin-left: 5%;
			width: 90%;
		}
		.btn-outline-warning {
			color: #ffc107 !important; 
		}
		.btn-outline-warning:hover {
			color: inherit !important;
		}
	</style>

</head>
<body class="bg-light">

<?php
	include $_SERVER['DOCUMENT_ROOT']."/pages/_navbar.php";

	$movieRateDOM = new MovieRate();

	if (isset($_POST['addMovie']) && isset($_POST['date']) && $_POST['addMovie'] && isset($_SESSION["viewer"]) && $movie->getChoosedBy() == null) {
		$movie->save($_SESSION["viewer"]->getNick(), strtotime($_POST['date']));
		unset($_POST);
	}
	else if (isset($_POST['remMovie']) && $_POST['remMovie'] && isset($_SESSION["viewer"])) {
		if($movie->getChoosedBy() == $_SESSION["viewer"]->getNick()) {
			$movie->remove();
			unset($_POST);
		}
	}
	else if (isset($_POST['newDate']) && isset($_SESSION["viewer"])) {
		if($movie->getChoosedBy() == $_SESSION["viewer"]->getNick()) {
			$movie->changeDate(strtotime($_POST['newDate']));
			unset($_POST);
		}
	}
	else if (isset($_POST['rateValue']) && isset($_SESSION["viewer"])) {
		$id = $movie->getID();
		$nick = $_SESSION["viewer"]->getNick();
		$rate = floatval($_POST['rateValue']);

		$movieRate = new MovieRate();
		if($movieRateDOM->wasRated($id, $nick)) {
			$movieRate->update($id, $nick, $rate);
		}
		else {
			$movieRate->save($id, $nick, $rate);
		}

		unset($_POST);
	}

	$ourRate = "??";
	$numVotes = 0;
	$aux = $movieRateDOM->getMovieRate($movie->getID());
	if($aux["rate"] !== null && $aux["votes"] !== null) {
		$ourRate = number_format($aux["rate"], 2, ",", "");
		$numVotes = $aux["votes"];
	}

	$myRate = "??";
	$rateButtonLabel = "Avaliar Filme";
	$aux = $movieRateDOM->getMyRate($movie->getID(), $_SESSION["viewer"]->getNick());
	if($aux !== null) {
		$myRate = number_format($aux, 2, ",", "");
		$rateButtonLabel = "Reavaliar Filme";
	}

	$rateTableHide = ($movie->criticRateList == null || count($movie->criticRateList) == 0) ? " hidden " : "";
	$addedOptionsHide = ($movie->getChoosedBy() == null) ? " hidden " : "";
	$nonAddedOptionsHide = ($movie->getChoosedBy() != null) ? " hidden " : "";
	$isMyMovie = ($movie->getChoosedBy() != null && isset($_SESSION["viewer"]) && $movie->getChoosedBy() == $_SESSION["viewer"]->getNick()) ? " hidden " : "";

	$dateMsg = "";
	$actualDate = "";
	if($movie->getWatchAt() != null) {
		$dateMsg = ($movie->getWatchAt() + $ONE_DAY > time()) ? " Marcado para o dia " : " Assistido no dia ";
		$dateMsg = $dateMsg.date("d/m/Y", $movie->getWatchAt()).".";
		$actualDate = date("Y-m-d", $movie->getWatchAt());
	}

	$about = "";
	if($movie->getRuntime() != null) {
		$about = $movie->getRuntime();
	}
	if($movie->getGenre() != null) {
		$about = ($about == "") ? "" : $about." | ";
		$about = $about.$movie->getGenre();
	}
?>

	<div class="container my-4 container-movie">
		<div class="card">
			<div class="row no-gutters">
				<div class="col-md-3 img-movie" style="background-image: url('<?php echo $movie->getPoster(); ?>');"></div>
				<div class="col-md-9">
					<div class="card-block px-4 pt-3">
						<a href="https://www.imdb.com/title/<?php echo $movie->getID(); ?>" target="blank" data-toggle="tooltip" data-placement="bottom" title="Ver no IMDb">
							<h3 class="card-title"><?php echo $movie->getTitle()." (".$movie->getYear().")"; ?></h3>
						</a>
						<span class="card-text text-secondary"><?php echo $about; ?></span><br><br>
						<p class="card-text my-1 mx-4 font-weight-light"><?php echo $movie->getPlot(); ?></p><br>
						<span class="card-text"><b>Diretor:</b> <?php echo $movie->getDirector(); ?></span><br>
						<span class="card-text"><b>Atores:</b> <?php echo $movie->getActors(); ?></span><br>
						<span class="card-text"><b>Indicações:</b> <?php echo $movie->getAwards(); ?></span>
						<span <?php echo $addedOptionsHide; ?> class="rate-right"><br><br><b>Nossa Nota:</b> <?php echo $ourRate; ?> / 5</span>
						<span <?php echo $addedOptionsHide; ?> class="card-text font-weight-light"> (<?php echo $numVotes; ?> avaliações)</span>
						<span <?php echo $addedOptionsHide; ?> class="rate-right"><br><b>Minha Nota:</b> <?php echo $myRate; ?> / 5</span><br>
						<div class="container mt-3 mb-5">
							<table <?php echo $rateTableHide; ?> class="col-md-8 col-lg-6 table table-sm">
								<thead class="thead-dark">
									<tr>
										<th scope="col">Crítico</th>
										<th scope="col">Nota</th>
									</tr>
								</thead>
								<tbody>
<?php
	if($movie->criticRateList != null) {
		foreach ($movie->criticRateList as $criticRate) {
			$source = $criticRate->getSource();
			$rate = $criticRate->getRate()."/".$criticRate->getMax();
?>
									<tr>
										<th><?php echo $source; ?></th>
										<td><?php echo $rate; ?></td>
									</tr>
<?php
		}
	}
?>
								</tbody>
							</table>
						</div>
						<form class="mt-4" <?php echo $nonAddedOptionsHide; ?> action="?section=movie&id=<?php echo $movie->getID(); ?>" method="POST">
							<input type="hidden" name="addMovie" value="true">
							<input class="m-1" type="date" name="date" required pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" min="2020-01-01">
							<button class="btn btn-primary m-1" type="submit">Agendar Filme</button>
						</form>
						<form class="mt-4" <?php echo $addedOptionsHide; ?> action="?section=movie&id=<?php echo $movie->getID(); ?>" method="POST">
							<input type="hidden" name="remMovie" value="true">
							<span class="card-text font-italic">Filme agendado por <?php echo $movie->getChoosedBy(); ?>.<?php echo $dateMsg; ?></span><br>
							<button <?php echo ($isMyMovie) ? "" : "hidden"; ?> class="btn btn-danger m-1" type="submit">Remover Filme</button>
							<a class="btn btn-outline-info m-1" <?php echo ($isMyMovie) ? "" : "hidden"; ?> role="button" data-toggle="modal" data-target="#changeDateModal">Alterar data</a>
							<a class="btn m-1 <?php echo ($myRate == '??') ? ' btn-warning ' : ' btn-outline-warning '; ?>" role="button" data-toggle="modal" data-target="#rateModal"><?php echo $rateButtonLabel; ?></a>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="rateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="rateModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-sm">
	<form class="modal-content" action="?section=movie&id=<?php echo $movie->getID(); ?>" method="POST">
		<div class="modal-header">
			<h5 class="modal-title" id="rateModalLabel">Avaliar Filme</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<span>Sua Nota: </span><span id="rateDesc"><?php echo ($myRate == "??") ? "0.0" : $myRate; ?></span><span> / 5</span><br>
				<input type="range" class="form-control-range" name="rateValue" id="rateValue" max="5.0" min="0.0" step="0.5" value="<?php echo ($myRate == '??') ? 0.0 : floatval($myRate); ?>" oninput="rateChange();">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			<button type="submit" class="btn btn-primary" >Salvar Nota</button>
		</div>
	</form>
	</div>
	</div>

	<div class="modal fade" id="changeDateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="changeDateModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-sm">
	<form class="modal-content" action="?section=movie&id=<?php echo $movie->getID(); ?>" method="POST">
		<div class="modal-header">
			<h5 class="modal-title" id="changeDateModalLabel">Alterar Data</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<span>Escolha uma nova data:</span><br>
				<input class="m-1" type="date" name="newDate" id="newDate" required pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" min="2020-01-01" value="<?php echo $actualDate; ?>" oninput="dateChange();">
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			<button type="submit" class="btn btn-primary" id="changeDateBtn" data-toggle="tooltip" data-placement="top" title="Você deve definir uma nova data." disabled>Alterar</button>
		</div>
	</form>
	</div>
	</div>

	<script type="text/javascript">
		
		function rateChange() {
			var input = document.getElementById("rateValue");
			var span = document.getElementById("rateDesc");
			span.innerHTML = parseFloat(input.value).toFixed(1);
		}

		function dateChange() {
			var input = document.getElementById("newDate");
			var button = document.getElementById("changeDateBtn");
			
			if(input.value == "<?php echo $actualDate; ?>") {
				button.disabled = true;
				button.title = "Você deve definir uma nova data.";
			}
			else {
				button.disabled = false;
				button.title = ""
			}
		}

	</script>


</body>
</html>