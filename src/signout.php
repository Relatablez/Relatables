<?php
	session_start();
	
	$_SESSION["username"] = null;
	
	$deadcookie = time()-3600;
	
	setcookie("rrmi","",$deadcookie);
	setcookie("rrmp","",$deadcookie);
	
	header('Location: http://www.relatablez.com/');
?>