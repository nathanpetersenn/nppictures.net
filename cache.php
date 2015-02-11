<?php

function ago($time) {
   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");

   $now = time();

       $difference     = $now - $time;
       $tense         = "ago";

   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }

   $difference = round($difference);

   if($difference != 1) {
       $periods[$j].= "s";
   }

   return "$difference $periods[$j] $tense ";
}

$path = 'cache';
$files = array_diff(scandir($path), array('..', '.'));

?>

<!doctype html>

<html lang="en">
<head>
  <title>Cache Manager - NPPictures</title>

  <meta name="description" content="NPPictures Cache Manager">
  <meta name="author" content="Nathan Petersen">
  <meta charset="utf-8">

  <style>

    table, th, td {
      border: 1px solid #333;
      border-collapse: collapse;
      padding: 5px;
    }

    span.open {
      color: blue;
      cursor: pointer;
    }

    span.reload {
      color: green;
      cursor: pointer;
    }

    span.delete {
      color: red;
      cursor: pointer;
    }

  </style>

  <script src="/util/jquery.min.js"></script>

  <script src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.min.css">

  <script>
    $(document).ready(function() {
      $("span.delete").click(function() {
        disable();

        var that = $(this);
        var file = $(this).data("file");

        $.get("http://nppictures.net/util/API/delete.php?file=" + file, function(data) {
          if (data == "true") {
            // cache deleted
            that.parent().parent().remove();
            enable();
          } else {
            // failed to delete
            alert('failed to delete');
            enable();
          }
        });
      });

      $("span.open").click(function() {
        var href = $(this).data("href");
        window.open(href, "_blank");
      });

      $("span.reload").click(function() {
        disable();

        var file = $(this).data("file");
        var href = $(this).data("href");

        $.get("http://nppictures.net/util/API/reload.php?file=" + file + "&href=" + href, function(data) {
          if (data == "true") {
            enable();
            window.open(href, "_blank");
            location.reload();
          } else {
            alert('failed to reload');
            enable();
          }
        });
      });

    });

    function disable() {
      $('body').append('<div id="over" style="position:absolute; top:0; left:0; width:100%; height:100%; z-index:2; opacity:0.4; background-color:#000;"></div>');
    }

    function enable() {
      $("#over").remove();
    }

  </script>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
  <h1>Cache Manager</h1>

  <p>Use this page to manage NPPicture's cache of its pages</p>

  <table>
    <thead>
      <tr>
        <th>Open</th>
        <th>Reload</th>
        <th>Mod Date</th>
        <th>Mod Time</th>
        <th>File Name</th>
        <th>Delete</th>
      </tr>
    </thead>

    <tbody>
    <?php

      $i = 0;
      foreach ($files as $file) {

        $url = str_replace('.cache', '', $file);
        $pattern = '/[\?|\&]\w+\=/i';
        $replacement = '/';
        $url = preg_replace($pattern, $replacement, $url);
        if ($url == 'index') { $url = ''; }

        $mtime = filemtime('cache/' . $file);
        $mdate = date("F j, Y, g:i a", $mtime);
        
        $mtime = ago($mtime);

        echo '<tr class="el' . $i++ . '">';
        echo '<td><span class="open" data-href="/' . $url . '">Open</span></td>';
        echo '<td><span class="reload" data-href="/' . $url . '" data-file="' . urlencode($file) . '">Reload</span></td>';
        echo '<td>' . $mdate . '</td>';
        echo '<td>' . $mtime . '</td>';
        echo '<td>' . $file . '</td>';
        echo '<td><span class="delete" data-file="' . urlencode($file) . '">Delete</span></td>';
        echo '</tr>';
      }

    ?>
    </tbody>

  </table>

<hr/>

<h2>Automatic regenerator!</h2>

<button class="start">Start</button>

<ul class="results"></ul>

<script>

<?php

$MAIN_URLS = array();

foreach ($files as $file) {
  $url = str_replace('.cache', '', $file);
  $pattern = '/[\?|\&]\w+\=/i';
  $replacement = '/';
  $url = preg_replace($pattern, $replacement, $url);
  if ($url == 'index') { $url = ''; }

  array_push($MAIN_URLS, $url);
}

echo 'var urls = ' . json_encode($MAIN_URLS) . ';';

?>

$(document).ready(function() {
  $("button.start").click(function() {
    $("button.start").prop('disabled', true);
    generatePages();
  });
});

function generatePages() {
  $("span.delete").each(function() {
    $(this).click();
  });

  $("ul.results").empty();
  $("ul.results").append("<li>Once a page regenerates, it will appear below:</li><li>Reload page once complete!</li><br/>");

  $.each(urls, function(key, value) {
    var url = 'http://nppictures.net/' + value;
    $("<div/>").load(url, function() {
      //$("table tbody tr.el" + key).remove();
      $("ul.results").append("<li>" + value + "</li>");
    }); 
  });
}

</script>

</body>

</html>