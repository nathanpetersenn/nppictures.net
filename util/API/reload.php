<?php

$file = $_GET['file'];
$href = $_GET['href'];

$real_file = '../../cache/' . $file;

$success = unlink($real_file);
if ($success) {
	$foo = file_get_contents('http://nppictures.net' . $href);
	echo 'true';
} else {
	echo 'false';
}

?>