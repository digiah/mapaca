<?php session_start();

include("dbheader.php"); 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<script>

$(document).ready(function(e) {
	
	$("#home").click(function(e) {
        document.location.href="index.php";
    });
	
	$("#modifysearch").click(function(e) {
        document.location.href="search.php";
    });
	
});

</script>

<title>CSV Export</title>

<link href="search-citation.css" rel="stylesheet" type="text/css" />
<style>

@import url(http://fonts.googleapis.com/css?family=Crimson+Text|Averia+Libre);

#navigation {
	margin: 5px;
	padding: 5px;
}

.resultbutton {
	font-family: 'Averia Sans Libre', sans-serif;
	width: 32px; height: 32px; padding: 3px;
	line-height: 32px;
	border: 1px solid #333;
	cursor: pointer;
	margin: 2px;	
}

.resultbutton:hover {
	background-color:#ccc;
}

.container {
	width: 900px;
	margin: auto;
	margin-top: 25px;
	padding: 15px;
	border: 1px solid #e15c11;
	
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;	
}

</style>
</head>

<body>
<?php

include("dbheader.php"); 

if (isset($_SESSION["q"])) {
	
	$idarray = array();
	
	$idq = "select `ID` from `_dighum_music_citation` where ".$_SESSION["q"];
	$allq = "select * from `_dighum_music_citation` where ".$_SESSION["q"]." AND `ID` = [theid]";
	
	/* $matches[1] contains the citation search terms */
	$regex = "/`Content` LIKE '%(.*?)%'/i";
	preg_match_all($regex, $_SESSION["q"], $matches, PREG_OFFSET_CAPTURE);
	$r = $mysqli->query($idq) or die ($idq."<br /><br />".mysqli_error($mysqli));
	
	//$hits = mysqli_num_rows($r);
	while ($citation = mysqli_fetch_array($r)) {
		$idarray[] = $citation["ID"];
	}
	
	$idarray = array_reverse($idarray);
	
	$filename = "csv/".date("m-d-y-His")."mapaca.csv";
	
	$fp = fopen($filename, 'w');
	
	fputcsv($fp, array("Year","Publication City","Publication Colony","Publication Title","Citation"));
	
	for ($i=0;$i<count($idarray);$i++) {
			
		$iq = str_replace("[theid]",$idarray[$i],$allq);
		$r = $mysqli->query($iq) or die ($iq."<br /><br />".mysqli_error($mysqli));
		$citation = mysqli_fetch_array($r);
		
		$pubCitation = "\"".$citation["Content"]."\"";
		$year = $citation["Year"];
		$pubCity = $citation["PubCity"];
		$pubColony = $citation["PubColony"];
		
		$titleq = "select * from `_dighum_music_bibliotitle` where `BiblioTitle_ID` = '".$citation["BiblioTitle_ID"]."'";
		$titler = $mysqli->query($titleq) or die (mysqli_error($mysqli));
		$thetitle = mysqli_fetch_array($titler);
		$pubTitle = $thetitle["Gen_ttl"];
		
		fputcsv($fp, array($year,$pubCity,$pubColony,$pubTitle,$pubCitation));
		
		//echo $year.",".$pubCity.",".$pubColony.",".$pubTitle.",\"".str_replace("\"","\"\"",$pubCitation)."\"\n";
	}
	
	fclose($fp);
	
}

?>

<div class="container">

<h1>MAPACA <span class="creamcopy" style="font-size: 0.5em;">Music and the Performing Arts in Colonial America</span> <button class="dashbutton" style="font-size: 0.5em;" id="home">HOME</button><button class="dashbutton" style="font-size: 0.5em;" id="modifysearch">MODIFY SEARCH</button></h1>

<a class="dashbutton" style="font-size: 0.5em;margin-top: 25px;margin-bottom:25px;" href="<?php echo $filename; ?>" target="_blank">DOWNLOAD CSV</a>
<br /><br />
</div>

</div>
</body>
</html>