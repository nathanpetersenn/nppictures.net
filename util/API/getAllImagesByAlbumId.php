<?php

define(PICASA_USER, 'nppictures.storage');

require_once '../PicasaWebUser.php';
require_once '../PicasaWebAlbum.php';
require_once '../PicasaWebPhoto.php';

$album = new PicasaWebAlbum(PICASA_USER, $_REQUEST['id']);

echo json_encode($album->getPhotos());

?>