<?php

define('PICASA_URL_BASE', 'http://picasaweb.google.com/data/feed/api/user/');

class PicasaWebUser {

  private $EXCLUDED_ALBUMS = array('Profile Photos', 'Profile Data', 'Scrapbook Photos');

  private $userId = '';
  private $feed;

  private $name = array();
  private $aboutBlurb = '';
  private $profilePictureUrl = '';
  private $albums = array();

  function __construct($userId) {
    $this->userId = $userId;
    $this->feed = simplexml_load_file(PICASA_URL_BASE . $userId);

    $this->fetchName();
    $this->fetchAboutBlurb();
    $this->fetchProfilePicture();
    $this->fetchAlbums();
  }

  /*
  =========================== PRIVATE METHODS ===========================
  */

  private function fetchName() {
    $this->name = explode(' ', (string)$this->feed->author->name);
  }

  private function fetchAboutBlurb() {
    foreach ($this->feed->entry as $album) {
      $title = $album->title;

      if ($title == 'Profile Data') {
        $this->aboutBlurb = (string)$album->summary;
        break;
      }
    }
  }

  private function fetchProfilePicture() {
    $this->profilePictureUrl = $this->_getAlbumCover('Profile Data');

    if ($this->profilePictureUrl == '') {
      $this->profilePictureUrl = $this->_getAlbumCover('Profile Photos');
    }

    $this->profilePictureUrl = $this->profilePictureUrl . '?imgmax=500';
  }

  private function fetchAlbums() {
    array_push($this->albums, $this->_getRecentAlbum());

    foreach ($this->feed->entry as $album) {
      $album_name = $album->title;
      if (in_array($album_name, $this->EXCLUDED_ALBUMS)) { continue; }

      $googleSchemas = $album->children('http://schemas.google.com/photos/2007');
      $album_id = $googleSchemas->id;
      $album_numphotos = $googleSchemas->numphotos;
      $album_timestamp = (float)$googleSchemas->timestamp; 
      $album_timestamp /= 1000;   

      $yahooMrss = $album->children('http://search.yahoo.com/mrss/');
      $album_thumb = $yahooMrss->group->content->attributes()->url;

      $tmp = array (
          "name" => (string)$album_name,
          "numphotos" => (int)$album_numphotos,
          "timestamp" => (int)$album_timestamp,
          "thumb" => (string)$album_thumb,
          "albumId" => (int)$album_id,
          "userId" => $this->userId
        );

      array_push($this->albums, $tmp);
    }
  }

  private function _getAlbumCover($searchTitle) {
    foreach ($this->feed->entry as $album) {
      $title = $album->title;

      if ($title == $searchTitle) {
        $mrss = $album->children('http://search.yahoo.com/mrss/');
        return $mrss->group->content->attributes()->url;
      }
    }
  }

  private function _getRecentAlbum() {
    $recents = simplexml_load_file('https://picasaweb.google.com/data/feed/base/user/' . $this->userId . '?kind=photo&access=public&max-results=15');

    foreach ($recents->entry as $photo) {
      $src = (string)$photo->content->attributes()->src;
      $md5 = md5(file_get_contents($src . '?imgmax=200'));
      $bad_md5 = 'd3c7a4cf13c71cc43aa6b65f390b658c';
      if ($md5 == $bad_md5) { continue; }

      $timestamp = $photo->published;
      $timestamp = strtotime($timestamp);

      $title = (string)$photo->title;

      return array(
        "name" => "Recent",
        "numphotos" => 15,
        "timestamp" => $timestamp,
        "thumb" => $src,
        "albumId" => "recent",
        "userId" => $this->userId
      );
    }
  }

  /*
  =========================== PUBLIC METHODS ===========================
  */

  public function getUserId() {
    return $this->userId;
  }

  public function getName() {
    return $this->name;
  }

  public function getAboutBlurb() {
    return $this->aboutBlurb;
  }

  public function getProfilePictureUrl() {
    return $this->profilePictureUrl;
  }

  public function getAlbums() {
    return $this->albums;
  }

  /*
  =========================== PUBLIC STATIC METHODS ===========================
  */

  public static function makeLink($link) {
    $link = strtolower($link);
    $replace = array('.', ',', ' ', '_', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '[', ']', ':', ';', '<', '>', '|');
    $link = str_replace($replace, '-', $link);
    $link=  preg_replace('~-{2,}~', '-', $link);
    if ($link[0] == '-') {$link = substr($link, 1);}
    if ($link[strlen($link)-1] == '-') {$link = substr_replace($link ,'',-1);}
    return $link;
  }


}

?>