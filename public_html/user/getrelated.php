<?php
	/*Copyright (C) Tyler Hackett 2015*/
	
	require $_SERVER['DOCUMENT_ROOT'] . '/global.php';
	
	$index  = $_POST['i']; 
	$uid	= $_POST['u'];
	
	$connection = GlobalUtils::getConnection();
	
	if($statement = $connection->prepare('SELECT flags FROM accounts WHERE id=(?)'))
	{
		$statement->bind_param('i',$uid);
		$statement->execute();
		
		$statement->bind_result($flags);
		$statement->fetch();
		
		if(!(($flags >> 0x02) & 0x01)) // If not hiding related, get 'em
		{
			$related = Post::getRelated($uid, $index, 3);
			
			if(sizeof($related) == 0)
				echo '<h1>There are no posts to display</h1>';
			
			for($i = 0; $i < 3; $i++)
			{	
				if($related[$i])
					echo $related[$i]->format();
			}
			
			if($related[3]) //If a 4th post exists, that means there's atleast one more post to load.
				echo '<div class="col-md-12"><span class="button" data-getrel="' . ($index+1) . '">Load More</span></div>';
		}
		else
		{
			echo '<h1>Related posts are privated</h1>';
		}
	}
	