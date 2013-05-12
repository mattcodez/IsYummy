<?php
function getItem(title){	
	$newName = strtolower(str_replace(' ', '', trim($_POST['title'])));
	$result = mysql_query('SELECT * FROM pages WHERE name = \'' . mysql_real_escape_string($newName) . '\'');
	return mysql_fetch_array($result);
}
?>