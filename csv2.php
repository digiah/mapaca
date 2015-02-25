<?php session_start();

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
	
	echo $filename;
}

?>
