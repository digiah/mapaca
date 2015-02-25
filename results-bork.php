<?php session_start();

include("dbheader.php"); 

$proceed = false;

if (isset($_SESSION["q"])) {
	$_SESSION["id_array"] = array();
	if (isset($_GET["field"])) {
	
		$idq = "select `ID` , `".$_GET["field"]."` from `_dighum_music_citation` where " . $_SESSION["q"]. " order by `".$_GET["field"]."` ".$_GET["direction"];
		$_SESSION["allq"] = "select * from `_dighum_music_citation` where ".$_SESSION["q"]." AND `ID` = [theid]" ;

		/* $matches[1] contains the citation search terms */
		$regex = "/`Content` LIKE '%(.*?)%'/i";
		preg_match_all($regex, $_SESSION["q"], $matches, PREG_OFFSET_CAPTURE);
	
		$proceed = true;
	}
	else {
		$idq = "select `ID` from `_dighum_music_citation` where ".$_SESSION["q"];/* $matches[1] contains the citation search terms */
		$regex = "/`Content` LIKE '%(.*?)%'/i";
		preg_match_all($regex, $_SESSION["q"], $matches, PREG_OFFSET_CAPTURE);
		$proceed = true;
	}
}

if (isset($_POST["q"])) {

	$_SESSION["qdata"] = $_POST["qdata"];
	
	$_SESSION["id_array"] = array();
	$content_array = array();
	
	$_SESSION["q"] = filter_var(htmlspecialchars_decode(urldecode($_POST["q"])));	
	
	$idq = "select `ID` from `_dighum_music_citation` where ".$_SESSION["q"];
	$_SESSION["allq"] = "select * from `_dighum_music_citation` where ".$_SESSION["q"]." AND `ID` = [theid]";
	
	/* $matches[1] contains the citation search terms */
	$regex = "/`Content` LIKE '%(.*?)%'/i";
	preg_match_all($regex, $_SESSION["q"], $matches, PREG_OFFSET_CAPTURE);
	
	$proceed = true;
}

