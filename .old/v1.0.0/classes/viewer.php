<?php

include_once $_SERVER['DOCUMENT_ROOT']."/db/database.php";
include_once $_SERVER['DOCUMENT_ROOT']."/classes/token.php";

class Viewer {

	private $nick;
	private $password;
	private $status = FALSE;

	# Getters
	public function getNick() { return $this->nick; }
	public function getPassword() { return $this->password; }
	public function isOnline() { return $this->status; }
	public function getCookie() { return "$this->nick:$this->password"; }

	public function login($nick, $pass) {
		$encodedPass = base64_encode($nick.":".$pass);
		$command = "SELECT * FROM Viewer
			WHERE nick = '$nick' AND password = '$encodedPass'";

		$db = new Database();
		$result = $db->runCommand($command);

		if($result == TRUE && $result->num_rows == 1) {
			$this->nick = $nick;
			$this->password = $encodedPass;
			$this->status = TRUE;
			return TRUE;
		}

		return FALSE;
	}

	public function loginEncoded($nick, $encodedPass) {
		$command = "SELECT * FROM Viewer
			WHERE nick = '$nick' AND password = '$encodedPass'";

		$db = new Database();
		$result = $db->runCommand($command);

		if($result == TRUE && $result->num_rows == 1) {
			$this->nick = $nick;
			$this->password = $encodedPass;
			$this->status = TRUE;
			return TRUE;
		}

		return FALSE;
	}

	public function signUp($nick, $pass, $token) {
		$tokenDOM = new Token();

		if($tokenDOM->validateToken($token)) {
			if($tokenDOM->deleteToken()) {
				$encodedPass = base64_encode($nick.":".$pass);
				$command = "INSERT INTO Viewer(nick,password) 
					VALUES ('$nick','$encodedPass')";
				$db = new Database();
			 	$result = $db->runCommand($command);

			 	if($result == TRUE) {
					$this->nick = $nick;
					$this->password = $pass;
				 	$this->status = TRUE;
				 	
				 	return TRUE;
			 	}
			}
		}

		return FALSE;
	}

	public function getAllNicks() {
		$command = "SELECT * FROM Viewer ORDER BY nick ASC";
		$db = new Database();
		$result = $db->runCommand($command);

		$allNicks = array();
		if($result == TRUE) {
			for($i = 0; $i < $result->num_rows; $i++){
				$viewerDB = $result->fetch_assoc();
				array_push($allNicks, $viewerDB['nick']);
			}
		}

		return $allNicks;
	}

	public function viewerExists($nick) {
		$command = "SELECT * FROM Viewer WHERE nick = '$nick'";
		
		$db = new Database();
		$result = $db->runCommand($command);
		return ($result == TRUE && $result->num_rows == 1);
	}

}

?>