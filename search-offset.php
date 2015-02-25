<?php session_start();

include("dbheader.php"); 

/* $matches[1] contains the citation search terms */
$regex = "/`Content` LIKE '%(.*?)%'/";
preg_match_all($regex, $_SESSION["q"], $matches, PREG_OFFSET_CAPTURE);

$offset = $_GET["offset"];

$the_content = array();

$hits = count($_SESSION["id_array"]);

$limit = 10;

if (($offset + 10) > $hits) {
	$limit = $hits - $offset;
}

for ($i=0;$i<$limit;$i++) {
	
	$thecontent = "";
	
	$q = str_replace("[theid]",$_SESSION["id_array"][$offset + $i],$_SESSION["allq"]);
	#echo $i.") ".$q."<br />";
	$r = $mysqli->query($q) or die ($q."<br /><br />".mysqli_error($mysqli));
	
	$citation = mysqli_fetch_array($r);
	$content = "<div class='citation'>".$citation["Content"]."</div>";
	for ($j=0;$j<count($matches[1]);$j++) {
		$content = str_replace($matches[1][$j][0],"<span class=\"found\">".$matches[1][$j][0]."</span>",$content);
		$content = str_replace(ucfirst($matches[1][$j][0]),"<span class=\"found\">".ucfirst($matches[1][$j][0])."</span>",$content);
		$content = str_replace(strtoupper($matches[1][$j][0]),"<span class=\"found\">".strtoupper($matches[1][$j][0])."</span>",$content);
	}
	$thecontent .= "<div class='resultdiv' id='result".$i."'>";
			
			$thecontent .= "<div style='float: left;'>";
			
$titleq = "select * from `_dighum_music_bibliotitle` where `BiblioTitle_ID` = '".$citation["BiblioTitle_ID"]."'";
			$titler = $mysqli->query($titleq) or die (mysqli_error($mysqli));
			$thetitle = mysqli_fetch_array($titler);
			
			/* outtake for now: <span class='titleup'>&#9650;</span> <span class='titledn'>&#9660;</span>*/
			
			$thecontent .= "<span class='titleup'>&#9650;</span> <span class='titledn'>&#9660;</span><span style='font-size:200%;'>".$thetitle["Gen_ttl"]."</span><br />";
			$thecontent .= "<span class='yearup'>&#9650;</span> <span class='yeardn'>&#9660;</span> <span class='orangecopy'>YEAR:</span> ".$citation["Year"]."&nbsp;&nbsp;&nbsp;";
			$thecontent .= "<span class='monthup'>&#9650;</span> <span class='monthdn'>&#9660;</span> <span class='orangecopy'>MONTH:</span> ".$citation["Month"]."&nbsp;&nbsp;&nbsp;";
			$thecontent .= "<span class='dayup'>&#9650;</span> <span class='daydn'>&#9660;</span> <span class='orangecopy'>DAY:</span> ".$citation["Day"]."<br />";
			$thecontent .= "<span class='pubcityup'>&#9650;</span> <span class='pubcitydn'>&#9660;</span> <span class='orangecopy'>CITY:</span> ".$citation["PubCity"]."&nbsp;&nbsp;&nbsp;";
			$thecontent .= "<span class='pubcolonyup'>&#9650;</span> <span class='pubcolonydn'>&#9660;</span> <span class='orangecopy'>COLONY:</span> ".$citation["PubColony"]."<br />";
			
			$thecontent .= "</div>";
			
			$thecontent .= "<div style='float: right;'>";
			
			$thecontent .= "<span style='font-size:200%;'>&nbsp;</span><br /><span class='orangecopy' >LOCATION:&nbsp;&nbsp;<span class='creamcopy'>".$citation["Location"]."</span></span>";
			
			$regionq = "select * from `_dighum_music_region` where `region_ID` = '".$citation["LocationCodes"]."'";
			$regionr = $mysqli->query($regionq) or die (mysqli_error($mysqli));
			$theregion = mysqli_fetch_array($regionr);
			
			$thecontent .= "<br /><span class='orangecopy'>REGION:&nbsp;&nbsp;<span class='creamcopy'>".$theregion["region"]."</span></span>";
			
			$thecontent .= "</div><div style='clear: both;'></div>";
			
			$thecontent .= $content."<br />";
			
			$thecontent .= "<div style='width:150px;margin:auto;'><img src='fleur-creamx5.png' style='width:150px;height:32px;' /></div>";

	
	$the_content[] = $thecontent;
	
}	

echo implode("[&]",$the_content);

?>