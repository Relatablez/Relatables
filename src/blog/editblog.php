<?php
	session_start();
	
	require($_SERVER['DOCUMENT_ROOT'] . '/userinfo.php');
	
	$connection = getConnection();
		
	if(!isAdmin($connection, $_SESSION['id']) || $_SESSION['username'] == null)
		header('Location: http://www.relatablez.com/404/');
	else
	{
		if($_POST['submit'] === 'Update' && $_POST['title'] && $_POST['contents'])
		{
			if($_POST['editimage'] && $_FILES['image']['error'] == 0)
			{
				$date = getdate();
				$image_path = "/images/{$date['year']}/{$date['mon']}/{$date['mday']}";
				$full_image_path = $_SERVER['DOCUMENT_ROOT'].$image_path;
				
				if(!is_dir($full_image_path))
					mkdir($full_image_path, 0777, true);
				
				$image_path .= '/'.$_FILES['image']['name'];
				move_uploaded_file($_FILES['image']['tmp_name'], $full_image_path);
				
				if($statement = $connection->prepare("UPDATE blog_articles SET title=(?), content=(?), image=(?) WHERE id=(?)"))
				{
					$statement->bind_param('sssi', $_POST['title'], $_POST['content'], $image_path, $_POST['id']);
					$statement->execute();
					
					header('Location: http://www.relatablez.com/blog/article/' . $_POST['id']);
				}
			}
			else
			{
				if($statement = $connection->prepare("UPDATE blog_articles SET title=(?), content=(?) WHERE id=(?)"))
				{
					$statement->bind_param('ssi', $_POST['title'], $_POST['contents'], $_POST['id']);
					$statement->execute();
					
					header('Location: http://www.relatablez.com/blog/article/' . $_POST['id']);
				}
			}
		}
	}
	
	$bid = $_GET['id'];
	
	if($statement = $connection->prepare("SELECT uid, title, content, image FROM blog_articles WHERE id = (?)"))
	{
		$statement->bind_param('i', $bid);
		$statement->execute();
		
		$statement->bind_result($uid, $title, $content, $image);
		$statement->fetch();
		
	}
