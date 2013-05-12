<?php
require('../_private/db.php');
header('Content-Type: text/javascript; charset=utf8');
mysql_connect('localhost',$username,$password);
@mysql_select_db($database) or die(json_encode(Array('Error' => 'Unable to select database')));

function checkTitle($title){
	$newName = strtolower(str_replace(' ', '', trim($title)));
	$result = mysql_query('SELECT * FROM pages WHERE name = \'' . mysql_real_escape_string($newName) . '\'');
	$row = mysql_fetch_array($result);
	
	if ($row) {
		return Array('titleExists' => 1);
	}
	else {
		return Array('titleExists' => 0);
	}
}

function runAction($action){
	switch ($action){
		case 'checkTitle':
			return checkTitle($_GET['title']);
		default:
			return Array('Error' => 'Unknown action');
	}
}

echo json_encode(runAction($_GET['action']));
?>