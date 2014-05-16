<?php	
	/*Copyright (C) Tyler Hackett 2014*/
	
	//Returns a connection to the MySQL database.
	function getConnection()
	{
		return mysqli_connect("mysql.a78.org","u683362690_insom","10102S33K3R17","u683362690_rtblz");
	}
	
	//Returns an array of relevant information pertaining to the specified user's profile.
	function getProfileData($username)
	{
		$connection = getConnection();
		
		if($statement = $connection->prepare("SELECT username, id, DATE_FORMAT(joined,'%M %d, %Y'), DATE_FORMAT(last_login,'%M %d, %Y'), description, posts, comments, moderated, hiderelated, hidelocation, hidedescription, country_id  FROM accounts WHERE username like (?)"))
		{	
			$statement->bind_param("s",$username);
			
			$statement->execute();
			
			$data = array('username'=>false);
			
			$statement->store_result();
			$statement->bind_result($data['username'],$data['id'],$data['joined'],$data['last_login'],$data['description'],$data['posts'],$data['comments'],$data['moderated'],$data['hiderelated'],$data['hidelocation'],$data['hidedescription'],$cid);
			$result = $statement->fetch();
			
			$data['country'] = getCountry($cid);
			
			if(!empty($result))
				return $data;
			else
				return false;
		}	
	}
	
	function getProfileSettings($id)
	{
		$connection = getConnection();
		
		if($statement = $connection->prepare("SELECT hidedescription, hidelocation, description, email, country_id  FROM accounts WHERE id = (?)"))
		{	
			$statement->bind_param("i",$id);
			
			$statement->execute();
			
			$data = array();
			
			$statement->store_result();
			$statement->bind_result($data['hidedescription'],$data['hidelocation'],$data['description'],$data['email'],$cid);
			$result = $statement->fetch();
			
			$data['country_id'] = $cid;
			$data['country'] = getCountry($cid);
			
			if(!empty($result))
				return $data;
			else
				return false;
		}	
	}
		
	function getCountry($id)
	{
		if($id == -1)
			return "No country specified";
			
		$connection = getConnection();
		
		if($statement = $connection->prepare("SELECT short_name FROM countries WHERE country_id = (?)"))
		{	
			$statement->bind_param("i",$id);
			
			$statement->execute();
			
			$statement->store_result();
			$statement->bind_result($country);
			$result = $statement->fetch();
			
			if(!empty($result))
				return $country;
			else
				return false;
		}	
	}	
	
	function getPasswordAndSalt($id)
	{
			
		$connection = getConnection();
		
		if($statement = $connection->prepare("SELECT password, salt FROM accounts WHERE id = (?)"))
		{	
			$statement->bind_param("i",$id);
			
			$statement->execute();
			
			$data = array('hash'=>'n/a','salt'=>'n/a');
			
			$statement->store_result();
			$statement->bind_result($data['hash'],$data['salt']);
			$result = $statement->fetch();
			
			if(!empty($result))
				return $data;
			else
				return false;
		}	
	}
	
	function getPendingEmail($id)
	{		
		$connection = getConnection();
		
		if($statement = $connection->prepare("SELECT pending_email FROM accounts WHERE id = (?)"))
		{	
			$statement->bind_param("i",$id);
			
			$statement->execute();
			
			$statement->store_result();
			$statement->bind_result($pendingEmail);
			$result = $statement->fetch();
			
			if(!empty($result))
				return $pendingEmail;
			else
				return false;
		}	
	}
	
	function setPendingEmail($pendingEmail, $id)
	{		
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET pending_email=(?) WHERE id = (?)"))
		{	
			$statement->bind_param("si",$pendingEmail,$id);		
			$statement->execute();
		}	
	}
	
	function setPassword($password, $id)
	{
		$new_salt = mcrypt_create_iv(16);
		$new_hash = md5($password.$new_salt);
				
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET password=(?), salt=(?) WHERE id=(?)"))
		{	
			$statement->bind_param('ssi',$new_hash,$new_salt,$id);	
			$statement->execute();	
		}	
	}
	
	function setCountry($country_id,$id)
	{	
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET country_id=(?) WHERE id=(?)"))
		{	
			$statement->bind_param('ii',$country_id,$id);	
			$statement->execute();
		}	
	}
	
	function setUsername($username,$id)
	{	
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET username=(?) WHERE id=(?)"))
		{	
			$statement->bind_param('si',$username,$id);		
			$statement->execute();
		}	
	}
	
	function setDescription($description,$id)
	{	
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET description=(?) WHERE id=(?)"))
		{	
			$statement->bind_param('si',$description,$id);		
			$statement->execute();
		}	
	}
	
	function showLocation($id)
	{	
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET hidelocation=0 WHERE id=(?)"))
		{	
			$statement->bind_param('i',$id);		
			$statement->execute();
		}	
	}
	
	function hideLocation($id)
	{	
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET hidelocation=1 WHERE id=(?)"))
		{	
			$statement->bind_param('i',$id);		
			$statement->execute();
		}	
	}
	
	function showRelated($id)
	{	
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET hiderelated=0 WHERE id=(?)"))
		{	
			$statement->bind_param('i',$id);		
			$statement->execute();
		}
	}
	
	function hideRelated($id)
	{	
		$connection = getConnection();
		
		if($statement = $connection->prepare("UPDATE accounts SET hiderelated=1 WHERE id=(?)"))
		{	
			$statement->bind_param('i',$id);		
			$statement->execute();
		}	
	}