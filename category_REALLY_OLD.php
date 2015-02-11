<?php 
require 'util/_functions.php';
require 'util/_functions_album.php';
require 'util/_cache_start.php';

$id = $_REQUEST['id'];
$name = $_REQUEST['name'];

// filter out bad requests
if (!is_numeric($id) || !is_album($id)) {
  header('Location: ../..');
  die();
}

// make sure name in URL matches name of album with id $id
if ($name != make_link(get_album_title_from_id($id))) {
  $url = '/category/' . $id . '/' . make_link(get_album_title_from_id($id));
  header('Location: ' . $url);
  die();
}


$title = get_album_title_from_id($_REQUEST['id']) . ' | NPPictures';
$show_about = false;
require 'util/_header.php';

// generate infomation used to make right arrow on category page
$next_id = get_next_album_id($id);
if ($next_id != '') {
  $next_title = get_album_title_from_id($next_id);
  $next_title_link = make_link($next_title);
  $next_url = '/category/' . $next_id . '/' . $next_title_link;
}

// generate infomation used to make left arrow on category page
$prev_id = get_prev_album_id($id);
if ($prev_id != '' && $prev_id != 'recent') {
  $prev_title = get_album_title_from_id($prev_id);
  $prev_title_link = make_link($prev_title);
  $prev_url = '/category/' . $prev_id . '/' . $prev_title_link;
} else if ($prev_id == 'recent') {
  $prev_url = '/recent';
}

?>

<h4><?= get_album_title_from_id($_REQUEST['id']) ?></h4>

<div class="cat-nav">
<?php
if ($prev_url) {
  echo '<a class="prev" href="' . $prev_url . '"><img class="arrow" src="/util/left-arrow.gif" alt="left-arrow" /></a>';
} else {
  echo '<img class="arrow hidden" src="/util/left-arrow.gif" alt="left-arrow" />';
}
if ($next_url) {
  echo '<a class="next" href="' . $next_url . '"><img class="arrow" src="/util/right-arrow.gif" alt="right-arrow" /></a>';
} else {
  echo '<img class="arrow hidden" src="/util/right-arrow.gif" alt="right-arrow" />';
}
?>
</div>

<script>

  $(document).ready(function() {
    $("img.small").each(function() {
      var lrgsrc = $(this).data("lrgsrc");
      $(this).attr("src", lrgsrc);
    });

  });

  // uses Google Picasa's downloader!
  // appending &imgdl=1 downloads the original image
  function download(obj) {
    var src = $(obj).data("src");
    src = src.replace("imgmax=2000", "imgdl=1");

    window.location.href = src;
  }

</script>


<?php

$TARGET_HEIGHT = 200; // px

$photos = get_photos_from_album($_REQUEST['id']);
  /* RETURNS:
   * title
   * caption
   * id
   * src
   * height
   * width
   * timestamp
   */

echo '<div class="gallery">' . PHP_EOL;

foreach($photos as $p) {
  $crop_factor = $p['height'] / $TARGET_HEIGHT;
  $new_width = $p['width'] / $crop_factor;
  $new_width = round($new_width);

  $a_src = $p['src'] . '?imgmax=2000';

  $e = get_photo_exif($p['id']);

  $caption = '<button onClick=\'download(this)\' data-src=\'' . $a_src . '\' class=\'dl\'>Download</button>';

  if ($e['time'] || $e['model'] || $e['iso']) { $caption .= '<b>EXIF Information:</b><br/>'; } else { $caption .= ''; }
  if ($e['time']) { $caption .= 'Date: ' . date('F j, Y \a\t g:i A', $e['time']) . '<br/>'; }
  if ($e['model']) { $caption .= 'Camera: ' . $e['model'] . ' at ' . $e['focallength'] . 'mm<br/>'; }
  if ($e['iso']) { $caption .= $e['exposure'] . ' at f/' . $e['fstop'] . ', ISO ' . $e['iso'] . '<br/>'; }

  $img_tag_content = array(
    'class' => "small",
    //'src' => $p['src'] . '?imgmax=50',
    'src' => 'data:image/gif;base64,' . base64_encode(file_get_contents($p['src'] . '?imgmax=10')),
    'data-lrgsrc' => $p['src'] . '?imgmax=500',
    'data-height' => $TARGET_HEIGHT,
    'height' => $TARGET_HEIGHT,
    'data-width' => $new_width,
    'width' => $new_width,
    'alt' => $p['title']
    );

  $img_tag = '<img ';
  $i = 0;
  foreach ($img_tag_content as $attr => $value) {
    $img_tag .= $attr . '="' . $value . '"';
    if (++$i < sizeof($img_tag_content)) {
      $img_tag .= ' ';
    }
  }
  $img_tag .= ' />';


  echo '<a href="' . $a_src . '" caption="' . $caption . '">';
  echo $img_tag;
  echo '</a>';

}

echo '</div>' . PHP_EOL . PHP_EOL;

?>

<?php 
require 'util/_footer.php';
require 'util/_cache_end.php';
?>