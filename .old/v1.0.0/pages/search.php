<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Buscar Filme - Grupo de Cine</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	
	<style type="text/css">
		.img-movieResult {
			height: 3em;
			width: auto;
		}
	</style>

</head>
<body class="bg-light">
	
<?php
	include $_SERVER['DOCUMENT_ROOT']."/pages/_navbar.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/searchResult.php";
	if(!isset($_SESSION)) { session_start(); }

	$searchTitle = "";
	$resultList = array();
	if (isset($_GET['t']) && $_GET['t'] != "") {
		$searchTitle = $_GET['t'];
		$searchResultDOM = new SearchResult();
		$resultList = $searchResultDOM->searchMovie($searchTitle);
	}
?>

	<div class="container mt-5">
		<form class="col-md-8 offset-md-2 col-lg-6 offset-lg-3" action="" method="GET">
			<div class="input-group mb-3">
				<input type="hidden" name="section" value="search">
				<input type="text" class="form-control" name="t" placeholder="Filme" value="<?php echo $searchTitle; ?>">
				<div class="input-group-append">
					<button class="btn btn-outline-secondary" type="submit">Buscar</button>
				</div>
			</div>
		</form>
		<div id="responseMovies"><br>
<?php
	if ($searchTitle != "") {
?>
		<h3>Existem <?php echo count($resultList); ?> resultados para "<?php echo $searchTitle; ?>".</h3><br>
<?php
		foreach ($resultList as $movie) {
?>
			<a class="card" href="?section=movie&id=<?php echo $movie->getID(); ?>">
				<div class="row no-gutters">
					<div class="col-auto">
						<img src="<?php echo $movie->getImageURL(); ?>" class="img-fluid img-movieResult">
					</div>
					<div class="col">
						<div class="card-block px-2">
							<span class="card-text"><?php echo $movie->getName(); ?></span><br>
							<span class="card-text text-secondary"><?php echo $movie->getInfo(); ?></span>
						</div>
					</div>
				</div>
			</a>
<?php
		}
	}
	else {
		echo "Informe o nome do filme acima.";
	}
?>
		</div>
	</div>
	<br><br><br>
</body>
</html>