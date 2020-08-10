<?php

include_once $_SERVER['DOCUMENT_ROOT']."/classes/simple_html_dom.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/movieRate.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/viewer.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/criticRate.php";
include_once $_SERVER['DOCUMENT_ROOT']."/db/database.php";

class Movie {

	private $id;
	private $title;
	private $year;
	private $poster;
	private $runtime;
	private $director;
	private $genre;
	private $plot;
	private $actors;
	private $awards;
	private $choosedBy = null;
	private $watchAt = null;

	// Lazy
	public $criticRateList = null;

	// Constants
	public const ONE_DAY = 24*60*60;
	public const IMDB_API_URL = "http://www.omdbapi.com/?apikey=432624aa";
	public const IMG_NULL = "media/image_not_found.png";
	public const ORDER_BY_WATCH_AT = 0;
	public const ORDER_BY_RATE = 1;
	public const ORDER_BY_OUR_RATE = 2;
	public const RATE_TYPE_IMDb = 0;
	public const RATE_TYPE_META = 1;
	public const RATE_TYPE_FOREIGN = 2;

	# Getters
	public function getID() { return $this->id; }
	public function getTitle() { return str_replace("\'", "'", $this->title); }
	public function getYear() { return $this->year; }
	public function getPoster() { return $this->poster; }
	public function getRuntime() { return $this->runtime; }
	public function getDirector() { return str_replace("\'", "'", $this->director); }
	public function getGenre() { return str_replace("\'", "'", $this->genre); }
	public function getPlot() { return str_replace("\'", "'", $this->plot); }
	public function getActors() { return str_replace("\'", "'", $this->actors); }
	public function getAwards() { return str_replace("\'", "'", $this->awards); }
	public function getChoosedBy() { return $this->choosedBy; }
	public function getWatchAt() { return $this->watchAt; }

	public function init($id, $title, $year, $poster, $runtime, $director, $genre, $plot, $actors, $awards, $choosedBy = null, $watchAt = null) {
		$this->id = $id;
		$this->title = str_replace("'", "\'", $title);
		$this->year = $year;
		$this->poster = $poster;
		$this->runtime = $runtime;
		$this->director = str_replace("'", "\'", $director);
		$this->genre = str_replace("'", "\'", $genre);
		$this->plot = str_replace("'", "\'", $plot);
		$this->actors = str_replace("'", "\'", $actors);
		$this->awards = str_replace("'", "\'", $awards);
		$this->choosedBy = $choosedBy;
		$this->watchAt = $watchAt;
	}

	public function save($choosedBy, $watchAt) {
		$viewerDOM = new Viewer();

		if($viewerDOM->viewerExists($choosedBy)) {
			$command = "INSERT INTO Movie(id,title,year,poster,runtime,director,genre,plot,actors,
				awards,choosedBy,watchAt) 
				VALUES ('$this->id','$this->title','$this->year','$this->poster','$this->runtime',
				'$this->director','$this->genre','$this->plot','$this->actors','$this->awards','$choosedBy',$watchAt)";
			$db = new Database();
		 	$result = $db->runCommand($command);

		 	if($result == TRUE) {
				$this->choosedBy = $choosedBy;
				$this->watchAt = $watchAt;

				foreach ($this->criticRateList as $criticRate) {
					if(!$criticRate->save()){
						return FALSE;
					}
				}

			 	return TRUE;
		 	}
		}