if ($proceed == true) {
	
	$r = $mysqli->query($idq) or die ($idq."<br /><br />".mysqli_error($mysqli));
	
	$hits = mysqli_num_rows($r);
	
	
	if ($hits > 0) {
		
		while ($citation = mysqli_fetch_array($r)) {
			#echo $citation["ID"]." ";
			$_SESSION["id_array"][] = $citation["ID"];
		}
		
		$_SESSION["id_array"] = array_reverse($_SESSION["id_array"]);
		
		$offset = 0;
		
		if (count($_SESSION["id_array"]) < 10) {
			$limit = count ($_SESSION["id_array"]);
		}
		else {
			$limit = 10;
		}
		/* if less than 10, $i < num_rows */
		/* sort options for whole date, city, colony, title */
		/* case sensitivity for orange highlight */
		
		for ($i=0;$i<$limit;$i++) {
		
			$thecontent = "";
			
			$iq = str_replace("[theid]",$_SESSION["id_array"][$i],$_SESSION["allq"]);
			#echo $i.") ".$iq."<br />";
			$r = $mysqli->query($iq) or die ($iq."<br /><br />".mysqli_error($mysqli));
			
			$citation = mysqli_fetch_array($r);
			$content = "<div style='padding: 15px'>".$citation["Content"]."</div>";
			for ($j=0;$j<count($matches[1]);$j++) {
				$content = str_replace($matches[1][$j][0],"<span class=\"found\">".$matches[1][$j][0]."</span>",$content);
				$content = str_replace(ucfirst($matches[1][$j][0]),"<span class=\"found\">".ucfirst($matches[1][$j][0])."</span>",$content);
				$content = str_replace(strtoupper($matches[1][$j][0]),"<span class=\"found\">".strtoupper($matches[1][$j][0])."</span>",$content);
			}
			$thecontent .= "<div class='resultdiv' id='result".$i."'>";
			
			$thecontent .= "<span class='yearup'>&#9650;</span> <span class='yeardn'>&#9660;</span> YEAR: ".$citation["Year"]."<br />";
			$thecontent .= "<span class='pubcityup'>&#9650;</span> <span class='pubcitydn'>&#9660;</span> PUBLICATION CITY: ".$citation["PubCity"]."<br />";
			$thecontent .= "<span class='pubcolonyup'>&#9650;</span> <span class='pubcolonydn'>&#9660;</span> PUBLICATION COLONY: ".$citation["PubColony"]."<br />";
			
			$titleq = "select * from `_dighum_music_bibliotitle` where `BiblioTitle_ID` = '".$citation["BiblioTitle_ID"]."'";
			#$thecontent .= $titleq."<br />";
			
			$titler = $mysqli->query($titleq) or die (mysqli_error($mysqli));
			$thetitle = mysqli_fetch_array($titler);
			
			$thecontent .= "PUBLICATION TITLE: ".$thetitle["Gen_ttl"]."<br />";
			
			/*
			$thecontent .= "<div id='citationsubject".$i."' class='citationsubject'>SUBJECT:<br />";
			$subjectq = "select * from `_dighum_music_subjectbiblio` where `Citation_ID` = '".$citation["Citation_ID"]."'";
			$subjectr = $mysqli->query($subjectq) or die (mysqli_error($mysqli));
			$subjects = array();
			
			while ($subject = mysqli_fetch_array($subjectr)) {
				$sq = "select `Subject` from `_dighum_music_subject` where `Subject_ID` = '".$subject["Subject_ID"]."'";
				$sr = $mysqli->query($sq) or die (mysqli_error($mysqli));
				$sub = mysqli_fetch_array($sr);
				
				$thecontent .=  $sub["Subject"]."<br />";
			}	
			$thecontent .= "</div>";
			*/
			
			$thecontent .= $content."<br />";
			
			$thecontent .= "</div>";
			
			$content_array[] = $thecontent;
		}
	
	}
	
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<script>

var searchResults = { }

searchResults.showing = 0;
searchResults.offset = 0;
searchResults.hits = <?php echo $hits; ?>;

$(document).ready(function(e) {
	
	if ($("#result0").length > 0) {
		
		$("#result0").show();
		
		$("#prevresults").hide();
		$("#prevresults").click(function() { offset(-10); });
		
		if (searchResults.hits > 10) {
			$("#nextresults").show();
			$("#start").html("1");
		}
		if (searchResults.hits > 0) {
			$("#start").html("1");
		}
		
		$("#nextresults").click(function() { offset(10); });
		
		for (i=0;i<10;i++) {
			$("#offset"+(i+searchResults.offset)).click(function() { showResult(i+searchResults.offset); });
		}
		
		$(".yearup").click(function() { appendSort('Year','DESC'); $(".yearup").css("color", "#0F0"); $(".yeardn").css("color", "#000"); });
		$(".yeardn").click(function() { appendSort('Year','ASC'); $(".yearup").css("color", "#000"); $(".yeardn").css("color", "#0F0"); });
		$(".topYear").click(function() { document.location.href='make-table.php?by=Year'; });
		
		$(".pubcityup").click(function() { appendSort('PubCity','DESC'); $(".pubcityup").css("color", "#0F0"); $(".pubcitydn").css("color", "#000"); });
		$(".pubcitydn").click(function() { appendSort('PubCity','ASC'); $(".pubcityup").css("color", "#000"); $(".pubcitydn").css("color", "#0F0"); });
		$(".topPubCity").click(function() { document.location.href='make-table.php?by=PubCity'; });
		
		$(".pubcolonyup").click(function() { appendSort('PubColony','DESC'); $(".pubcolonyup").css("color", "#0F0"); $(".pubcolonydn").css("color", "#000"); });
		$(".pubcolonydn").click(function() { appendSort('PubColony','ASC'); $(".pubcolonyup").css("color", "#000"); $(".pubcolonydn").css("color", "#0F0"); });
		$(".topPubColony").click(function() { document.location.href='make-table.php?by=PubColony'; });
	}
	else {
		
		$("#prevresults").hide();
		$("#nextresults").hide();
		$("#start").html("0");
	}
	
});

function appendSort(field, direction) {
	document.location.href="search.php?field="+field+"&direction="+direction;
}

function showResult(id) {
	$("#result"+searchResults.showing).hide();
	$("#result"+id).show();
	searchResults.showing = id;
}

function offset(amount) {
	
	if (amount > 0) {	
		searchResults.offset += 10;
	}
	else {	
		searchResults.offset -= 10;
	}
	
	data = "offset="+searchResults.offset;
	
	/* load up the next or previous ten contents via ajax */
	
	jQuery.ajax({
	   type: "GET",
	   url: "search-offset.php",
	   data: data,
	   success: function(msg) { 
			if (msg == "0") { 
				alert("AJAX error. Not your fault, please try again later."); 
			}
			else {
				$("#resultscontainer").html("");
				divs = msg.split("[&]");
				for (i=0;i<divs.length;i++) {
					$("#resultscontainer").html($("#resultscontainer").html()+divs[i]);
				}
			}
	   },
	   error: function(msg) { alert("Server error. Not your fault, please try again later."); }
	 });
		 
	if (searchResults.offset > 9) {
		$("#prevresults").show();
	}
	if (searchResults.offset == 0) {
		$("#prevresults").hide();
	}
	$("#start").html(searchResults.offset+1);
	if ((searchResults.offset + 10) > searchResults.hits) {
		$("#end").html(searchResults.hits);
		$("#nextresults").hide();
	}
	else {
		$("#end").html(searchResults.offset+10);
		$("#nextresults").show();
	}
}

</script>

<title>Search Results</title>

<link href="search-citation.css" rel="stylesheet" type="text/css" />
<style>

@import url(http://fonts.googleapis.com/css?family=Crimson+Text|Averia+Libre);
#navigation {
	margin: 5px;
	padding: 5px;
}

.resultdiv { 
	margin: 5px;
	border: 1px solid #88400d;
	padding: 5px;
	font-family: 'Crimson Text', serif;
	font-size: 0.85em;
	display: block;
}

.resultbutton {
	width: 32px; height: 32px; padding: 3px;
	line-height: 32px;
	border: 1px solid #333;
	cursor: pointer;
	margin: 2px;	
	font-family: 'Averia Libre', serif;
    }

.resultbutton:hover {
	background-color:#ccc;
}

.found {
	background-color: #FFF;
	color: #1a1915;
    font-family: 'Averia Libre', serif;
	/*padding-left: 3px; padding-right: 3px;	*/
}

.yearup, .yeardn, .pubcityup, .pubcitydn, .pubcolonyup, .pubcolonydn, .topYear, .topPubCity, .topPubColony {
	cursor: pointer;
	font-size: smaller;
}

.topYear, .topPubCity, .topPubColony {
	text-decoration: underline;
}

.citationsubject {
	margin: 3px;
	padding: 3px;	
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

<div class="container">

<div id="navigation">HITS: <span id="start"></span> to <span id="end"><?php echo ($hits < 10)?$hits:"10"; ?></span> of <?php echo $hits; ?> 
<span class="resultbutton" id="prevresults">Prev 10</span>
<span class="resultbutton" id="nextresults">Next 10</span>
<span class="resultbutton"><a href="search.php">Modify Search</a></span>
</div>

<div id="resultscontainer">
<?php 


if ($proceed == true) {
	
	if ($hits > 0) {
			
		foreach ($content_array	as $content) {
			echo $content;	
		}

	}
	else {
		echo "no results!";
	}

}

?>
</div>

</div>
</body>
</html>//////
