<?php

$dberror = 0;
$mysqli = mysqli_connect("YOURSERVER", "YOURDATABASE", "YOURPASSWORD", "YOURUSERNAME");
if (mysqli_connect_errno($mysqli)) {
  $dberror = 1;
}

?>
