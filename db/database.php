<?php

class Database {

	private $servername;
	private $username;
	private $password;
	private $dbname;
	private $port;
	
	function Database(){
		// $this->servername = "localhost";
		// $this->username = "root";
		// $this->password = "";
		// $this->dbname = "MOVIEGROUP";
		$this->servername = "sql210.epizy.com";
		$this->username = "epiz_25795904";
		$this->password = "vgBxpfb3PfMR";
		$this->dbname = "epiz_25795904_MOVIEGROUP";
		// $this->port = 3306;
	}

	function runCommand($command){
		// Create connection
		@$conn = new mysqli($this->servername, $this->username, $this->password,
			$this->dbname/*, $this->port*/);

		// Check connection
		if ($conn->connect_error){
			// die("Connection failed: " . $conn->connect_error);
			header("Location: /index.php?error=500");
			return;
		}
		$conn->set_charset('utf8');

		$result = $conn->query($command);
		if ($result == FALSE){
		    // die("Error: " . $command . "<br>" . $conn->error);
		    header("Location: /index.php?error=500");
		}

		$conn->close();
		return $result;
	}

	function insert($command){
		// Create connection
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($conn->connect_error){
	    	// die("Connection failed: " . $conn->connect_error);
	    	header("Location: /index.php?error=500");
	    	return;
		}
		$conn->set_charset('utf8');

		$result = $conn->query($command);
		if ($result == FALSE){
		    // die("Error: " . $command . "<br>" . $conn->error);
		    header("Location: /index.php?error=500");
		}
		else{
			$result = $conn->insert_id;
		}

		$conn->close();
		return $result;
	}
}

?>