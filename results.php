<?php session_start();

include("dbheader.php"); 

$proceed = false;

if (isset($_SESSION["q"])) {
	$_SESSION["id_array"] = array();
	$selectfields = array();
	$orderfields = array();
	if (strlen($_POST["appends"]) > 0) {
		# we have a search	
		$_SESSION["appends"] = "";
		list($fields,$order) = explode("=",$_POST["appends"]);
		$idq = "select `ID` , ".$fields." from `_dighum_music_citation` where " . $_SESSION["q"]. " order by ".$fields." ".$order;
		$_SESSION["appends"] = $_POST["appends"];
		$proceed = true;
	}
	else {
		$idq = "select `ID` from `_dighum_music_citation` where ".$_SESSION["q"]; # $matches[1] contains the citation search terms 
		$regex = "/`Content` LIKE '%(.*?)%'/i";
		preg_match_all($regex, $_SESSION["q"], $matches, PREG_OFFSET_CAPTURE);
		$proceed = true;
		$_SESSION["appends"] = "";
	}
	$_SESSION["allq"] = "select * from `_dighum_music_citation` where ".$_SESSION["q"]." AND `ID` = [theid]" ;
	
}

if (isset($_POST["q"]) && (strlen($_POST["q"]) > 0)) {

	if (isset($_SESSION["qdata"])) { unset($_SESSION["qdata"]); }
	
	$_SESSION["qdata"] = $_POST["qdata"];
	
	$_SESSION["id_array"] = array();
	$content_array = array();
	
	$_SESSION["q"] = filter_var(htmlspecialchars_decode(urldecode($_POST["q"])));	
	
	//echo $_SESSION["q"];
	
	$idq = "select `ID` from `_dighum_music_citation` where ".$_SESSION["q"];
	$_SESSION["allq"] = "select * from `_dighum_music_citation` where ".$_SESSION["q"]." AND `ID` = [theid]";
	
	/* $matches[1] contains the citation search terms */
	$regex = "/`Content` LIKE '%(.*?)%'/i";
	preg_match_all($regex, $_SESSION["q"], $matches, PREG_OFFSET_CAPTURE);
	
	$proceed = true;
}

