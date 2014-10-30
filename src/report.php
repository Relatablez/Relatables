<?php
	/*Copyright (C) Tyler Hackett 2014*/
	session_start();
	require($_SERVER['DOCUMENT_ROOT'] . '/userinfo.php');
	
	if(isset($_POST['c']))
	{
		$id = $_POST['c'];
		
		$type = 'comment';
		$id_name = 'cid';
	}
	else if(isset($_POST['s']))
	{
		$id = $_POST['s'];
		
		$type = 'submission';
		$id_name = 'id';
	}
	else
		die();
	
	$connection = getConnection();
	
	if(/*isAdmin($connection, $_SESSION['id'])*/false)
	{
		if($statement = $connection->prepare("UPDATE {$type}s SET reported=1 WHERE $id_name = (?)"))
		{
			$statement->bind_param('i', $id);
			$statement->execute();
		}
	}
	else
	{
		if($statement = $connection->prepare("INSERT INTO {$type}_reports (uid, id) VALUES ({$_SESSION['id']}, ?)"))
		{
			$statement->bind_param('i', $id);
			$statement->execute();
		}
	}
	
	echo $connection->error;
	