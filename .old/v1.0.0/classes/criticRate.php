<?php

include_once $_SERVER['DOCUMENT_ROOT']."/db/database.php";

class CriticRate {

	private $id;
	private $source;
	private $rate;
	private $max;

	# Getters
	public function getID() { return $this->id; }
	public function getSource() { return $this->source; }
	public function getRate() { return $this->rate; }
	public function getMax() { return $this->max; }

	public function init($id, $source, $rate, $max) {
		$this->id = $id;
		$this->source = str_replace("'", "\'", $source);
		$this->rate = $rate;
		$this->max = $max;
	}

	public function create($id, $source, $rate, $max) {
		$command = "INSERT INTO CriticRate(id,source,rate,max) VALUES ('$id','$source',$rate,$max)";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE) {
			$this->id = $id;
			$this->source = $source;
			$this->rate = $rate;
			$this->max = $max;
		 	return TRUE;
	 	}

	 	return FALSE;
	}

	public function save() {
		$command = "INSERT INTO CriticRate(id,source,rate,max) VALUES ('$this->id','$this->source',$this->rate,$this->max)";
		$db = new Database();
		$result = $db->runCommand($command);
	 	return $result;
	}

	public function wasRatedBySource($id, $source) {
		$command = "SELECT * FROM CriticRate WHERE id = '$id' AND source = '$source'";
		$db = new Database();
		$result = $db->runCommand($command);
	 	return ($result == TRUE && $result->num_rows == 1);
	}

	public function getCriticRate($id, $source) {
		$command = "SELECT * FROM CriticRate WHERE id = '$id' AND source = '$source'";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE && $result->num_rows == 1) {
	 		$criticRateDB = $result->fetch_assoc();

	 		$id = $criticRateDB['id'];
			$source = $criticRateDB['source'];
			$rate = $criticRateDB['rate'];
			$max = $criticRateDB['max'];

			$criticRate = new CriticRate();
			$criticRate->init($id, $source, $rate, $max);
			return $criticRate;
	 	}

	 	return null;
	}

	public function getAllCriticRates($id) {
		$command = "SELECT * FROM CriticRate WHERE id = '$id'";
		$db = new Database();
		$result = $db->runCommand($command);

		$criticRateList = array();
		if($result == TRUE) {
			for($i = 0; $i < $result->num_rows; $i++) {
				$criticRateDB = $result->fetch_assoc();

		 		$id = $criticRateDB['id'];
				$source = $criticRateDB['source'];
				$rate = $criticRateDB['rate'];
				$max = $criticRateDB['max'];

				$criticRate = new CriticRate();
				$criticRate->init($id, $source, $rate, $max);
				array_push($criticRateList, $criticRate);
			}
		}

		return $criticRateList;
	}

	public function getAllSourceRates() {
		$command = "SELECT DISTINCT source, max FROM CriticRate";
		$db = new Database();
		$result = $db->runCommand($command);

		$sourceRateList = array();
		if($result == TRUE) {
			for($i = 0; $i < $result->num_rows; $i++) {
				$criticRateDB = $result->fetch_assoc();
				array_push($sourceRateList, array("source" => $criticRateDB['source'], "max" => $criticRateDB['max']));
			}
		}

		return $sourceRateList;
	}

}

?>