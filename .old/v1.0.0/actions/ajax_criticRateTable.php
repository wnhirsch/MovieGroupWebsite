<style type="text/css">
	.custom-select {
		background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='white' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E");
	}
</style>

<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/criticRate.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/movie.php";
	$movieDOM = new Movie();
	$criticRateDOM = new CriticRate();

	$sourceList = $criticRateDOM->getAllSourceRates();
	$sourceMax = 0;
	$sourceIndex = 0;
	if(isset($_POST["sourceIndex"])){
		$sourceIndex = $_POST["sourceIndex"];
		unset($_POST);
	}

	$auxSource = (count($sourceList) <= $sourceIndex) ? "" : $sourceList[$sourceIndex]["source"];
	$mostRatedMovies = $movieDOM->getAllMovies($movieDOM::ORDER_BY_RATE, $auxSource);
	$hasZeroMostRated = (count($mostRatedMovies) == 0 || count($sourceList) == 0);
?>
					<select id="sourceRate" class="custom-select custom-select-sm bg-dark text-light border-secondary rounded-0" onchange="reloadCriticTable();">
<?php
	foreach ($sourceList as $i => $sourceData) {
		$isSelected = "";
		if($i == $sourceIndex) {
			$isSelected = " selected ";
			$sourceMax = $sourceData["max"];
		}
?>
						<option <?php echo $isSelected; ?> value="<?php echo $i; ?>"><?php echo $sourceData["source"]; ?></option>
<?php } ?>
					</select>
					<table class="table table-sm table-dark <?php echo $hasZeroMostRated ? '' : 'table-hover'; ?>">
						<thead>
							<tr>
							<th scope="col">#</th>
							<th scope="col">Filme</th>
							<th class="text-center" scope="col">Nota (max. <?php echo $sourceMax; ?>)</th>
							<th scope="col">Recomendação</th>
							</tr>
						</thead>
						<tbody>
<?php
	if($hasZeroMostRated) {
?>
							<tr>
								<td class="text-center font-weight-light" colspan="4">Nenhum filme agendado / assistido.</td>
							</tr>
<?php
	}

	for ($i = 0; $i < min(10, count($mostRatedMovies)); $i++) {
		$movie = $mostRatedMovies[$i]["movie"];
		$criticRate = $mostRatedMovies[$i]["criticRate"];

		$nick = $movie->getChoosedBy();
		$nick = ($nick == null) ? "n.a." : $nick;
		$rate = $criticRate->getRate();
		$rate = ($rate === null) ? "n.a." : $rate;
		
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
								<td class="column-limit text-center"><?php echo $rate; ?></td>
								<td class="font-weight-light">@<?php echo $nick; ?></td>
							</tr>
<?php } ?>
						</tbody>
					</table>
