<?php

include_once $_SERVER['DOCUMENT_ROOT']."/db/database.php";

class MovieRate {

	private $id;
	private $nick;
	private $rate;

	# Getters
	public function getID() { return $this->id; }
	public function getNick() { return $this->nick; }
	public function getRate() { return $this->rate; }

	public function init($id, $nick, $rate) {
		$this->id = $id;
		$this->nick = $nick;
		$this->rate = $rate;
	}

	public function save($id, $nick, $rate) {
		$command = "INSERT INTO MovieRate(id,nick,rate) VALUES ('$id','$nick',$rate)";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE) {
			$this->id = $id;
			$this->nick = $nick;
			$this->rate = $rate;
		 	return TRUE;
	 	}

	 	return FALSE;
	}

	public function update($id, $nick, $rate) {
		$command = "UPDATE MovieRate SET rate = $rate WHERE id = '$id' AND nick = '$nick'";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE) {
			$this->id = $id;
			$this->nick = $nick;
			$this->rate = $rate;
		 	return TRUE;
	 	}

	 	return FALSE;
	}

	public function wasRated($id, $nick) {
		$command = "SELECT * FROM MovieRate WHERE id = '$id' AND nick = '$nick'";
		$db = new Database();
		$result = $db->runCommand($command);
	 	return ($result == TRUE && $result->num_rows == 1);
	}

	public function getMyRate($id, $nick) {
		$command = "SELECT * FROM MovieRate WHERE id = '$id' AND nick = '$nick'";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE && $result->num_rows == 1) {
	 		$movieRateDB = $result->fetch_assoc();
			return floatval($movieRateDB['rate']);
	 	}

	 	return null;
	}

	public function getMyRatedMovies($nick) {
		$command = "SELECT * FROM MovieRate WHERE nick = '$nick'";
		$db = new Database();
		$result = $db->runCommand($command);

		$movieList = array();
		if($result == TRUE) {
			for($i = 0; $i < $result->num_rows; $i++) {
				$movieRateDB = $result->fetch_assoc();

				$id = $movieRateDB['id'];
				$nick = $movieRateDB['nick'];
				$rate = $movieRateDB['rate'];

				$movieRate = new MovieRate();
				$movieRate->init($id, $nick, $rate);
				array_push($movieList, $movieRate);
			}
		}

		return $movieList;
	}

	public function getMovieRate($id) {
		$command = "SELECT * FROM MovieRate WHERE id = '$id'";
		$db = new Database();
		$result = $db->runCommand($command);

		if($result == TRUE && $result->num_rows >= 1) {
			$totalRate = 0.0;
			for($i = 0; $i < $result->num_rows; $i++) {
				$movieRateDB = $result->fetch_assoc();
				$totalRate = $totalRate + $movieRateDB['rate'];
			}

			$totalRate = ($totalRate == 0.0) ? 0.0 : $totalRate / $result->num_rows;
			return array("rate" => $totalRate, "votes" => $result->num_rows);
		}

		return array("rate" => null, "votes" => null);
	}

}

?>