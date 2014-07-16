<?php

$contents = ob_get_contents();
ob_end_clean();

$handle = fopen($cachefile, "w");
fwrite($handle, $contents);
fclose($handle);

include($cachefile);

?>