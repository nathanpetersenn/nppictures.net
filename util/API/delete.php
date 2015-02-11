<?php

$file = $_GET['file'];

$success = unlink('../../cache/' . $file);
if ($success) {
	echo 'true';
} else {
	echo 'false';
}

?>