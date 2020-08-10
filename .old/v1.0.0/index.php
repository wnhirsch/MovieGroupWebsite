<?php
	// Constants
	$COOKIE_LIMIT = 7*24*60*60;
	$ONE_DAY = 24*60*60;
	$ABOUT_LENGTH = 80;
	$NICK_REGEX = "/(^[a-z A-Z])([a-z A-Z 0-9 ^@_-]){4,19}$/";
	$PASS_REGEX = "/([a-z A-Z 0-9 ^_]){8,16}$/";
	$IMDB_API_URL = "http://www.omdbapi.com/?apikey=432624aa";

	// if(isset($_GET["update"]) && isset($_GET["pass"])) {
	// 	header("Location: /actions/".$_GET["update"].".php?pass=".$_GET["pass"]);
	// }

	include_once $_SERVER['DOCUMENT_ROOT']."/classes/viewer.php";
	if(!isset($_SESSION)) { session_start(); }

	if(isset($_GET["error"])) {
		include $_SERVER['DOCUMENT_ROOT']."/pages/error.php";
		return;
	}
	if(isset($_GET["exit"])) {
		unset($_SESSION["viewer"]);
		unset($_COOKIE["user"]);
		setcookie("user", null, time() - 3600, "/", $_SERVER["HTTP_HOST"]);
	}

	if(!isset($_SESSION["viewer"]) && isset($_COOKIE["user"])) {
		$_SESSION["viewer"] = new Viewer();
		$login = explode(":", $_COOKIE["user"]);
		$_SESSION["viewer"]->loginEncoded($login[0], $login[1]);
		setcookie("user", $_COOKIE["user"], time() + $COOKIE_LIMIT, "/", $_SERVER["HTTP_HOST"]); // Restart
	}

	if(isset($_SESSION["viewer"])) {
		if(isset($_GET["section"])) {
			switch ($_GET["section"]) {
				case "main":
					include $_SERVER['DOCUMENT_ROOT']."/pages/main.php";
					break;
				case "movie":
					include $_SERVER['DOCUMENT_ROOT']."/pages/movie.php";
					break;
				case "search":
					include $_SERVER['DOCUMENT_ROOT']."/pages/search.php";
					break;
				case "statistics":
					include $_SERVER['DOCUMENT_ROOT']."/pages/statistics.php";
					break;
				case "invite":
					include $_SERVER['DOCUMENT_ROOT']."/pages/invite.php";
					break;
				default:
					include $_SERVER['DOCUMENT_ROOT']."/pages/main.php";
					break;
			}
		}
		else {
			include $_SERVER['DOCUMENT_ROOT']."/pages/main.php";
		}
	}
	else {
		if(isset($_GET["token"])) {
			include $_SERVER['DOCUMENT_ROOT']."/pages/signUp.php";
		}
		else {
			include $_SERVER['DOCUMENT_ROOT']."/pages/login.php";
		}
	}
?>