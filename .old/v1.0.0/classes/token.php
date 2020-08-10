<?php

include_once $_SERVER['DOCUMENT_ROOT']."/db/database.php";

class Token {

	private $token;
	private $createdAt;

	// Constants
	public const TOKEN_LIMIT = 24*60*60;
	public const TOKEN_LENGTH = 10;

	# Getters
	public function getToken() { return $this->token; }
	public function getCreatedAt() { return $this->createdAt; }

	public function createToken() {
		$randomString = '';
		do {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			for ($i = 0; $i < self::TOKEN_LENGTH; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
		} while($this->tokenExists($randomString));

		$timeNow = time();

		$command = "INSERT INTO Token(token,createdAt) VALUES ('$randomString',$timeNow)";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE) {
			$this->token = $randomString;
			$this->createdAt = $timeNow;
		 	return TRUE;
	 	}

	 	return FALSE;
	}

	public function tokenExists($token) {
		$command = "SELECT * FROM Token WHERE token = '$token'";

		$db = new Database();
		$result = $db->runCommand($command);
		return ($result == TRUE && $result->num_rows == 1);
	}

	public function validateToken($token) {
		$command = "SELECT * FROM Token WHERE token = '$token'";

		$db = new Database();
		$result = $db->runCommand($command);

		if($result == TRUE && $result->num_rows == 1) {
			$tokenDB = $result->fetch_assoc();
			if($tokenDB['createdAt'] + self::TOKEN_LIMIT > time()) {
				$this->token = $tokenDB['token'];
				$this->createdAt = $tokenDB['createdAt'];
				return TRUE;
			}
		}

		return FALSE;
	}

	public function deleteToken() {
		$command = "DELETE FROM Token WHERE token = '$this->token'";
		$db = new Database();
		$result = $db->runCommand($command);

	 	if($result == TRUE) {
			$this->token = "";
			$this->createdAt = -1;
		 	return TRUE;
	 	}

	 	return FALSE;
	}

}

?>