if ($proceed == true) {
	
	#echo $idq."<br />";
	
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
			
			/* outtake for now: */
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
searchResults.hits = <?php echo ($hits > 0)?$hits:0; ?>;
searchResults.csvmade = false;
searchResults.csv = "";
searchResults.appends = "<?php echo $_SESSION["appends"]; ?>";

// *** for the animated dots */
var dotTimer = null;
var dotCount = 0;
var dotz = new Array(" ", ".", ". .", ". . .");

function doDots() {
	dotCount++;
	if (dotCount > 3) { dotCount = 0; }
	$(".dots").each(function(index, element) {
        $(this).html(dotz[dotCount]);
    });
}

function ajaxit() {
	searchResults.csvmade = true;
	$("#csvbutton").html("Processing");
	data = "";
	jQuery.ajax({
	   type: "GET",
	   url: "csv2.php",
	   data: data,
	   success: function(msg) { 
			if (msg == "0") { 
				alert("AJAX error. Not your fault, please try again later."); 
			}
			else {
				$("#csvbutton").html("Download CSV");
				searchResults.csv = msg;
			}
	   },
	   error: function(msg) { alert("Server error. Not your fault, please try again later."); }
	 });
}

function downloadit() {
	if (searchResults.csv == "") {
		/* ignore */
	}
	else {
		window.location.href = searchResults.csv;
		$("#csvbutton").html("CSV EXPORT");
		searchResults.csvmade = false;
		searchResults.csv = "";
	}
}

$(document).ready(function(e) {
	
	$("#nextresults1").hide();
	$("#nextresults2").hide();
	$("#home").click(function(e) {
        document.location.href="index.php";
    });
	
	$("#modifysearch").click(function(e) {
        document.location.href="search.php";
    });
	
	$("#newsearch").click(function(e) {
        document.location.href="search.php?r";
    });
	
	$("#helpbutton").click(function(e) {
        document.location.href="help.php";
    });
	
	$("#csvbutton").click(function(e) {
		$(this).blur();
		if (searchResults.csvmade == false) {
			ajaxit();
		}
		else {
			downloadit();
		}
	});
	
	if ($("#result0").length > 0) {
		
		$("#result0").show();
		
		$("#prevresults1").hide();
		$("#prevresults2").hide();
		$("#prevresults1").click(function() { $(this).blur(); offset(-10,0); });
		$("#prevresults2").click(function() { $(this).blur(); offset(-10,0); $('html, body').animate({ scrollTop: 0 }, 0); });
		
		if (searchResults.hits > 10) {
			$("#nextresults1").show();
			$("#nextresults2").show();
			$("#start1").html("1");
			$("#start2").html("1");
		}
		if (searchResults.hits > 0) {
			$("#start1").html("1");
			$("#start2").html("1");
		}
		
		$("#nextresults1").click(function() { $(this).blur(); offset(10,0); });
		$("#nextresults2").click(function() { $(this).blur(); offset(10,0); $('html, body').animate({ scrollTop: 0 }, 0); });
		/*
		$("#sortreset1").click(function() { $("#appends").val(""); $("#appendstuff").submit(); });
		$("#sortreset2").click(function() { $("#appends").val(""); $("#appendstuff").submit(); });
		*/
		
		for (i=0;i<10;i++) {
			$("#offset"+(i+searchResults.offset)).click(function() { showResult(i+searchResults.offset); });
		}
		
		/* select * from `_dighum_music_citation` where ( `PubColony` LIKE '%NH%' ) AND ( `Content` LIKE '%fiddle%' ) order by `Month`, `Year`, `Day` 


/* Sort by day 
select * from `_dighum_music_citation` where ( `Content` LIKE '%fiddle%' ) order by `Day`, `Year`, `Month` */

/* Sort by year 
select * from `_dighum_music_citation` where ( `Content` LIKE '%fiddle%' ) order by `Year`, `Month`, `Day` */

/* Sort by Month 
select * from `_dighum_music_citation` where ( `Content` LIKE '%fiddle%' ) order by `Month`, `Year`, `Day`*/

		setSortArrows();
	}
	else {
		
		$("#prevresults1").hide();
		$("#prevresults1").hide();
		$("#nextresults2").hide();
		$("#nextresults2").hide();
		$("#start1").html("0");
		$("#start2").html("0");
	}
	
	$("#gotorecord1").keyup(function(e) {
		if (e.keyCode == 13) {
			gotoRecord(1);
		} 
	});
	
	$("#gotorecord2").keyup(function(e) {
		if (e.keyCode == 13) {
			gotoRecord(2);
		} 
	});
	
	$(".gotorecord").each(function(index, element) {
		$(this).keyup(function(e) {
			if ($(this).val().length > 0) {
				var os = parseInt($(this).val()) - 1;
				if ((os > 0) && (os <= searchResults.hits)) {
					searchResults.offset = parseInt($(this).val()) - 1;
					$(".status").each(function(index, element) { $(this).html(""); });
				}
				else {
					$(".status").each(function(index, element) { $(this).html("Enter integer > 0 and <= "+searchResults.hits); });
				}
			}
			else {
				$(".status").each(function(index, element) { $(this).html(""); });
			}
		});
	});

	
});

function appendSort(field, direction) {
	if (direction == "ASC") { opp = "DESC"; } else { opp = "ASC"; }
	
	if ((searchResults.appends.length == 0) || (searchResults.appends == field+"="+opp)) {
		searchResults.appends = field+"="+direction;
	}
	else if (searchResults.appends.indexOf(field+"="+direction+"&") > -1) {
		// do nothing, current sort flag exists in string
	}
	else {
		if (searchResults.appends.indexOf(field+"="+opp+"&") > -1) {
			searchResults.appends = searchResults.appends.replace(field+"="+opp+"&","");
		}
		else if (searchResults.appends.indexOf("&"+field+"="+opp) > -1) {
			searchResults.appends = searchResults.appends.replace("&"+field+"="+opp,"");
		}
		searchResults.appends += "&"+field+"="+direction;
	}
	
	$("#appends").val(searchResults.appends);
	$("#appendstuff").submit();
}

function showResult(id) {
	$("#result"+searchResults.showing).hide();
	$("#result"+id).show();
	searchResults.showing = id;
}

function offset(amount,gotorecord) {
	
	$(".status").each(function(index, element) { $(this).html("PROCESSING"); });
	dotTimer = setInterval(function(){doDots();},500);
		
	if (gotorecord == 0) {
		
		if (amount > 0) {	
			searchResults.offset += 10;
		}
		else {	
			searchResults.offset -= 10;
			if (searchResults.offset < 0) {
				searchResults.offset = 0;
			}
		}
	}
	else {
		$("#gotorecord1").val("");
		$("#gotorecord2").val("");
	}
	
	data = "offset="+searchResults.offset;
	
	/* load up the next or previous ten contents via ajax */
	jQuery.ajax({
	   type: "GET",
	   url: "search-offset.php",
	   data: data
	}).always(function() {
		clearInterval(dotTimer);
		$(".status").each(function(index, element) { $(this).html(""); });
		$(".dots").each(function(index, element) { $(this).html(""); });
	}).done(function(msg) { 
	  if (msg == "0") { 
		  alert("AJAX error. Not your fault, please try again later."); 
	  }
	  else {
		  $('html, body').animate({ scrollTop: 0 }, 0);
		  $("#resultscontainer").html("");
		  divs = msg.split("[&]");
		  for (i=0;i<divs.length;i++) {
			  $("#resultscontainer").html($("#resultscontainer").html()+divs[i]);
		  }
		  setSortArrows();
		  if (searchResults.offset > 9) {
			  $("#prevresults1").show();
			  $("#prevresults2").show();
		  }
		  if (searchResults.offset == 0) {
			  $("#prevresults1").hide();
			  $("#prevresults2").hide();
		  }
		  
		  $("#start1").html(searchResults.offset+1);
		  
		  if (gotorecord != 0) {
			  $("#gotorecord1").val("");
			  $("#gotorecord2").val("");
		  }
		  
		  if ((searchResults.offset + 10) > searchResults.hits) {
			  $("#end1").html(searchResults.hits);
			  $("#nextresults1").hide();
		  }
		  else {
			  $("#end1").html(searchResults.offset+10);
			  $("#nextresults1").show();
		  }
		  
		  $("#start2").html(searchResults.offset+1);
		  
		  if ((searchResults.offset + 10) > searchResults.hits) {
			  $("#end2").html(searchResults.hits);
			  $("#nextresults2").hide();
		  }
		  else {
			  if (gotorecord == 0) {
				  $("#end2").html(searchResults.offset+10);
			  }
			  else {
				  $("#end2").html(searchResults.offset+11);
			  }
			  
			  $("#nextresults2").show();
		  }
	  }
 
	}).fail(function(msg) {
		alert("Server error. Not your fault, please try again later.");
	});
		 
}

function gotoRecord(index) { 
	var os = parseInt($("#gotorecord"+index).val()) - 1
	if ((os > 0) && (os <= searchResults.hits)) {
		searchResults.offset = parseInt($("#gotorecord"+index).val()) - 1;
		offset(searchResults.offset,1);
	}
	else {
		$(".status").each(function(index, element) { $(this).html("Enter integer > 0 and <= "+searchResults.hits); });
	}
}

function setSortArrows() {
	
		$(".yearup").click(function() { appendSort('`Year`,`Month`,`Day`','DESC'); });
		$(".yeardn").click(function() { appendSort('`Year`,`Month`,`Day`','ASC'); });
		if (searchResults.appends.indexOf("Year=ASC") < 4) { $(".yeardn").css("color", "#82d2ed"); }
		if (searchResults.appends.indexOf("Year=DESC") < 4) { $(".yearup").css("color", "#82d2ed"); }
		
		$(".monthup").click(function() { appendSort('`Month`,`Year`,`Day`','DESC'); });
		$(".monthdn").click(function() { appendSort('`Month`,`Year`,`Day`','ASC'); });
		if (searchResults.appends.indexOf("Month=ASC") < 4) { $(".monthdn").css("color", "#82d2ed"); }
		if (searchResults.appends.indexOf("Month=DESC") < 4) { $(".monthup").css("color", "#82d2ed"); }
		
		$(".dayup").click(function() { appendSort('`Day`,`Month`,`Year`','DESC'); });
		$(".daydn").click(function() { appendSort('`Day`,`Month`,`Year`','ASC'); });
		if (searchResults.appends.indexOf("Day=ASC") < 4) { $(".daydn").css("color", "#82d2ed"); }
		if (searchResults.appends.indexOf("Day=DESC") < 4) { $(".dayup").css("color", "#82d2ed"); }
		
		$(".titleup").click(function() { appendSort('BiblioTitle_ID','DESC'); });
		$(".titledn").click(function() { appendSort('BiblioTitle_ID','ASC'); });
		if (searchResults.appends.indexOf("BiblioTitle_ID=ASC") > -1) { $(".titledn").css("color", "#82d2ed"); }
		if (searchResults.appends.indexOf("BiblioTitle_ID=DESC") > -1) { $(".titleup").css("color", "#82d2ed"); }
		
		$(".pubcityup").click(function() { appendSort('PubCity','DESC'); });
		$(".pubcitydn").click(function() { appendSort('PubCity','ASC'); });	
		if (searchResults.appends.indexOf("PubCity=ASC") > -1) { $(".pubcitydn").css("color", "#82d2ed"); }
		if (searchResults.appends.indexOf("PubCity=DESC") > -1) { $(".pubcityup").css("color", "#82d2ed"); }
		
		$(".pubcolonyup").click(function() { appendSort('PubColony','DESC'); });
		$(".pubcolonydn").click(function() { appendSort('PubColony','ASC'); });
		if (searchResults.appends.indexOf("PubColony=ASC") > -1) { $(".pubcolonydn").css("color", "#82d2ed"); }
		if (searchResults.appends.indexOf("PubColony=DESC") > -1) { $(".pubcolonyup").css("color", "#82d2ed"); }
		
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
	width: 75%;
	margin: auto;
	padding: 5px;
	padding-top: 15px;
	font-family: 'Averia Libre', sans-serif;
	font-size: 0.85em;
	display: block;
}

.resultbutton {
	font-family: 'Averia Libre', serif;
	width: 32px; height: 32px; padding: 3px;
	line-height: 32px;
	border: 1px solid #333;
	cursor: pointer;
	margin: 2px;	
}

.resultbutton:hover {
	background-color:#ccc;
}

.found {
	background-color: #FFF;
	color: #1a1915;
	/*padding-left: 3px; padding-right: 3px;	*/
}

.yearup, .yeardn, .monthup, .monthdn, .dayup, .daydn, .titleup, .titledn, .pubcityup, .pubcitydn, .pubcolonyup, .pubcolonydn, .topYear, .topPubCity, .topPubColony {
	color: #f4d7b7;
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

.citation {
    font-family: 'Crimson', serif;
	font-size: 1.3em;
	/*line-height: 1.5em;*/
	padding: 0px;	
	margin-left: 25px;
	margin-top: 15px;
}

.container {
	
	min-width: 4in;
	max-width: 9in;
	margin: auto;
	margin-top: 25px;
	padding: 15px;
	border: 1px solid #e15c11;
	
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;	
}

.gotorecord {
	background-color: #FFF;
	width: 25px;font-family: 'Averia Libre', serif;
	border: 1px solid #FFF;
	padding: 3px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
}

</style>
</head>

<body>

<div class="container">
<div>
<h1 style="float: left;"><span id="home">MAPACA</span> <span class="creamcopy" style="font-size: 0.5em;">Music and the Performing Arts in Colonial America</span></h1><button class="dashbutton" style="float: right;" id="helpbutton">HELP</button><button class="dashbutton" style="float: right;" id="newsearch">NEW SEARCH</button><button class="dashbutton" style="float: right;" id="modifysearch">MODIFY SEARCH</button><button id="csvbutton" class="dashbutton" style="float: right;" >CSV EXPORT</button>
<div style="clear:both;"></div>
</div>

<div id="navigation1">HITS: <span id="start1"></span> to <span id="end1"><?php echo ($hits < 10)?$hits:"10"; ?></span> of <?php echo $hits; ?> 
<button class="savebutton" id="prevresults1">Prev 10</button>
<button class="savebutton" id="nextresults1">Next 10</button>
<!--<button class="savebutton" id="sortreset1">Reset Sort</button>-->
<button class="savebutton" id="gotoresult1" onclick="gotoRecord(1);">Go to record #:</button>&nbsp;<input type="text" id="gotorecord1" name="gotorecord1" value="" class="gotorecord" /> <span class="status"><span class="dots"></span></span>
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

<div id="navigation2">HITS: <span id="start2"></span> to <span id="end2"><?php echo ($hits < 10)?$hits:"10"; ?></span> of <?php echo $hits; ?> 
<button class="savebutton" id="prevresults2">Prev 10</button>
<button class="savebutton" id="nextresults2">Next 10</button>
<!--<button class="savebutton" id="sortreset2">Reset Sort</button>-->
<button class="savebutton" id="gotoresult2" onclick="gotoRecord(2);">Go to record #:</button>&nbsp;<input type="text" id="gotorecord2" name="gotorecord2" value="" class="gotorecord" /> <span class="status"><span class="dots"></span></span>
</div>

<form name="appendstuff" id="appendstuff" action="results.php" method="post" enctype="multipart/form-data"><input type="hidden" name="appends" id="appends" value="<?php echo $_SESSION["appends"]; ?>" /></form>
</div>
</body>
</html>