?>
<!-- Copyright (C) Tyler Hackett 2014 -->
<!DOCTYPE html>
<html>
	<head>
		<title>Relatablez - New Article</title>
		
		<meta charset="UTF-8">
		<meta name="keywords" content="Am I The Only One, Relatablez, Am I The Only One That">
		<meta name="description" content="Relatablez – Is it Just You? Relatablez is website that connects people using the things we do in our life to see if others feel or do the same.">
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="http://www.relatablez.com/toolbartheme.css">
		<link rel="stylesheet" type="text/css" href="http://www.relatablez.com/blog/newblogtheme.css">
		<link rel="canonical" href="http://www.relatablez.com/">
	</head>
	<body>
		<?php require($_SERVER["DOCUMENT_ROOT"] . "/toolbar.php"); ?>
	
		<div id='new-blog'>
			<h1 class='header' >Create a Blog Article</h1>
			
			<div id='cheatsheet'>
				<button id='cheatsheet-view-button'> &gt </button>
				<h3>HTML Cheatsheet</h3>
				
				<table>
					<tr>
						<td><b>Bold Text</b></td>
						<td> &#60b&#62Text&#60/b&#62 </td>
					<tr>
						<td><i>Italic Text</i></td>
						<td> &#60i&#62Text&#60/i&#62 </td>
					<tr>
						<td>Image</td>
						<td> &#60img src='example.com/img.jpg' /&#62 </td>
					<tr>
						<td>Paragraphs</td>
						<td> &#60p&#62Paragraph&#60/p&#62 </td>
					<tr>
						<td>Line<br>Break</td>
						<td>&#60br&#62</td>
					<tr>
						<td>Line<hr></td>
						<td>&#60hr&#62</td>
					<tr>
						<td><a style='cursor:pointer;'>Link</a></td>
						<td>&#60a href='example.com'&#62Link&#60/a&#62
				</table>
				
				<span class='footnote'>HTML elements can be combined. For example, you can make an image link with <br> <b>&#60a href='http://www.relatablez.com/'&#62 &#60img src='http://www.relatablez.com/logotextwhite.png'/&#62&#60/a&#62 </b> for this result:</span><br>
				<a href='http://www.relatablez.com/'><img style='background:#BFBFBF;' src='http://www.relatablez.com/logotextwhite.png' /></a>
			</div>
			
			<div id='article-creation' class='content'>
				<form id='article-form' action='http://www.relatablez.com/blog/editblog.php' method='POST' enctype='multipart/form-data'>
					<input type='hidden' name='id' value='<?php echo $bid; ?>' />
				
					<h3>Title:</h3>
					<input id='article-title' type='text' name='title' value='<?php echo $title; ?>'/>
					
					<hr>
					
					<h3>Front Page Image:</h3>
					<span>Edit Image:</span><input type='checkbox' name='editimage' value='1' /><br>
					<input id='article-img' type='file' name='image' /><br>
					<span  class='footnote'>Image should be atleast 850x300 to avoid pixelation. If these fields are left unedited, the image will remain the same. </span>
					
					<hr>
					
					<h3>Contents:</h3>
					<textarea id='article-contents' name='contents'><?php echo $content; ?></textarea>
					
					<input id='submit' type='submit' name='submit' value='Update' />
				</form>
			</div>
			
			<h1 class='header' id='preview-title'><?php echo $title; ?></h1>
			<div id='article-preview' class='content'>
				<img id='preview-img' <?php if($image != null) echo "src='$image'"; ?> width='100%' height='300px' />
				<div style='padding:20px;font-size:17px;' id='preview-contents'><?php echo $content; ?></div>
			</div>
		</div>
	
	<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src='http://www.relatablez.com/toolbar.js'></script>
	<script type='text/javascript'>
		var reader = new FileReader();
		var preview = document.getElementById("preview-img");
		var cheatsheetVisible = false;
		
		$('#submit').click(function(event)
		{
			if(!$('#article-title').val())
			{
				$('#article-title').css('box-shadow', '0px 0px 10px red');
				return false;
			}
			if(!$('#article-contents').val())
			{
				$('#article-contents').css('box-shadow', '0px 0px 10px red');
				return false;
			}
		});
		$('#article-img').change(function(event)
		{
			var file = document.getElementById("article-img").files[0];

			if (!file.type.match(/image.*/))
				$('#article-img').css('box-shadow', '0px 0px 10px red');

			preview.file = file;	
			reader.onload = function (e) {
				$('#preview-img').attr('src', e.target.result);
			}

			reader.readAsDataURL(file);
				
		});
		
		$('#cheatsheet-view-button').click(function(event)
		{
			if(cheatsheetVisible)
			{
				$('#cheatsheet').animate({left:"-310px"}, 1000);
				cheatsheetVisible = !cheatsheetVisible;
			}
			else
			{
				$('#cheatsheet').animate({left:"0px"}, 1000);
				cheatsheetVisible = !cheatsheetVisible;
			}
		});
		
		$("#article-title").on('keyup change paste', function(event){ $('#preview-title').html($(this).val()); });
		$("#article-contents").on('keyup change paste', function(event){ $('#preview-contents').html($(this).val()); });
		$('#cheatsheet-view-button').mouseout(function(event){if(!cheatsheetVisible) $('#cheatsheet').animate({left:"-310px"}, 500); });
		$('#cheatsheet-view-button').mouseover(function(event){if(!cheatsheetVisible) $('#cheatsheet').animate({left:"-290px"}, 500); });
		$("#article-contents").keypress(function(event){ if(event.keyCode == 13) $('#article-contents').val($('#article-contents').val() + "<br>"); });
	</script>
	</body>
</html>