<?php
	/*Copyright (C) Tyler Hackett 2014*/
	
	session_start();
	
	$_SESSION["username"] = null;
	$_SESSION['id'] = null;
	
	$deadcookie = time()-3600;
	
	setcookie("rrm","",$deadcookie,'/');
	
	header("Location: http://www.relatables.com$_GET[dir]"); //dir is the directory to send the user to.
?>