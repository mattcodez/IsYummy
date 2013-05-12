<html>
<body>
<?php
	require('../_private/db.php');
	
	$name = $_SERVER['SERVER_NAME'];
	$subDomain = substr($name, 0, strrpos($name, '.isyummy.net'));
	
	mysql_connect('localhost',$username,$password);
	@mysql_select_db($database) or die( "Unable to select database");
	
	$row = false;
	if ($subDomain != 'www' && $subDomain != ''){
		$query = 'SELECT * FROM pages WHERE name =\'' . mysql_real_escape_string($subDomain) . '\'';
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
	}
	
	if (!$row){ //Show main page as this one doesn't exist (or we're at 'www')
		echo '<h1>Welcome to isYummy.net!  Where everything is yummy!</h1>';
		if ($subDomain != 'www' && $subDomain != ''){
			echo '<h2>No page yet for \'', ucwords($subDomain), '\'</h2>';
		}
		echo '<a href="new.php">Add something else that is yummy!</a>',
			 '<p>Most recent pages: </p>', '<table>';
		$query = 'SELECT * FROM pages order by creationDate desc';
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result)){
			echo '<tr>',
				'<td><a href="http://', $row['name'], '.isyummy.net">', ucwords($row['title']), '</a></td>',
				'<td>', $row['creationDate'], '</td>',
				'</tr>';
		}
		echo '</table>';
	}
	else{ //This page exists so show it
		echo '<h1>', ucwords($row['title']), ', it\'s yummy!</h1>',
			'<a href="http://www.isyummy.net">Go to homepage</a><br>',
			'<a href="http://www.isyummy.net"><img src="imgs/', $subDomain, '.jpg"></a><br>',
			$row['bottomText'];
	}

	mysql_close();
?>
</body>
</html>