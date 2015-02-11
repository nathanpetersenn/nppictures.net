<footer>

  All images &copy; 2008-2015 <?php $name = $user->getName(); echo $name[0] . ' ' . $name[1]; ?>
  <br/>
  <small>All Rights Reserved.</small>

</footer>

<script>

$(document).ready(function() {

  var footerClicks = 0;
  var creationTime = "<?= '<small>Server rendering time: <b>' . round((microtime(true) - $START_TIME), 3) . '</b> seconds</small>' ?>";

  $("footer").click(function() {
    footerClicks++;

    if (footerClicks > 8) {
      return false;
    }

    if (footerClicks > 7) {
      $(this).append("<br/>" + creationTime);
      $(this).css({
        "background-color" : "#eee",
        "cursor" : "default"
      });

      window.scrollTo(0, document.body.scrollHeight);
    }

  });
});

</script>

</body>

</html>