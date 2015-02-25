<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php


?>
###<br />
<?php echo count($_SESSION["id_array"])." hits...<br />"; ?>

<?php

include("dbheader.php"); 

$results = array();
$count = 0;
foreach ($_SESSION["id_array"] as $id) {
	$q = str_replace("[theid]",$id,$_SESSION["allq"]);
	$r = $mysqli->query($q) or die ($q."<br /><br />");
	$hit = mysqli_fetch_array($r) or die ("<br /><br />".mysqli_error($mysqli));
	$count++; 
	if (isset($results[$hit["Year"]])) {
		//echo $count.") incremented at ".$hit[$_GET["by"]]."<br />"; 
		$results[$hit[$_GET["by"]]]++;
	}
	else {
		//echo $count.") added at ".$hit[$_GET["by"]]."<br />"; 
		$results[$hit[$_GET["by"]]] = 1;
	}
}

asort($results);
print_r(array_reverse($results, true));

?>
</body>
</html>