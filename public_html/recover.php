<?php
	/*Copyright (C) Tyler Hackett 2015*/
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/global.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/password.php';
	
	$email 	= $_POST['e'];
	$javascript = $_POST['s'];
	
	/**UID and Verification for email linky clicky thingy*/
	$uid			= intval($_GET['i']);
	$verification 	= $_GET['v'];
	
	$connection = GlobalUtils::getConnection();
	
	if($email)   //If an email is provided, send a verification link to it.
	{	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			if($javascript)
			{
				die('Invalid email.');
			}
			else
			{
				$_SESSION['error_msg'] = "Invalid email.";
				header('Location: /');
				return;
			}
		}
		
		if($statement = $connection->prepare('SELECT id FROM accounts WHERE email LIKE (?)'))
		{
			$statement->bind_param('s', $email);
			$statement->execute();
			
			$statement->bind_result($uid);
			$statement->fetch();
			
			if($uid) //Ensure the user exists
			{
				$user = new User($uid);
				
				$verification = $user->generateVerification();
				$user->update();
				
				$body = "Hey " . $user->getUsername() . ",\n\nYou are receiving this email because you have requested to reset your password.\n\nPlease click the link below to reset your password.\nhttp://www.relatables.com/recover.php?v=$verification&i=$uid \n\nIf you didn’t request this change, please ignore this message.\n\nThanks,\nThe Relatables Team";
				
				
				$user->email('Password Recovery', $body);
				
				if($javascript)
					die('We\'ve sent an email to ' . $user->getEmail() . ' with a verification link.');
			}
		}
	}
	else if($verification && $uid) //If verification and uid is provided, attempt to verify the password recovery and send user to the password reset page.
	{
		if($uid == $_SESSION['recover_uid'])
		{ 
			header('Location: /recover/');
		}
		else
		{
			if($statement = $connection->prepare('SELECT verification FROM accounts WHERE id=(?)'))
			{
				$statement->bind_param('i', $uid);
				$statement->execute();
				
				$statement->bind_result($verification_hash);
				$statement->fetch();
				
				if(!password_verify($verification, $verification_hash))
				{
					$_SESSION['error_msg'] = 'Invalid verification.';
					header('Location: /');
				}
				else
				{ 
					//Recover UID temporarily stores the verified user ID until a password new password is chosen on /recover/
					$_SESSION['recover_uid'] = $uid;
					header('Location: /recover/');
				}
			}
		}
	}
	else
	{
		if($javascript)
		{
			die('No email provided.');
		}
		else
		{
			$_SESSION['error_msg'] = "No email provided.";
			header('Location: /');
		}
	}
	