<?php session_start();

# validate the ID the person has input
$validids = array("11111111");

if (in_array($_POST["id"],$validids)) {
	echo "1";
}
else {
	echo "0";
}

?>