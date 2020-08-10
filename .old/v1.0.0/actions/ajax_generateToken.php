<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/classes/token.php";

	$token = new Token();
	if(!$token->createToken()){
		header("Location: /index.php?error=500");
	}
	
	echo $_SERVER['HTTP_HOST']."?token=".$token->getToken();
?>