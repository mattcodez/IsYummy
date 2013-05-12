<?php
require('../_private/dbIns.php');
mysql_connect('localhost',$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

//Make sure someone else hasn't added this already
$newName = strtolower(str_replace(' ', '', trim($_POST['title'])));
$result = mysql_query('SELECT * FROM pages WHERE name = \'' . mysql_real_escape_string($newName) . '\'');
$row = mysql_fetch_array($result);
if ($row){
	exit('Sorry, someone else already thought ' . $_POST['title'] . ' is yummy.  Try a different title.<br>' .
		'<a href="javascript:history.back()">Go back</a>');
}

//Insert is new, proceed...
$target_path = 'imgs/' . $newName . '.jpg'; 

//Max dimensions for the new image
define('MAXWIDTH', 1024);
define('MAXHEIGHT', 768);

$imgFN = $_FILES['file']['tmp_name'];
$imgInfo = getimagesize($imgFN) or die('Init:File not a supported image type, please upload one of the following: JPEG/JPG, GIF or PNG');

switch ($imgInfo[2]) {
	case IMAGETYPE_JPEG :
		$img = imagecreatefromjpeg($imgFN);
		break;
	case IMAGETYPE_GIF :
		$img = imagecreatefromgif($imgFN); 
		break;
	case IMAGETYPE_PNG :
		$img = imagecreatefrompng($imgFN);
		break;
	default :
		exit($imgInfo[2] . ':File not a supported image type, please upload one of the following: JPEG/JPG, GIF or PNG');
}

$newDimens = getResizeHeights($imgInfo);
if ($newDimens) {
	$image_new = imagecreatetruecolor($newDimens[0], $newDimens[1]);
	imagecopyresampled($image_new, $img, 0, 0, 0, 0, $newDimens[0], $newDimens[1], $imgInfo[0], $imgInfo[1]);
	if (doInsert($newName)){
		imagejpeg($image_new, $target_path, 75) or die("There was an error converting the file.");
		header('Location: http://' . $newName . '.isyummy.net');
	}
	else {
		exit('1:Error adding.');
	}
}
else {
	if(doInsert($newName)){
		move_uploaded_file($_FILES['file']['tmp_name'], $target_path) or die("There was an error uploading the file.");
		header('Location: http://' . $newName . '.isyummy.net');
	} 
	else {
		exit('2:Error adding.');
	}
}


function doInsert($newName){
	return mysql_query('INSERT INTO pages(name, title, bottomText) values(\'' . 
		mysql_real_escape_string($newName) . '\', \'' . 
		mysql_real_escape_string($_POST['title']) . '\', \'' . 
		mysql_real_escape_string($_POST['text']) . '\')'
	);
}

function getResizeHeights($imgInfo){
	$resized = false;
	
	if ($imgInfo[0] > MAXWIDTH) {
		$x = MAXWIDTH;
		$y = floor((MAXWIDTH / $imgInfo[0]) * MAXHEIGHT); //Retain aspect ratio by reducing height by same percentage as width
		$resized = true;
	}
	
	if ($imgInfo[1] > MAXHEIGHT) {
		if ($resized) { //We already did some size modifications and height still isn't low enough
			$recursive = getResizeHeights(Array($x, $y));
			$x = $recursive[0];
			$y = $recursive[1];
		}
		else {
			$y = MAXHEIGHT;
			$x = floor((MAXHEIGHT / $imgInfo[1]) * MAXWIDTH);
			$resized = true;
		}
	}
	
	if ($resized){
		//In-case AR is really obscure, we don't want to allow a zero x or y to be returned
		if ($x == 0){
			if ($imgInfo[0] <= MAXWIDTH){
				$x = $imgInfo[0];
			}
			else {
				$x = MAXWIDTH;
			}
		}
		
		if ($y == 0){
			if ($imgInfo[1] <= MAXHEIGHT){
				$y = $imgInfo[1];
			}
			else {
				$y = MAXHEIGHT;
			}
		}
		return Array($x, $y);
	}
	else {
		return false;
	}
}
?>