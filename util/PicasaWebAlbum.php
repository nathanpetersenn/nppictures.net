<?php

define('PICASA_URL_BASE', 'http://picasaweb.google.com/data/feed/api/user/');
require_once 'PicasaWebPhoto.php';

class PicasaWebAlbum {

  private $userId;
  private $albumId;
  private $feed;

  private $title = '';
  private $photos = array();
  private $prev;
  private $next;


  function __construct($userId, $albumId) {
    $this->userId = $userId;
    $this->albumId = $albumId;

    if ($albumId == 'recent') {
      $this->feed = simplexml_load_file('http://picasaweb.google.com/data/feed/base/user/' . $userId . '?kind=photo&access=public&max-results=15');
    } else {
      $this->feed = simplexml_load_file(PICASA_URL_BASE . $userId . '/albumid/' . $albumId);
    }

    $this->fetchTitle();
    $this->fetchPrevNext();
    $this->fetchPhotos();
  }

  /*
  =========================== PRIVATE METHODS ===========================
  */

  private function fetchTitle() {
    if ($this->albumId == 'recent') {
      $this->title = 'Recent';
    } else {
      $this->title = (string)$this->feed->title;
    }
  }

  private function fetchPhotos() {
    foreach ($this->feed->entry as $photo) {
      $timestamp = $photo->published;
      $timestamp = strtotime($timestamp);

      $title = (string)$photo->title;

      $caption = (string)$photo->summary;

      $src = (string)$photo->content->attributes()->src;
      

      $gphoto = $photo->children('http://schemas.google.com/photos/2007');
      $photoId = (int)$gphoto->id;

      $id = (string)$photo->id;

      $regex = array();
      preg_match("/\/user\/(\d+)\/albumid\/(\d+)/", $id, $regex);

      $userId = $regex[1];
      $albumId = $regex[2];

      if ($this->albumId == 'recent') {
        $md5 = md5(file_get_contents($src . '?imgmax=200'));
        $bad_md5 = 'd3c7a4cf13c71cc43aa6b65f390b658c';
        if ($md5 == $bad_md5) { continue; }

        $photo = new PicasaWebPhoto($this->userId, $albumId, $photoId);
        $dem = $photo->getDimensions();
        $width = $dem['width'];
        $height = $dem['height'];
      } else {
        $width = (int)$gphoto->width;
        $height = (int)$gphoto->height;
      }
      
      $pSet = array(
        "title" => $title,
        "caption" => $caption,
        "photoId" => $photoId,
        "albumId" => $albumId,
        "userId" => $userId,
        "src" => $src,
        "height" => $height,
        "width" => $width,
        "timestamp" => $timestamp
      );

      array_push($this->photos, $pSet);
    }
  }

  private function fetchPrevNext() {
    $user = new PicasaWebUser($this->userId);
    $albums = $user->getAlbums();

    if ($this->albumId == 'recent') {
      $this->prev = '';
    } else {
      $i = 0;
      foreach ($albums as $a) {
        if ($a['albumId'] == $this->albumId) {
          $this->prev = $albums[$i-1]['albumId'];
          break;
        }
        $i++;
      }
    }

    $i = 0;
    foreach ($albums as $a) {
      if ($a['albumId'] == $this->albumId) {
        if (($i+1) >= count($albums)) {
          $this->next = '';
          break;
        } else {
          $this->next = $albums[$i+1]['albumId'];
          break;
        }
      }
      $i++;
    }    
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

  public function getTitle() {
    return $this->title;
  }

  public function getPhotos() {
    return $this->photos;
  }

  public function getPrev() {
    return $this->prev;
  }

  public function getNext() {
    return $this->next;
  }

  public function getAlbumCover($size) {
    $url = (string)$this->feed->icon;
    return str_replace('s160-c', 's' . $size . '-c', $url);
  }

  /*
  =========================== STATIC PUBLIC METHODS ===========================
  */

  public static function isAlbum($userId, $albumId) {
    if ($albumId == 'recent') { return true; }

    $file = file_get_contents(PICASA_URL_BASE . $userId . '/albumid/' . $albumId);
    $file = explode(' ', $file);
    if ($file[0] == 'Invalid') {
      return false;
    } else {
      return true;
    }
  }

}

?>