<?php

$START_TIME = microtime(true);

define(PICASA_USER, 'nppictures.storage');

require_once 'util/PicasaWebUser.php';
require_once 'util/PicasaWebAlbum.php';
require_once 'util/PicasaWebPhoto.php';

$user = new PicasaWebUser(PICASA_USER);

require 'util/_cache_start.php';

?>

<!doctype html>

<html lang="en">

<head>

  <title>NPPictures</title>

  <meta name="description" content="A collection of pictures taken by Nathan Petersen">
  <meta name="author" content="Nathan Petersen">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <link rel="stylesheet" type="text/css" media="screen, print, projection" href="/util/compressed.css.php?page=index" />

  <script src="/util/jquery.min.js"></script>
  <script src="/util/scripts.js"></script>

  <!-- Google Analytics code -->
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-47404422-1', 'nppictures.net');
    ga('send', 'pageview');
  </script>

</head>

<body>

<header>
  <?php //$src = '/util/timthumb/timthumb.php?src=' . $user->getProfilePictureUrl() . '&amp;w=300&amp;h=300&amp;q=90'; ?>
  <?php $src = 'data:image/jpeg;base64,' . base64_encode(file_get_contents('http://nppictures.net' . '/util/timthumb/timthumb.php?src=' . $user->getProfilePictureUrl() . '&amp;w=300&amp;h=300&amp;q=90')); ?>

  <img src="<?= $src ?>" alt="profile picture" />
  <section>
    <h2><?php $n = $user->getName(); echo $n[0]; ?></h2>
    <h3><?php $n = $user->getName(); echo $n[1]; ?></h3>
  </section>

  <div><h1><a href="/">NP<span>Pictures</span></a></h1></div>

  <?php
    $about = $user->getAboutBlurb();

    $about = explode('.', $about);
    $about_intro = $about[0] . '.';
    $about_all = implode('.', $about);
  ?>

  <div class="about">
    <p>
      <span class="intro"><?= $about_intro ?> <span class="more" style="color: #aaa; cursor: pointer;">more...</span></span>
      <span class="all" style="display:none;"><?= $about_all ?> <span class="less" style="color: #aaa; cursor: pointer;">less...</span></span>
    </p>
  </div>

</header>

<script>
$(document).ready(function() {

  var dpr = window.devicePixelRatio;
  var defaultWidth = 300;
  var scaled = defaultWidth * dpr;

  $("img.main").each(function() {
    var lrgsrc = $(this).data("lrgsrc");

    var w = $(window).width();
    if (w <= 715) {
      var imgWidth = Math.round((w - 30) / 2);
      scaled = imgWidth * dpr;
    }

    lrgsrc = lrgsrc + "&w=" + scaled + "&h=" + scaled;

    $(this).attr("src", lrgsrc);
  });

  $("span.more").click(function() {
    $("span.intro").hide();
    $("span.all").show(500);
  });

  $("span.less").click(function() {
    $("span.intro").show();
    $("span.all").hide();
  });

});

</script>

<h4>Categories</h4>

<ul class="cat">

<?

$albums = $user->getAlbums();

foreach ($albums as $album) {
  echo '<li>' . PHP_EOL;

  $thumb = urlencode($album['thumb'] . '?imgmax=1000');

  $small_src = '/util/timthumb/timthumb.php?src=' . $thumb . '&w=100&h=100&q=30';
  //$large_src = '/util/timthumb/timthumb.php?src=' . $thumb . '&q=90&w=600&h=600'; // no need for w and h because javascript adds it, taking into account the page size dpr and screen width! :)
  $large_src = '/util/timthumb/timthumb.php?src=' . $thumb . '&q=90';

  $small_src = 'data:image/jpeg;base64,' . base64_encode(file_get_contents('http://nppictures.net' . $small_src));

  if ($album['albumId'] == 'recent') {
    echo '<a href="recent">';
  } else {
    echo '<a href="category/' . $album['albumId'] . '/';
    echo PicasaWebUser::makeLink($album['name']);
    echo '">' . PHP_EOL;
  }

  echo '<img class="main" src="' . $small_src . '" data-lrgsrc="' . $large_src . '" alt="' . $album['name'] . '" />' . PHP_EOL; // main category cover

  echo '<div>';
  echo $album['name'];
  if ($album['numphotos'] == 1) { echo '<span> - ' . $album['numphotos'] . ' photo</span>'; } else { echo '<span> - ' . $album['numphotos'] . ' photos</span>'; }
  echo '</div>' . PHP_EOL;
  echo '</a>' . PHP_EOL;
  echo '</li>' . PHP_EOL . PHP_EOL;

}

?>

</ul>

<?php 
require 'util/_footer.php';
require 'util/_cache_end.php';
?>