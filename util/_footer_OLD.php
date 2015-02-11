<footer>

  © <?php $name = get_name(); echo $name[0]; ?> <?php $name = get_name(); echo $name[1]; ?>
  <br/>

  <?php
  $end = microtime(true);
  $creationtime = ($end - $start);
  echo '<small>';
  printf("Page created in %.6f seconds.", $creationtime);
  echo '</small>';
  ?>


</footer>

</body>

</html>