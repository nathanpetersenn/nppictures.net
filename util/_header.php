<?php
  $start = microtime(true);
?>

<!doctype html>

<html lang="en">

<head>

  <title><?= $title ?></title>

  <meta name="description" content="A collection of pictures taken by Nathan Petersen">
  <meta name="author" content="Nathan Petersen">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <link rel="stylesheet" type="text/css" media="screen, print, projection" href="/util/compressed.css.php" />

  <? /*

  all the following are combined into the compressed.css.php file:

  <link rel="stylesheet" href="/util/styles.css">
  <link rel="stylesheet" href="/util/styles.fonts.min.css">

  <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,800,300,700' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
  */ ?>

  <script src="/util/jquery.min.js"></script>
  <script src="/util/fullWidthGallery.js"></script>
  <script src="/util/fancybox/fancybox.min.js"></script>
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

<body<?php if (!$show_about) {  echo ' class="subpage"'; } ?>>

<header>
  <?php $src = '/util/timthumb/timthumb.php?src=' . $user->getProfilePictureUrl() . '&amp;w=300&amp;h=300&amp;q=90'; ?>

  <img src="<?= $src ?>" alt="profile picture" />
  <section>
    <h2><?php $n = $user->getName(); echo $n[0]; ?></h2>
    <h3><?php $n = $user->getName(); echo $n[1]; ?></h3>
  </section>

  <div><h1><a href="/">NP<span>Pictures</span></a></h1></div>

<?php

if ($show_about) {
  echo '<div class="about"><p>' . $user->getAboutBlurb() . '</p></div>';
}

?>

  <?php 
    /*
    echo '<div class="updated">Last updated: ';
    echo date('F jS, Y \a\t g:i:s A');
    echo '</div>';
    */
  ?>
</header>