		return FALSE;
	}

	public function remove() {
		$command = "DELETE FROM Movie WHERE id = '$this->id'";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE) {
			$this->choosedBy = null;
			$this->watchAt = null;
		 	return TRUE;
	 	}

	 	return FALSE;
	}

	public function changeDate($newDate) {
		$command = "UPDATE Movie SET watchAt = $newDate WHERE id = '$this->id' AND choosedBy = '$this->choosedBy'";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE) {
			$this->watchAt = $newDate;
		 	return TRUE;
	 	}

	 	return FALSE;
	}

	public function getMovie($id) {
		$command = "SELECT * FROM Movie
			WHERE id = '$id'";

		$db = new Database();
		$result = $db->runCommand($command);

		if($result == TRUE && $result->num_rows == 1) {
			$movieDB = $result->fetch_assoc();
			$this->id = $movieDB['id'];
			$this->title = $movieDB['title'];
			$this->year = $movieDB['year'];
			$this->poster = $movieDB['poster'];
			$this->runtime = $movieDB['runtime'];
			$this->director = $movieDB['director'];
			$this->genre = $movieDB['genre'];
			$this->plot = $movieDB['plot'];
			$this->actors = $movieDB['actors'];
			$this->awards = $movieDB['awards'];
			$this->choosedBy = $movieDB['choosedBy'];
			$this->watchAt = $movieDB['watchAt'];

			return $this;
		}
		else {
			$jsonurl = self::IMDB_API_URL."&i=".$id."&type=movie";
			$json = file_get_contents($jsonurl);
			$obj = json_decode($json);

			if(isset($obj->{"Response"}) && $obj->{"Response"} == "True") {
				$imdbID = $obj->{"imdbID"};
				$title = $obj->{"Title"};
				$year = intval($obj->{"Year"});
				$poster = ($obj->{"Poster"} == "N/A") ? self::IMG_NULL : $obj->{"Poster"};
				$runtime = ($obj->{"Runtime"} == "N/A") ? null : $obj->{"Runtime"};
				$director = $obj->{"Director"};
				$genre = ($obj->{"Genre"} == "N/A") ? null : $obj->{"Genre"};
				$plot = ($obj->{"Plot"} == "N/A") ? "Sinopse não encontrada." : $obj->{"Plot"};
				$actors = $obj->{"Actors"};
				$awards = $obj->{"Awards"};

				$movie = new Movie();
				$movie->init($imdbID, $title, $year, $poster, $runtime, $director, $genre, $plot, $actors, $awards);

				$ratings = $obj->{"Ratings"};
				$movie->criticRateList = array();
				foreach ($ratings as $rateObj) {
					$id = $imdbID;
					$source = $rateObj->{"Source"};
					$rate = 0.0;
					$max = 0.0;

					$value = $rateObj->{"Value"};
					if(strpos($value, "/") != False) {
						$pos = strpos($value, "/");
						$rate = floatval(substr($value, 0, $pos));
						$max = floatval(substr($value, $pos+1));
					}
					else if(strpos($value, "%") != False) {
						$rate = floatval(str_replace("%", "", $value));
						$max = 100.0;
					}
					else {
						continue;
					}

					$criticRate = new CriticRate();
					$criticRate->init($id, $source, $rate, $max);
					array_push($movie->criticRateList, $criticRate);
				}

				return $movie;
			}
		}

		return null;
	}

	public function getAllMovies($flag = self::ORDER_BY_WATCH_AT, $rateSource = "") {
		$command = "SELECT * FROM Movie";
		if($flag == self::ORDER_BY_WATCH_AT) {
			$command = "SELECT * FROM Movie ORDER BY watchAt DESC";
		}
		else if($flag == self::ORDER_BY_RATE) {
			$command = "SELECT * FROM Movie INNER JOIN CriticRate ON Movie.id = CriticRate.id WHERE source LIKE '$rateSource' ORDER BY rate DESC";
		}
		else if($flag == self::ORDER_BY_OUR_RATE) {
			if($rateSource == "") {
				$command = "SELECT * FROM Movie ORDER BY (SELECT SUM(rate) FROM MovieRate WHERE Movie.id = MovieRate.id) DESC, (SELECT SUM(rate / max) / COUNT(*) FROM CriticRate WHERE Movie.id = CriticRate.id) DESC, watchAt DESC";
			}
			else {
				$command = "SELECT * FROM Movie ORDER BY (SELECT rate FROM MovieRate WHERE Movie.id = MovieRate.id AND '$rateSource' = MovieRate.nick) DESC, (SELECT SUM(rate) FROM MovieRate WHERE Movie.id = MovieRate.id) DESC, (SELECT SUM(rate / max) / COUNT(*) FROM CriticRate WHERE Movie.id = CriticRate.id) DESC, watchAt DESC";
			}
		}

		$db = new Database();
		$result = $db->runCommand($command);

		$allMovies = array();
		if($result == TRUE) {
			for($i = 0; $i < $result->num_rows; $i++){
				$movieDB = $result->fetch_assoc();

				$id = $movieDB['id'];
				$title = $movieDB['title'];
				$year = $movieDB['year'];
				$poster = $movieDB['poster'];
				$runtime = $movieDB['runtime'];
				$director = $movieDB['director'];
				$genre = $movieDB['genre'];
				$plot = $movieDB['plot'];
				$actors = $movieDB['actors'];
				$awards = $movieDB['awards'];
				$choosedBy = $movieDB['choosedBy'];
				$watchAt = $movieDB['watchAt'];

				$movie = new Movie();
				$movie->init($id, $title, $year, $poster, $runtime, $director, $genre, $plot, $actors, $awards, $choosedBy, $watchAt);

				if($flag == self::ORDER_BY_RATE) {
					$rate = $movieDB['rate'];
					$max = $movieDB['max'];

					$criticRate = new CriticRate();
					$criticRate->init($id, $rateSource, $rate, $max);
					array_push($allMovies, array("movie" => $movie, "criticRate" => $criticRate));
				}
				else {
					array_push($allMovies, $movie);
				}
			}
		}

		return $allMovies;
	}

	public function getScheduledMovies($movieList) {
		$scheduledMovies = array();

		foreach ($movieList as $movie) {
			if($movie->getWatchAt() != null && $movie->getWatchAt() + self::ONE_DAY > time()){
				array_push($scheduledMovies, $movie);
			}
		}

		return $scheduledMovies;
	}

	public function getNonRatedMovies($movieList, $myNick) {
		$nonRatedMovies = array();

		$movieRateDOM = new MovieRate();
		$ratedMovies = $movieRateDOM->getMyRatedMovies($myNick);

		foreach ($movieList as $movie) {
			if($movie->getWatchAt() != null && $movie->getWatchAt() + self::ONE_DAY <= time()) {
				$index = $this->searchMovieByID($ratedMovies, $movie->getID());
				if($index == -1) {
					array_push($nonRatedMovies, $movie);
				}
				else {
					array_splice($ratedMovies, $index, 1);
				}
			}
		}

		return $nonRatedMovies;
	}

	public function getMyMovies($movieList, $myNick) {
		$myMovies = array();

		foreach ($movieList as $movie) {
			if($movie->getChoosedBy() != null && $movie->getChoosedBy() == $myNick){
				array_push($myMovies, $movie);
			}
		}

		return $myMovies;
	}

	private function searchMovieByID($movieList, $id) {
		for ($i = 0; $i < count($movieList); $i++) { 
			if($movieList[$i]->getID() == $id) {
				return $i;
			}
		}

		return -1;
	}

}

?>