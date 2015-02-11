<?php 
require 'util/_functions.php';
require 'util/_functions_album.php';
require 'util/_cache_start.php';
$title = 'Recent | NPPictures';
$show_about = false;
require 'util/_header.php';

$cats = get_categories_array();
$next_id = $cats[1]['id'];
$next_title = make_link(get_album_title_from_id($next_id));
$next_url = '/category/' . $next_id . '/' . $next_title;

?>

<h4>Recent</h4>
<div class="cat-nav">
<?php
echo '  <img class="arrow hidden" src="/util/left-arrow.gif" alt="left-arrow" />';
echo '  <a class="next" href="' . $next_url . '"><img class="arrow" src="/util/right-arrow.gif" alt="right-arrow" /></a>';
?>
</div>

<?php

$TARGET_HEIGHT = 200; // px

$photos = get_recent_photos();
  /* RETURNS:
   * title
   * caption
   * src
   * id
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

  echo '<a href="' . $a_src . '">' . PHP_EOL;
  echo '<img src="' . $p['src'] . '?imgmax=500" data-height="' . $TARGET_HEIGHT . '" height="' . $TARGET_HEIGHT . '" data-width="' . $new_width . '" width="' . $new_width . '" alt="' . $p['title'] . '"/>';
  echo '</a>' . PHP_EOL;

}
echo '</div>' . PHP_EOL . PHP_EOL;

?>

<?php 
require 'util/_footer.php';
require 'util/_cache_end.php';
?>