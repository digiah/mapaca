<?php
$filename = "mapaca-query-".date("m-d-y-His").".mapaca";
chdir("csv");
$fp = fopen($filename, 'w');
fwrite($fp,$_GET["alltags"]);
fclose($fp);
header("Content-disposition: attachment; filename=".$filename);
header("Content-type: text/plain");
readfile($filename);
unlink($filename);
?>
