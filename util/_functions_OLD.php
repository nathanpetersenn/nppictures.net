<?php

require 'CONFIG_VARS.php';

// Returns URL of image source of album
// searches for $searchTitle
function _getAlbumCover($searchTitle) {
  global $USER_ID;
  $mypix = simplexml_load_file('http://picasaweb.google.com/data/feed/api/user/' . $USER_ID);
  $PROFILE_SOURCE_IMAGE = '';

  foreach ($mypix->entry as $pixinfo) {
    // GET ALBUM 'Profile Data' COVER

    $title = $pixinfo->title;

    if ($title == $searchTitle) {
      $foo = $pixinfo->children('http://search.yahoo.com/mrss/');
      $media = $foo->group;
      $PROFILE_SOURCE_IMAGE = $media->content->attributes()->url;
      break;
    }
  }

  return $PROFILE_SOURCE_IMAGE;
}

// Returns profile picture URL for Picasa account set in CONFIG_VARS.php
// Uses 'Profile Data' album cover as profile picture
// If 'Profile Data' doesn't exist, it returns the real profile picture for the account
function get_profile_pic() {
  $PROFILE_SOURCE_IMAGE = _getAlbumCover('Profile Data');

  if ($PROFILE_SOURCE_IMAGE == '') {
    // GET REAL PROFILE PICTURE
    $PROFILE_SOURCE_IMAGE = _getAlbumCover('Profile Photos');
  }

  return $PROFILE_SOURCE_IMAGE . '?imgmax=500';

}

// Returns the about text for the index page
// Comes from description of 'Profile Data' album
function get_about_blurb() {
  global $USER_ID;
  $mypix = simplexml_load_file('http://picasaweb.google.com/data/feed/api/user/' . $USER_ID);

  $ABOUT_BLURB = '';

  foreach ($mypix->entry as $pixinfo) {
    $title = $pixinfo->title;

    if ($title == 'Profile Data') {
      $ABOUT_BLURB = $pixinfo->summary;
      break;
    }
  }

  return $ABOUT_BLURB;

}


// Returns an array of the albums for the Picasa account
function get_categories_array() {
  global $USER_ID;
  $mypix = simplexml_load_file('http://picasaweb.google.com/data/feed/api/user/' . $USER_ID);
  
  $cat = array();
  array_push($cat, _get_recent_album_cover());

  foreach ($mypix->entry as $pixinfo) {

    $cat_name = $pixinfo->title;

    if ($cat_name == 'Profile Photos' || $cat_name == 'Profile Data' || $cat_name == 'Scrapbook Photos') { continue; }

    $foo = $pixinfo->children('http://schemas.google.com/photos/2007');
    $cat_id = $foo->id;
    $cat_numphotos = $foo->numphotos;
    $cat_timestamp = $foo->timestamp;
    $cat_timestamp = (float)$cat_timestamp; 
    $cat_timestamp /= 1000;   

    $foo = $pixinfo->children('http://search.yahoo.com/mrss/');
    $media = $foo->group;
    $cat_thumb = $media->content->attributes()->url;

    $tmp = array (
        "name" => (string)$cat_name,
        "numphotos" => (int)$cat_numphotos,
        "timestamp" => (float)$cat_timestamp,
        "thumb" => (string)$cat_thumb,
        "id" => (int)$cat_id
      );

    array_push($cat, $tmp);
  }
  
  return $cat;

}

// PRIVATE FUNCTION
// Used by get_categories_array() to manually add the recent album
function _get_recent_album_cover() {
  global $USER_ID;
  $recent = simplexml_load_file('https://picasaweb.google.com/data/feed/base/user/' . $USER_ID . '?kind=photo&access=public&max-results=15');

  foreach ($recent->entry as $p) {
    $src = (string)$p->content->attributes()->src;
    $md5 = md5(file_get_contents($src . '?imgmax=200'));
    $bad_md5 = 'd3c7a4cf13c71cc43aa6b65f390b658c';
    if ($md5 == $bad_md5) { continue; }

    $timestamp = $p->published;
    $timestamp = strtotime($timestamp);

    $title = (string)$p->title;

    return array(
      "name" => "Recent",
      "numphotos" => 15,
      "timestamp" => $timestamp,
      "thumb" => $src,
      "id" => "recent"
    );

  }

}

// Returns an array of the user's name split by spaces
// Ex. [John, Doe] -- Ex. [John, Henry, Doe]
function get_name() {
  global $USER_ID;
  $info = simplexml_load_file('http://picasaweb.google.com/data/feed/api/user/' . $USER_ID);

  $name = $info->author->name;
  $name = explode(' ', $name);
  return $name;
}

// Utility function that converts string into a link formated string
// Ex. "This is my first STRING and HAHA :) I am so...happy" --> "this-is-my-first-string-and-haha-i-am-so-happy"
function make_link($link) {
  $link = strtolower($link);
  $replace = array('.', ',', ' ', '_', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '[', ']', ':', ';', '<', '>', '|');
  $link = str_replace($replace, '-', $link);
  $link=  preg_replace('~-{2,}~', '-', $link);
  if ($link[0] == '-') {$link = substr($link, 1);}
  if ($link[strlen($link)-1] == '-') {$link = substr_replace($link ,'',-1);}
  return $link;
}

// Returns array of all images in an album from album ID
function get_pictures_in_album_by_id($id) {
  global $USER_ID;
  $album = simplexml_load_file('https://picasaweb.google.com/data/feed/api/user/' . $USER_ID . '/albumid/' . $id); 

  $imgs = array();

  foreach ($album->entry as $p) {
    $src = (string)$p->content->attributes()->src;
    array_push($imgs, $src);
  }

  return $imgs;
}

?>