<!DOCTYPE html>
<!-- Copyright (C) Tyler Hackett 2014 -->
<html>
	<?php 
		session_start();
		
		$user = $_GET['username'];
		
		//If username ends in a /, remove it.
		$slashpos = strpos($user,'/');
		
		if($slashpos != false)
			$user = substr($user,0,$slashpos);
		
		require($_SERVER['DOCUMENT_ROOT']."/userinfo.php");
		
		$connection = mysqli_connect("mysql.a78.org","u683362690_insom","10102S33K3R17","u683362690_rtblz");
		$data = getProfileData($connection, $user);
	?>
	<head>
		<title id='title'><?php echo $data['username']; ?></title>
		
		<meta charset="UTF-8">
		<meta name="keywords" content="Am I The Only One, Relatablez, Am I The Only One That">
		<meta name="description" content="Relatablez – Is it Just You? Relatablez is website that connects people using the things we do in our life to see if others feel or do the same.">
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="http://www.relatablez.com/toolbartheme.css">
		<link rel="stylesheet" type="text/css" href="http://www.relatablez.com/usertheme.css">
		<link rel="canonical" href="http://www.relatablez.com/">
			
	</head>
	<body style='margin:0px;'>
		<?php require($_SERVER['DOCUMENT_ROOT']."/toolbar.php"); ?>
	
		<div id='infobanner'>
			<div id='infolayout'>
				<div id='info'>
					<span id='username'><?php echo $data['username']; ?></span><br>
					<?php if($data['hidelocation'] == 0){ echo "<span id='location' class='right-spacer'>".$data['country']."</span>"; } ?>
					<span id='date' class='right-spacer'>
						<?php echo $data['joined']; ?>
					</span>
					<?php if($data['admin'] === 1) echo '<span style=\'color:red;font:bold 15px arial;\'>[Admin]</span>'; ?>
					<br>
					<p id='user-description'><?php echo htmlspecialchars($data['description']); ?></p>
				</div>
			</div>
		</div>
		
		<div id='moduleslayout'>
			<div id='relatedmodule'>
				<h3>Related With</h3>
				<?php
					if(!$data['hiderelated'])
					{
						$related_data = getRelated($connection, $data['id']);
						
						$related = $related_data['related'];
						$statement = $related_data['statement'];
						
						while($statement->fetch())
						{	
							if($related['anonymous'])
								$user='Anonymous';
							else
								$user = getUsername($connection, $related['uid']);
							
							echo "\r\n<div class='dialogue uppadding' id='{$related['id']}' data-category='{$related['category']}' data-nsfw='{$related['nsfw']}' data-date='{$related['fdate']}'>";
							echo "\r\n<p class='dialogue'>{$related['submission']}</p>";
							echo "\r\n<table class='vote-table'>";
							echo "\r\n<tr>";
							if($_SESSION["username"] != null)
							{
								echo "\r\n<td><button class='dialoguebutton' id='bna{$related['id']}' data-vid='{$related['id']}' data-v='{$related['verification']}'>No, me too!</button></td>";
								echo "\r\n<td><button class='dialoguebutton' id='ba{$related['id']}'  data-vid='{$related['id']}' data-v='{$related['verification']}'>You're alone.</button></td>";
							}
							else
							{
								echo "\r\n<td><button class='dialoguebutton showreg' data-header='Please sign up to vote'>No, me too!</button></td>";
								echo "\r\n<td><button class='dialoguebutton showreg' data-header='Please sign up to vote'>You're alone.</button></td>";				
							}
							echo "\r\n<tr>";
							echo "\r\n<td><span class='vote-counter' id='na{$related['id']}'>(" . number_format($related["notalone"]) . ")</span></td>";
							echo "\r\n<td><span class='vote-counter' id='a{$related['id']}'>(" . number_format($related["alone"]) . ")</span></td>";
							echo "\r\n</table>";
							echo "\r\n<div style='text-align:right;'><span class='submissioninfo'><a ";
							
							if($related['anonymous'])
								echo ' >' . $user . "</a> - {$related['fdate']}</span></div>";
							else
							{
								if(isAdmin($connection, $related['uid']))
									echo 'class=\'admin\'';
								echo " href='http://www.relatablez.com/user/" . $user . "'>" . $user . "</a> - " . $related["fdate"] . "</span></div>";
							}
							echo "\r\n</div>";
						}
					}
				?>	
			</div>		
			<div id='statsmodule'>
				<h3>Stats</h3>
				<div id='stats'>
					<span>Posts: <?php echo $data['posts']; ?></span><br>
					<span>Comments: <?php echo $data['comments']; ?></span><br>
					<span>Moderated: <?php echo $data['moderated']; ?></span>
				</div>
			</div>
		</div>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src='http://www.relatablez.com/toolbar.js'></script>
		<script type="text/javascript" src="https://apis.google.com/js/platform.js"></script>		
	</body>
</html>