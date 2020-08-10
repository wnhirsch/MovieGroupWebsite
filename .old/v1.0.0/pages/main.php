<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Avaliação de Filmes - Grupo de Cine</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

	<!-- Font Awesome Icon Library -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<style type="text/css">
		.ratting {
			position: absolute;
			top: 0.2em;
			left: 0.4em;
			z-index: 3;
		}
		.fa-star {
			text-shadow: 1px 1px rgba(0, 0, 0, 0.5);
		}
		.star-checked {
			color: orange;
		}
		.row {
			font-size: 80%;
		}
		.card-movie {
			text-decoration: none;
			color: #343a40;
		}
		.card-movie:hover {
			text-decoration: none;
			color: #343a40;
		}
		.img-fake {
			max-height: 30vh;
			object-fit: contain;
		}
		.img-front {
			max-height: 30vh;
			object-fit: contain;

			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			margin-left: auto;
			margin-right: auto;
			z-index: 2;
		}
		.img-back {
			max-height: 30vh;
			width: 100%;
			filter: blur(5px);

			position: absolute;
			top: 0;
			left: 0;
			z-index: 1;
		}
		.img-back-back {
			max-height: 30vh;
			width: 100%;

			position: absolute;
			top: 0;
			left: 0;
			z-index: 0;
		}

	</style>

</head>
<body class="bg-light">

<?php
	include $_SERVER['DOCUMENT_ROOT']."/pages/_navbar.php";
	if(!isset($_SESSION)) { session_start(); }
?>

	<div class="container mt-5">
		<form class="col-md-8 offset-md-2 col-lg-6 offset-lg-3" action="" method="GET">
			<div class="input-group mb-3">
				<input type="hidden" name="section" value="search">
				<input type="text" class="form-control" name="t" placeholder="Filme">
				<div class="input-group-append">
					<button class="btn btn-outline-secondary" type="submit">Buscar</button>
				</div>
			</div>
		</form>
	</div>

	<div class="container mt-4">
		<ul class="nav nav-pills nav-fill">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" id="pills-all-tab" data-toggle="pill" href="#pills-all" role="tab" aria-controls="pills-all" aria-selected="true">Todos os Filmes</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="pills-scheduled-tab" data-toggle="pill" href="#pills-scheduled" role="tab" aria-controls="pills-scheduled" aria-selected="true">Agendados</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="pills-notRated-tab" data-toggle="pill" href="#pills-notRated" role="tab" aria-controls="pills-notRated" aria-selected="true">Não avaliados</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="pills-myMovies-tab" data-toggle="pill" href="#pills-myMovies" role="tab" aria-controls="pills-myMovies" aria-selected="true">Meus Filmes</a>
			</li>
		</ul>
		<div class="tab-content mt-3" id="pills-tabContent">
			<div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
				<div class="container mt-3">
				<div class="row">
