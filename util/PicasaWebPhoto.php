<?php

class PicasaWebPhoto {

  private $userId;
  private $albumId;
  private $photoId;

  private $feed;
  private $exif = array();

  function __construct($userId, $albumId, $photoId) {
    $this->userId = $userId;
    $this->albumId = $albumId;
    $this->photoId = $photoId;

    $this->feed = simplexml_load_file('http://picasaweb.google.com/data/entry/api/user/' . $userId . '/albumid/' . $albumId . '/photoid/' . $photoId);
    $this->fetchExif();
  }

  /*
  =========================== PRIVATE METHODS ===========================
  */

  private function fetchExif() {
    $e = $this->feed->children('http://schemas.google.com/photos/exif/2007')->tags;

    $exp = (float)$e->exposure;
    if ($exp && $exp < 1) { $this->exif['exposure'] = '1/' . round(1 / $exp); }
    if ($exp && $exp >= 1) { $this->exif['exposure'] = round($exp) . ' sec'; }
    
    $this->exif['fstop'] = (float)$e->fstop;
    $this->exif['iso'] = (float)$e->iso;
    $this->exif['make']= (string)$e->make;
    $this->exif['model'] = (string)$e->model;
    $this->exif['focallength'] = (float)$e->focallength;
    $this->exif['time'] = ((int)$e->time) / 1000;
  }

  /*
  =========================== PUBLIC METHODS ===========================
  */

  public function getUserId() {
    return $this->userId;
  }

  public function getAlbumId() {
    return $this->albumId;
  }

  public function getPhotoId() {
    return $this->photoId;
  }

  public function getUrl() {
    return (string)$this->feed->content->attributes()->src;
  }

  public function getExif() {
    return $this->exif;
  }

  public function getDimensions() {
    $gphoto = $this->feed->children('http://schemas.google.com/photos/2007');
    $width = (int)$gphoto->width;
    $height = (int)$gphoto->height;
    return array('width' => $width, 'height' => $height);
  }

  public function getFileSize() {
    $gphoto = $this->feed->children('http://schemas.google.com/photos/2007');
    return (int)$gphoto->size;
  }

}

?>