<?php
	/*Copyright (C) Tyler Hackett 2015*/
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/global.php';
	
	if($_SESSION['username'] == null) die();
	
	$vote = $_POST['v'];
	
	$connection = GlobalUtils::getConnection();
	
	$query = $connection->query("SELECT id FROM qotw ORDER BY created LIMIT 1"); //Grab latest QOTW
	$qotw = $query->fetch_row();
	
	if($statement = $connection->prepare("INSERT INTO qotw_votes SET uid={$_SESSION['id']}, qid={$qotw[0]}, v=(?) ON DUPLICATE KEY UPDATE v=(?)"))
	{
		$statement->bind_param('ii',$vote,$vote);
		$statement->execute();
	}
	
	$query = $connection->query("SELECT answer, (SELECT COUNT(v) FROM qotw_votes WHERE qotw_votes.qid={$qotw[0]} AND v=id) AS total_votes FROM qotw_options WHERE qid={$qotw[0]}");
	
	$tallies = array();
	$total_votes = 0;
	
	while($votes = $query->fetch_assoc())
	{
		$tallies[$votes['answer']] = $votes['total_votes'];
		$total_votes += $votes['total_votes'];
	}
	
	arsort($tallies);
	$first_tally = true;
	
	foreach($tallies as $answer => $tally)
	{
		$vote_percentage = (int)($tally/$total_votes*100);
		if($first_tally === true)
		{
			echo "<b>$answer ($tally) $vote_percentage%</b><br>";
			$first_tally = false;
		}
		else
			echo "$answer ($tally) $vote_percentage%<br>";
	}
	echo '</table>';
	