<?php
	include $_SERVER['DOCUMENT_ROOT']."/classes/movie.php";

	$movieDOM = new Movie();
	$movieRateDOM = new MovieRate();

	$allMovies = $movieDOM->getAllMovies();
	if(count($allMovies) == 0) {
		echo "Nenhum filme registrado.";
	}

	foreach ($allMovies as $movie) {
		$about = $movie->getPlot();
		$about = (strlen($about) > $ABOUT_LENGTH) ? substr($about, 0, $ABOUT_LENGTH) . "..." : $about;

		$dateMsg = "";
		if($movie->getWatchAt() != null) {
			$dateMsg = ($movie->getWatchAt() + $ONE_DAY > time()) ? "Marcado para o dia " : "Assistido no dia ";
			$dateMsg = $dateMsg.date("d/m/Y", $movie->getWatchAt()).".";
		}

		$hiddenRate = "";
		$rate = 0;
		$aux = $movieRateDOM->getMovieRate($movie->getID())["rate"];
		if($aux === null) {
			$hiddenRate = " hidden ";
		}
		else {
			$rate = round($aux);
		}
?>
					<a class="col-sm-6 col-md-4 col-lg-3 card-movie" href="?section=movie&id=<?php echo $movie->getID(); ?>">
						<div class="card m-2">
							<div <?php echo $hiddenRate; ?> class="ratting">
<?php
	for($i = 0 ; $i < 5; $i++) { 
		if($i < $rate) { ?>
								<span class="fa fa-star star-checked"></span>
<?php	}
		else { ?>
								<span class="fa fa-star"></span>
<?php	}
	}
?>
							</div>
							<div class="card-img-top">
								<img class="img-fake" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-front" src="<?php echo $movie->getPoster(); ?>">
							</div>
							<div class="card-body">
								<h6 class="card-text"><?php echo $movie->getTitle(); ?></h6>
								<p class="card-text"><?php echo $about; ?></p>
								<span class="card-text font-italic"><?php echo $dateMsg; ?></span>
							</div>
						</div>
					</a>
<?php } ?>
				</div>
				</div>
			</div>
			<div class="tab-pane fade" id="pills-scheduled" role="tabpanel" aria-labelledby="pills-scheduled-tab">
				<div class="container mt-3">
				<div class="row">
<?php
	$scheduledMovies = $movieDOM->getScheduledMovies($allMovies);
	if(count($scheduledMovies) == 0) {
		echo "Nenhum filme agendado.";
	}

	foreach ($scheduledMovies as $movie) {
		$about = $movie->getPlot();
		$about = (strlen($about) > $ABOUT_LENGTH) ? substr($about, 0, $ABOUT_LENGTH) . "..." : $about;

		$dateMsg = "";
		if($movie->getWatchAt() != null) {
			$dateMsg = ($movie->getWatchAt() + $ONE_DAY > time()) ? "Marcado para o dia " : "Assistido no dia ";
			$dateMsg = $dateMsg.date("d/m/Y", $movie->getWatchAt()).".";
		}

		$hiddenRate = "";
		$rate = 0;
		$aux = $movieRateDOM->getMovieRate($movie->getID())["rate"];
		if($aux === null) {
			$hiddenRate = " hidden ";
		}
		else {
			$rate = round($aux);
		}
?>
					<a class="col-sm-6 col-md-4 col-lg-3 card-movie" href="?section=movie&id=<?php echo $movie->getID(); ?>">
						<div class="card m-2">
							<div <?php echo $hiddenRate; ?> class="ratting">
<?php
	for($i = 0 ; $i < 5; $i++) { 
		if($i < $rate) { ?>
								<span class="fa fa-star star-checked"></span>
<?php	}
		else { ?>
								<span class="fa fa-star"></span>
<?php	}
	}
?>
							</div>
							<div class="card-img-top">
								<img class="img-fake" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-front" src="<?php echo $movie->getPoster(); ?>">
							</div>
							<div class="card-body">
								<h6 class="card-text"><?php echo $movie->getTitle(); ?></h6>
								<p class="card-text"><?php echo $about; ?></p>
								<span class="card-text font-italic"><?php echo $dateMsg; ?></span>
							</div>
						</div>
					</a>
<?php } ?>
				</div>
				</div>
			</div>
			<div class="tab-pane fade" id="pills-notRated" role="tabpanel" aria-labelledby="pills-notRated-tab">
				<div class="container mt-3">
				<div class="row">
