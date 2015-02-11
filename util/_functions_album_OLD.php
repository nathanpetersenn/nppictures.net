<?php

require 'CONFIG_VARS.php';

// Returns detailed array of photos in an album from album ID
function get_photos_from_album($id) {
  global $USER_ID;

  $photos = array();
  $album = simplexml_load_file('https://picasaweb.google.com/data/feed/api/user/' . $USER_ID . '/albumid/' . $id);  

  foreach ($album->entry as $p) {
    $timestamp = $p->published;
    $timestamp = strtotime($timestamp);

    $title = (string)$p->title;

    $caption = (string)$p->summary;

    $src = (string)$p->content->attributes()->src;

    $gphoto = $p->children('http://schemas.google.com/photos/2007');
    $width = (int)$gphoto->width;
    $height = (int)$gphoto->height;

    $id = (string)$p->id;

    $pSet = array(
      "title" => $title,
      "caption" => $caption,
      "id" => $id,
      "src" => $src,
      "height" => $height,
      "width" => $width,
      "timestamp" => $timestamp
    );

    array_push($photos, $pSet);
  }

  return $photos;

}


// Same as get_photos_from_album($id) but hardcoded for recents
function get_recent_photos() {
  global $USER_ID;
  $photos = array();
  $recent = simplexml_load_file('https://picasaweb.google.com/data/feed/base/user/' . $USER_ID . '?kind=photo&access=public&max-results=15');

  foreach ($recent->entry as $p) {
    $timestamp = $p->published;
    $timestamp = strtotime($timestamp);

    $title = (string)$p->title;

    $caption = (string)$p->summary;

    $src = (string)$p->content->attributes()->src;

    // If an image was deleted after upload, it could still appear in the recents feed
    // Instead of the image, a "junk" image will be served
    // This is bad, so we get the hash of the image on the server and compare it with the hash of the bad image
    // If they match, they are the same so skip the image
    $md5 = md5(file_get_contents($src . '?imgmax=200'));
    $bad_md5 = 'd3c7a4cf13c71cc43aa6b65f390b658c';
    if ($md5 == $bad_md5) { continue; }

    $id = (string)$p->id;

    $media = $p->children('http://search.yahoo.com/mrss/');
    $media = $media->group;
    $width = (int)$media->content->attributes()->width;
    $height = (int)$media->content->attributes()->height;

    $pSet = array(
      "title" => $title,
      "caption" => $caption,
      "src" => $src,
      "id" => $id,
      "height" => $height,
      "width" => $width,
      "timestamp" => $timestamp
    );

    array_push($photos, $pSet);

  }

  return $photos;

}

// Returns string album title from album ID
function get_album_title_from_id($id) {
  if ($id == '') {
    return 'ERROR: NO ALBUM ID SPECIFIED';
  }

  global $USER_ID;
  $album = simplexml_load_file('https://picasaweb.google.com/data/feed/api/user/' . $USER_ID . '/albumid/' . $id);

  $title = (string)$album->title;
  return $title;
}

// Checks if album exists
function is_album($id) {
  global $USER_ID;
  $file = file_get_contents('https://picasaweb.google.com/data/feed/api/user/' . $USER_ID . '/albumid/' . $id);
  $file = explode(' ', $file);
  if ($file[0] == 'Invalid') {
    return false;
  } else {
    return true;
  }
}

// returns the next album in categories list from album ID
function get_next_album_id($id) {
  global $USER_ID;
  $categories = get_categories_array();

  $i = 0;
  foreach ($categories as $c) {
    if ($c['id'] == $id) {
      $next = $categories[$i+1]['id'];
      return $next;
    }
    $i++;
  }
}

// returns the previous album in categories list from album ID
function get_prev_album_id($id) {
  global $USER_ID;
  $categories = get_categories_array();

  $i = 0;
  foreach ($categories as $c) {
    if ($c['id'] == $id) {
      $prev = $categories[$i-1]['id'];
      return $prev;
    }
    $i++;
  }
}

// Returns array of photo exif info from photo ID
function get_photo_exif($photo_id) {

  $photo = simplexml_load_file($photo_id);
  $e = $photo->children('http://schemas.google.com/photos/exif/2007')->tags;

  $ar = array();

  $ar['exp'] = (float)$e->exposure;
  if ($ar['exp'] && $ar['exp'] < 1) { $ar['exposure'] = '1/' . round(1 / $ar['exp']); unset($ar['exp']); }
  if ($ar['exp'] && $ar['exp'] >= 1) { $ar['exposure'] = round($ar['exp']); unset($ar['exp']); }
  
  $ar['fstop'] = (float)$e->fstop;
  $ar['iso'] = (float)$e->iso;
  $ar['make']= (string)$e->make;
  $ar['model'] = (string)$e->model;
  $ar['focallength'] = (float)$e->focallength;
  $ar['time'] = ((int)$e->time) / 1000;

  return array_filter($ar);

}

?>