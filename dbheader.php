<?php
/*
Database server: mdb41.pvt.hawaii.edu
Database name: arthumdigital_p
Database login: arthumdigital
Database password: Dh9PCt8yvH4Ec8t3
*/
$dberror = 0;
$mysqli = mysqli_connect("mdb41.pvt.hawaii.edu", "arthumdigital", "Dh9PCt8yvH4Ec8t3", "arthumdigital_p");
if (mysqli_connect_errno($mysqli)) {
  $dberror = 1;
}

?>