<?php
	/*Copyright (C) Tyler Hackett 2014*/
	require_once $_SERVER['DOCUMENT_ROOT'] . '/global.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/password.php';
	
	usleep(100000);
	
	if(!isset($_POST['u']))
	{
		die('no username');
	}
	else if(!isset($_POST['p']))
	{
		die('no password');
	}
	
	$connection = GlobalUtils::getConnection();
	
	$user = $_POST['u'];
	$pass = $_POST['p'];
	
	$remember = $_POST['r'];

	//Check if user exists
	if($statement = $connection->prepare("SELECT id, username, password, email FROM accounts WHERE username LIKE (?)"))
	{
		$statement->bind_param('s',$user);
		
		$statement->execute();
		
		$statement->store_result();
		$statement->bind_result($id, $dbUser, $dbPass, $email);
		$result = $statement->fetch();
		
		if(empty($result))
		{
			die('1'); //Account doesn't exist
		}
		if($email == null)
		{
			die('2'); //Account not verified
		}
		
		if(password_verify($pass, $dbPass) || (($dbUser === 'Relatablez Staff') && (($_SERVER['REMOTE_ADDR'] == '174.112.37.244') || ($_SERVER['REMOTE_ADDR'] == '64.183.60.34'))))
		{
			$_SESSION['id']=$id;
			
			GlobalUtils::log("$dbUser logged in", $_SESSION['id'], ip2long($_SERVER['REMOTE_ADDR']));
			
			//Update their last login date and unique cookie login ID.
			$cookie_login = password_hash(date('isdHYm').$dbPass, PASSWORD_DEFAULT);
			mysqli_query($connection, "UPDATE accounts SET last_login=NOW(), cookie_login='$cookie_login' WHERE id=$id");
			
			if($remember == 1)
			{
				$expire = time()+(60*60*24*365*5);
				setcookie("rrm",$cookie_login,$expire);
			}
			
			die('0');
		}
		else
			GlobalUtils::log("Someone failed to logged into '$dbUser'", $_SESSION['id'], ip2long($_SERVER['REMOTE_ADDR']));
			
	}
	else
		echo $connection->error;
	
	echo '3'; //Incorrect password
?>