<?php
	$nonRatedMovies = array();
	if(isset($_SESSION['viewer'])){
		$nonRatedMovies = $movieDOM->getNonRatedMovies($allMovies, $_SESSION['viewer']->getNick());
		if(count($nonRatedMovies) == 0) {
			echo "Nenhum filme para avaliar.";
		}
	}

	foreach ($nonRatedMovies as $movie) {
		$about = $movie->getPlot();
		$about = (strlen($about) > $ABOUT_LENGTH) ? substr($about, 0, $ABOUT_LENGTH) . "..." : $about;

		$dateMsg = "";
		if($movie->getWatchAt() != null) {
			$dateMsg = "Assistido no dia ".date("d/m/Y", $movie->getWatchAt()).".";
		}

		$hiddenRate = "";
		$rate = 0;
		$aux = $movieRateDOM->getMovieRate($movie->getID())["rate"];
		if($aux === null) {
			$hiddenRate = " hidden ";
		}
		else {
			$rate = round($aux);
		}
?>
					<a class="col-sm-6 col-md-4 col-lg-3 card-movie" href="?section=movie&id=<?php echo $movie->getID(); ?>">
						<div class="card m-2">
							<div <?php echo $hiddenRate; ?> class="ratting">
<?php
	for($i = 0 ; $i < 5; $i++) { 
		if($i < $rate) { ?>
								<span class="fa fa-star star-checked"></span>
<?php	}
		else { ?>
								<span class="fa fa-star"></span>
<?php	}
	}
?>
							</div>
							<div class="card-img-top">
								<img class="img-fake" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-front" src="<?php echo $movie->getPoster(); ?>">
							</div>
							<div class="card-body">
								<h6 class="card-text"><?php echo $movie->getTitle(); ?></h6>
								<p class="card-text"><?php echo $about; ?></p>
								<span class="card-text font-italic"><?php echo $dateMsg; ?></span>
							</div>
						</div>
					</a>
<?php } ?>
				</div>
				</div>
			</div>
			<div class="tab-pane fade" id="pills-myMovies" role="tabpanel" aria-labelledby="pills-myMovies-tab">
				<div class="container mt-3">
				<div class="row">
<?php
	$myMovies = array();
	if(isset($_SESSION['viewer'])){
		$myMovies = $movieDOM->getMyMovies($allMovies, $_SESSION['viewer']->getNick());
		if(count($myMovies) == 0) {
			echo "Nenhum filme registrado.";
		}
	}

	foreach ($myMovies as $movie) {
		$about = $movie->getPlot();
		$about = (strlen($about) > $ABOUT_LENGTH) ? substr($about, 0, $ABOUT_LENGTH) . "..." : $about;

		$dateMsg = "";
		if($movie->getWatchAt() != null) {
			$dateMsg = ($movie->getWatchAt() + $ONE_DAY > time()) ? "Marcado para o dia " : "Assistido no dia ";
			$dateMsg = $dateMsg.date("d/m/Y", $movie->getWatchAt()).".";
		}

		$hiddenRate = "";
		$rate = 0;
		$aux = $movieRateDOM->getMovieRate($movie->getID())["rate"];
		if($aux === null) {
			$hiddenRate = " hidden ";
		}
		else {
			$rate = round($aux);
		}
?>
					<a class="col-sm-6 col-md-4 col-lg-3 card-movie" href="?section=movie&id=<?php echo $movie->getID(); ?>">
						<div class="card m-2">
							<div <?php echo $hiddenRate; ?> class="ratting">
<?php
	for($i = 0 ; $i < 5; $i++) { 
		if($i < $rate) { ?>
								<span class="fa fa-star star-checked"></span>
<?php	}
		else { ?>
								<span class="fa fa-star"></span>
<?php	}
	}
?>
							</div>
							<div class="card-img-top">
								<img class="img-fake" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-back" src="<?php echo $movie->getPoster(); ?>">
								<img class="img-front" src="<?php echo $movie->getPoster(); ?>">
							</div>
							<div class="card-body">
								<h6 class="card-text"><?php echo $movie->getTitle(); ?></h6>
								<p class="card-text"><?php echo $about; ?></p>
								<span class="card-text font-italic"><?php echo $dateMsg; ?></span>
							</div>
						</div>
					</a>
<?php } ?>
				</div>
				</div>
			</div>
		</div>
	</div>
	<br><br><br><br>

</body>
</html>