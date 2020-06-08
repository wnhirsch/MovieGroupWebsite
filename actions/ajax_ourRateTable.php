<style type="text/css">
	.custom-select {
		background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='white' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E");
	}
</style>

<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/movieRate.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/movie.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/viewer.php";
	$movieRateDOM = new MovieRate();
	$movieDOM = new Movie();
	$viewerDOM = new Viewer();

	$viewersList = $viewerDOM->getAllNicks();
	$viewerIndex = -1;
	if(isset($_POST["viewerIndex"])) {
		$viewerIndex = $_POST["viewerIndex"];
		unset($_POST);
	}

	$ratedBy = "";
	if($viewerIndex != -1) {
		$ratedBy = (count($viewersList) <= $viewerIndex) ? "" : $viewersList[$viewerIndex];
	}

	$mostRatedByUsMovies = $movieDOM->getAllMovies($movieDOM::ORDER_BY_OUR_RATE, $ratedBy);
	$hasZeroMostRatedByUs = (count($mostRatedByUsMovies) == 0);
?>
					<select id="ourRate" class="custom-select custom-select-sm bg-dark text-light border-secondary rounded-0" onchange="reloadOurTable();">
						<option <?php echo ($viewerIndex == -1) ? " selected " : ""; ?> value="-1">Todos os votantes</option>
<?php
	foreach ($viewersList as $i => $viewerNick) {
		$isSelected = "";
		if($i == $viewerIndex) {
			$isSelected = " selected ";
		}
?>
						<option <?php echo $isSelected; ?> class="font-weight-light" value="<?php echo $i; ?>">@<?php echo $viewerNick; ?></option>
<?php } ?>
					</select>
					<table class="table table-sm table-dark <?php echo $hasZeroMostRatedByUs ? '' : 'table-hover'; ?>">
						<thead>
							<tr>
							<th scope="col">#</th>
							<th scope="col">Filme</th>
							<th class="text-center" scope="col">Nota (max. 5,0)</th>
							<th scope="col">Recomendação</th>
							</tr>
						</thead>
						<tbody>
<?php
	if($hasZeroMostRatedByUs) {
?>
							<tr>
								<td class="text-center font-weight-light" colspan="4">Nenhum filme agendado / assistido.</td>
							</tr>
<?php
	}

	$hasTotalVotes = true;
	for ($i = 0; $i < min(10, count($mostRatedByUsMovies)); $i++) { 
		$movie = $mostRatedByUsMovies[$i];
		$nick = $movie->getChoosedBy();
		$nick = ($nick == null) ? "n.a." : $nick;

		$rate = "";
		if($viewerIndex == -1) {
			$aux = $movieRateDOM->getMovieRate($movie->getID());
			$rate = ($aux["rate"] === null) ? "n.a." : number_format($aux["rate"], 2, ",", "");
			$numVotes = $aux["votes"];

			if($numVotes != null && $numVotes != count($viewersList)){
				$hasTotalVotes = false;
				$rate = $rate."*";
			}
		}
		else {
			$aux = $movieRateDOM->getMyRate($movie->getID(), $ratedBy);
			$rate = ($aux === null) ? "n.a." : number_format($aux, 2, ",", "");
		}


		$color = "#FFFFFF";
		if($i == 0) {
			$color = "#FFD700";
		}
		else if($i == 1) {
			$color = "#C0C0C0";
		}
		else if($i == 2) {
			$color = "#CD7F32";
		}
?>
							<tr onclick="window.location.href = '?section=movie&id=<?php echo $movie->getID(); ?>';"
								style="color: <?php echo $color; ?> !important;">
								<th scope="row"><?php echo $i+1; ?></th>
								<td><?php echo $movie->getTitle(); ?></td>
								<td class="text-center"><?php echo $rate; ?></td>
								<td class="font-weight-light">@<?php echo $nick; ?></td>
							</tr>
<?php } ?>
						</tbody>
						<caption <?php echo ($hasTotalVotes) ? " hidden " : ""; ?> class="text-light font-weight-light">* Nota e colocação podem ser alteradas pois nem todos os usuários votaram.</caption>
					</table>
