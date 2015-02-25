<?php session_start(); 

if ($_SESSION["pre"] == false) { unset($_SESSION["q"],$_SESSION["id_array"],$_SESSION["allq"], $_SESSION["appends"]); }

//echo $_SESSION["qdata"]."<br /><br />";
if (isset($_GET["r"])) { unset($_SESSION["qdata"],$_SESSION["pre"],$_SESSION["q"]); header("location:search.php"); }

include("dbheader.php"); 

$pubcityq = "select DISTINCT `PubCity` from `_dighum_music_citation` order by `PubCity` asc";
$pubcityr = $mysqli->query($pubcityq) or die (mysqli_error($mysqli));

$pubcolonyq = "select DISTINCT `PubColony` from `_dighum_music_citation` order by `PubColony` asc";
$pubcolonyr = $mysqli->query($pubcolonyq) or die (mysqli_error($mysqli));

$yearq = "select DISTINCT `Year` from `_dighum_music_citation` order by `Year` asc";
$yearr = $mysqli->query($yearq) or die (mysqli_error($mysqli));

$locationq = "select DISTINCT `Location` from `_dighum_music_citation` order by `Location` asc";
$locationr = $mysqli->query($locationq) or die (mysqli_error($mysqli));

$locationcq = "select DISTINCT `LocationCodes` from `_dighum_music_citation` order by `LocationCodes` asc";
$locationcr = $mysqli->query($locationcq) or die (mysqli_error($mysqli));

$pubtitlesq = "select `Gen_ttl`, `BiblioTitle_ID`, `ID` from `_dighum_music_bibliotitle` order by `Gen_ttl` asc";
$pubtitlesr = $mysqli->query($pubtitlesq) or die (mysqli_error($mysqli));

$pubcities = array();
$pubcolonies = array();
$years = array();
$locations = array();
$locationcodes = array();
$pubtitles = array();
$pubcodes = array();

while ($pc = mysqli_fetch_array($pubcityr)) { $pubcities[] = "'".addslashes($pc[0])."'"; }
while ($pc = mysqli_fetch_array($pubcolonyr)) { $pubcolonies[] = "'".addslashes($pc[0])."'"; }
while ($pc = mysqli_fetch_array($yearr)) { $years[] = $pc[0]; }
while ($pc = mysqli_fetch_array($locationr)) { $locations[] = "'".addslashes($pc[0])."'"; }
while ($pc = mysqli_fetch_array($locationcr)) { $locationcodes[] = $pc[0]; }
while ($pc = mysqli_fetch_array($pubtitlesr)) { $pubtitles[] = "'".addslashes($pc[0])."'"; $pubcodes[] = "'".$pc[1]."'"; }

if ($_SESSION["pre"] == true) {

	$sectionchunks = explode(")) AND ((",$_SESSION["q"]);
	$areas = array();
	$functioncalls = array();
	for ($i=0;$i<count($sectionchunks);$i++) {
		$temp = $sectionchunks[$i];
		# clean up all the extra SQL formatting
		# collapse the various parens
		$temp = str_replace("((","",$temp);
		$temp = str_replace(")"," ",$temp);
		$temp = str_replace("("," ",$temp);
		# strip out the quotation marks and wildcards
		$temp = str_replace("`","",$temp);
		$temp = str_replace("%","",$temp);
		#$temp = str_replace("'","",$temp);
		# now look for AND / OR clauses in case there is a chain of terms
		if (strstr($temp,"AND")) { $split = " AND "; }
		if (strstr($temp,"OR")) { $split = " OR "; }
		$clauses = explode($split,$temp);
		for ($j=0;$j<count($clauses);$j++) {
			if (($j+1) == count($clauses)) {
				$areas[] = trim($clauses[$j]);
			}
			else {
				$areas[] = trim($clauses[$j].$split);
			}
		}
		
	}
	$serializedTags = array();
	$contentTagCount = 0;
	$pubColonyTagCount = 0;
	$locationTagCount = 0;
	$titleTagCount = 0;
	$pubCityTagCount = 0;
	$yearTagCount = 0;
	
	for ($i=0;$i<count($areas);$i++) {
		
		#echo $areas[$i]."<br />";
		
		# possible lead values are: Content, Year, PubColony, PubCity, Location, Title
		# Year is a special case...
		if (strpos($areas[$i],"Year")) {
			# ignore for now
			$field = "Year";
			
			if (strpos($areas[$i]," >= ")) { $mod = "#y1"; $match = " >= "; }
			if (strpos($areas[$i]," <= ")) { $mod = "#y2"; $match = " <= "; }
			if (strpos($areas[$i]," = ")) { $mod = "#y1"; $mod2 = "#y2"; $match = " = "; }
			list($field,$value_and_or) = explode($match,$areas[$i]);
		}
		else {
			if (strpos($areas[$i]," NOT LIKE ")) { $match = " NOT LIKE "; } else { $match = " LIKE "; }
		}
		# get field, and value – including AND / OR modifier in the event of a chain
		list($field,$value_and_or) = explode($match,$areas[$i]);
		# determine if there is an AND / OR value
		$bool = "AND"; 
		if (strpos($value_and_or,"OR")) { $bool = "OR"; }
		# strip the boolean variable to just get the value
		$value = rtrim($value_and_or," ".$bool); # space added because there is one separating the value and the boolean modifier in the string
	
		/*
		
		Once we've got the SQL stripped down to the essential information,
		we need to convert it into the "serialized" version that is unpacked 
		by helper functions in support.js
		
		These functions rebuild the UI so that it reflects the saved SQL string.
		
		Each tag type has slightly different requirements, so we have to figure 
		out which one we're working with and work accordingly.
		
		*/
		switch ($field) {
		
			case "Content": 
			# sample serialized Content tag: Content)|(0)|(all)|(AND)|(LIKE)|(bells)|(*
			# unpacked by decodeSavedSearchTags() in "support.js"
			
			# this little bit that follows is a bit redundant but I want to show how it lines up
			# with the javascript object that represents a search tag in the system.
			/*
			tempTag.id = "...";
			tempTag.index = i;
			tempTag.data_content = $(this).attr("data-content");
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
			*/
			$tag_id = $field;
			$tag_index = $contentTagCount;
			$tag_datalike = $match;
			$tag_dataandor = $bool;
			$tag_datavalue = $value;
			$phrase = strpos($tag_datavalue," "); # if false this is just a word
			if ($phrase) {
				$tag_datacontent = "exact";
				if ($match == " NOT LIKE ") { $tag_datacontent = "exclude"; }
			}
			else {
				$tag_datacontent = "all";
				if ($bool == "OR") { $tag_datacontent = "any"; }
				if ($match == " NOT LIKE ") { $tag_datacontent = "none"; }
			}	
			$st = $field.")|(".$contentTagCount.")|(".$tag_datacontent.")|(".$tag_dataandor.")|(".trim($tag_datalike).")|(".trim(str_replace("'","",$tag_datavalue)).")|(*"; 
			$serializedTags[] = $st;
			$contentTagCount++;
			break;
			
			case "PubColony":
			# sample serialzied PubColony tag: PubColony)|(0)|(*)|(OR)|(LIKE)|(MA)|(*
			# unpacked by decodeSavedSearchTags() in "support.js"
			
			$tag_id = $field;
			$tag_index = $pubcolonyTagCount;
			$tag_datalike = $match;
			$tag_dataandor = $bool;
			$tag_datavalue = $value;
			$st = $field.")|(".$pubcolonyTagCount.")|(*)|(".$tag_dataandor.")|(".trim($tag_datalike).")|(".trim(str_replace("'","",$tag_datavalue)).")|(*"; 
			$serializedTags[] = $st;
			$pubColonyTagCount++;
			break;
			
			case "LocationCodes":
			# sample serialzied LocationCodes tag: LocationCodes)|(1)|(*)|(AND)|( NOT LIKE )|(nbn)|(New England
			# unpacked by decodeSavedSearchTags() in "support.js"
			
			$locationIndexes = array(
			"n" => "North America",
			"nbn" => "New England",
			"nbm" => "NY NJ PA DE",
			"nbs" => "MD VA NC SC GA",
			"nb" => "British NA-unspecified",
			"ns" => "Spanish North America",
			"ifs" => "French North America-southern",
			"b" => "England",
			"c" => "Carribean",
			"%c" => "Canada",
			"nbc" => "English Canada",
			"nfc" => "French Canada",
			"e" => "Europe",
			"af" => "Africa",
			"sa" => "South America",
			"a" => "Asia and Pacific",
			"me" => "Near or Middle East",
			"o" => "Other",
			"sea" => "On ship at sea",
			"u" => "Unknown"
			);
			
			$mod = "";
			$tag_id = $field;
			$tag_index = $pubcolonyTagCount;
			$tag_datalike = $match;
			$tag_dataandor = $bool;
			$tag_datavalue = trim(str_replace("'","",$value)); 
			$fullLocationName = $locationIndexes[$tag_datavalue];
			if ($tag_datavalue == "n") { $tag_datavalue = "n%"; }
			# this is going to cause problems later because the carribean is also "c" and rich wanted a one-click solution for canada...
			if ($tag_tatavalue == "c") { $tag_datavalue = "%c"; } 
			$st = $field.")|(".$locationTagCount.")|(*)|(".$tag_dataandor.")|(".trim($tag_datalike).")|(".$tag_datavalue.")|(".$fullLocationName; 
			$serializedTags[] = $st;
			$locationTagCount++;
			break;
			
			case "Title":
			$titleTagCount++;
			break;
			
			case "PubCity":  
			$pubCityTagCount++;
			break;
			
			case "Year":
			# sample serialzied Year tag: 
			# range: Year)|(0)|(*)|(OR)|(LIKE)|(1705-1719)|(*
			# equals: Year)|(0)|(*)|(AND)|(LIKE)|(1705-1705)|(*
			# less than: Year)|(1)|(*)|(AND)|(LIKE)|(-1730)|(*
			# greater than: Year)|(0)|(*)|(AND)|(LIKE)|(1714-)|(*
			# unpacked by decodeSavedSearchTags() in "support.js"
			$yearTagCount++;
			break;
			
		}
		
	} # end for ($i=0;$i<count($areas);$i++)
	
	foreach ($serializedTags as $st) {
		#echo $st."<br />";
	}
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Search Citation</title>
<link href="search-citation.css" rel="stylesheet" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<script language="javascript" src="musicSearch.js" type="text/javascript"></script>

<script>
$(document).keyup(function(e) {
	if (e.keyCode == 27) {
		$("#PubColonyMatches").fadeOut(125);
		$("#PubCityMatches").fadeOut(125);
		$("#TitleMatches").fadeOut(125);
		$("#LocationMatches").fadeOut(125);
	} 
	
});

$(document).ready(function(e) {
	
	$("#save_search").click(function(e) {
		e.preventDefault();
		$(this).blur();
		saveMapacaFile();
	});
	
	$("#load_search").click(function(e) { $('#loadsearch').show(); });
	
	$("#home").click(function(e) {
        document.location.href="index.php";
    });
	
	$("#q_search").click(function(e) {
		
		$("#q_search").blur();
        if (generateTags(0)) {  
		document.forms[0].submit(); 
		} else {return false; }
    });
	
	$("#seeSQL").click(function(e) {
        generateTags(2);
		$("#seeSQL").blur();
		return false;
    });
	
	<?php 
	if (isset($_SESSION["pre"])) {
		$_SESSION["qdata"] = implode("[|]",$serializedTags);
	}
	?> 
	
	<?php if (isset($_SESSION["qdata"])): ?>
	
	/* we have a set of saved search tags */
	musicSearch.allSearchTags = "<?php echo $_SESSION["qdata"]; ?>".split("[|]");
	decodeSavedSearchTags();
	
	<?php else: ?>
	
	musicSearch.allSearchTags = new Array();
	
	<?php endif; ?>
	
	/* radio button bindings for LocationCodes*/
	$(".radiochoice").click(function() { addFilter(this.id, $("#"+this.id).attr("value")); this.blur(); });
	
	$("#helpbutton").click(function(e) {
        document.location.href="help.php";
    });
	
	/* year selection bindings */
	$("#Year").click(function() { addFilter(this.id, ""); this.blur(); });
	$("#y1").change(function () { $("#y2").val(($(this).val())); this.blur(); } );
	
	/* intitalizations and bindings for the Content field */
	$("#ContentAdd").click(function() { addFilter("Content", $("#Content").val()); this.blur(); });
	
	/* position the div that displays possible publication location matches */
	$("#LocationMatches").css( { left: $("#referenceLocationBox").outerWidth()-10 } );
	$("#LocationMatches").css( { minHeight: $("#referenceLocationBox").outerHeight()-10 } );
	
	/* position the div that displays possible publication title matches */
	$("#TitleMatches").css( { left: $("#publicationTitleBox").outerWidth()-10 } );
	$("#TitleMatches").css( { minHeight: $("#publicationTitleBox").outerHeight()-10 } );
	
	/* position the div that displays possible publication colony matches */
	$("#PubColonyMatches").css( { left: $("#publicationColonyBox").outerWidth()-10 } );
	$("#PubColonyMatches").css( { minHeight: $("#publicationColonyBox").outerHeight()-10 } );
	
	/* position the div that displays possible publication city matches */
	$("#PubCityMatches").css( { left: $("#publicationCityBox").outerWidth()-10 } );
	$("#PubCityMatches").css( { minHeight: $("#publicationCityBox").outerHeight()-10 } );
	
	/* 
	arrays that are used for type-anticipation events
	these properties are defined in "musicSearch.js" but initialized here 
	*/
	musicSearch.possibleLocation = new Array(<?php echo implode(", ",$locations); ?>);
	musicSearch.possibleTitle = new Array(<?php echo implode(", ",$pubtitles); ?>);
	musicSearch.publicationCodes = new Array(<?php echo implode(", ",$pubcodes); ?>);
	musicSearch.possiblePubColony = new Array(<?php echo implode(", ",$pubcolonies); ?>);
	musicSearch.possiblePubCity = new Array(<?php echo implode(", ",$pubcities); ?>);
	
	/* musicSearch.PubColonyNames is defined in "musicSearch.js" */
	for (key in musicSearch.PubColonyNames) { musicSearch.possiblePubColony.push(musicSearch.PubColonyNames[key]); }
 	
	/* key-based events for various text fields */
	$("#Location").keyup( function() { guessText("Location"); });
	$("#Title").keyup (function() { guessText("Title"); });
	$("#PubColony").keyup (function() { guessText("PubColony"); });
	$("#PubCity").keyup (function() { guessText("PubCity"); });
	
	
	$("#PubColonyAdd").click(function(e) { addFilter("PubColony", this.value, -1); this.blur(); });
	$("#PubCityAdd").click(function(e) { addFilter("PubCity", this.value, -1); this.blur(); });
	$("#PubTitleAdd").click(function(e) { addFilter("Title", this.value, -1); this.blur(); });
	$("#LocationAdd").click(function(e) { setText(-1, "Location", -1); this.blur(); });
	
	$('.textinput').each(function(i, obj) {
		$(this).keypress(function(e) {
            if (e.keyCode==13) { 
				if (generateTags(false)) {  
				$("#searchnow").submit(); 
				} else {return false; }
			}
        });
	});
	
	$('[class*="ignoretab"]').each(function(i, obj) {
		$(this).attr("tabindex","-1");
	});
	
});

function saveMapacaFile() {
	generateTags(1);
	if (musicSearch.allSearchTags.length > 0) {
		foo = musicSearch.allSearchTags.join("[|]");
		document.location.href="qsave.php?alltags="+foo;
		$('#searchprompt').show();
		setTimeout(function(){ $('#searchprompt').hide(); },5000);
	}
	else {
		alert("No search criteria established.");	
	}
}

</script>

<script language="javascript" src="support.js" type="text/javascript"></script>

<style>

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

#publication_location {
	display: block; 
	/*background-color:#f4d7b7;*/
	float: left;
	
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	border: 1px solid #e15c11;
}

#reference_location {
	
	width: 49%; 
	display: block; 
	/*background-color:#f4d7b7;*/
	float: left;
	
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	
	border: 1px solid #e15c11;
	margin-left:10px;
}

#searchTagchain {
	width: 420px;padding: 10px; display: block;
}

.instructions {
	border-top: 1px solid #e15c11;padding-top:15px;margin: 10px;
}

#publicationColonyBox, #publicationCityBox, #publicationTitleBox, #referenceLocationBox {
	width: auto;
}

#searchprompt { display: none; }

</style>
</head>

<body>

<div class="container">

<div>
<h1 style="float: left;"><span id="home">MAPACA</span> <span class="creamcopy" style="font-size: 0.5em;">Music and the Performing Arts in Colonial America</span></h1><button class="dashbutton" style="float: right;" id="helpbutton">HELP</button>
<div style="clear: both;"></div>
</div>

<div style="width: 49%;display:block;float:left;">
<div id="publication_location">


<div id="publicationContentBox"><strong>Content:</strong>
<select name="ContentSort" id="ContentSort">
<option value="all">ALL OF THESE WORDS</option>
<option value="any">ANY OF THESE WORDS</option>
<option value="none">NONE OF THESE WORDS</option>
<option value="exact">EXACTLY THIS PHRASE</option>
<option value="exclude">EXCLUDE THIS PHRASE</option>
</select>
<br />
<input type="text" class="textinput" name="Content" id="Content" value="" style="width: 320px"/>
<button id="ContentAdd" name="ContentAdd" class="savebutton ignoretab">+</button>
<div style="clear:both;"></div>
<div id="ContentTags">
</div>
</div>
<div style="clear:both;"></div>


<div id="publicationYearBox"><strong>Year:</strong>
<select name="y1" id="y1"><option value='%%'>ANY</option><?php for ($i=0;$i<count($years);$i++): ?><option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option><?php endfor; ?></select>&nbsp;&mdash;&nbsp;<select name="y2" id="y2"><option value='%%'>ANY</option><?php for ($i=0;$i<count($years);$i++): ?><option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option><?php endfor; ?></select>&nbsp;<button id="Year" name="Year" class="savebutton ignoretab">+</button>
<div id="YearTags">
</div>
</div>
<div style="clear:both;"></div>

<div id="publicationColonyBox"><strong>Publication Colony: </strong><br />
<input data-code="" class="textinput" type="text" name="PubColony" id="PubColony" value="" />&nbsp;<button id="PubColonyAdd" name="PubColonyAdd" class="savebutton ignoretab">+</button><div class="varMatches" id="PubColonyMatches"></div>
<div id="PubColonyTags">
</div>
</div>

<div style="clear:both;"></div>


<div id="publicationCityBox"><strong>Publication City: </strong><br />
<input data-code="" class="textinput" type="text" name="PubCity" id="PubCity" value="" />&nbsp;<button id="PubCityAdd" name="PubCityAdd" class="savebutton ignoretab">+</button><div class="varMatches" id="PubCityMatches"></div>
<div id="PubCityTags">
</div>
</div>
<div style="clear:both;"></div>

<div id="publicationTitleBox"><strong>Publication Title: </strong><br />
<input data-code="" class="textinput" type="text" name="Title" id="Title" value="" />&nbsp;<button id="PubTitleAdd" name="PubTitleAdd" class="savebutton ignoretab">+</button><div class="varMatches" id="TitleMatches"></div>
<div id="TitleTags">
</div>


</div>
  
<div style="clear:both;"></div>

<p class="instructions">This panel specifies filters based on where the citation was published, e.g. Hartford, CT</p>

</div>

<div style="clear:both;"></div>
<div id="searchTagchain">
<form name="searchnow" id="searchnow" enctype="multipart/form-data" action="results.php" onsubmit="return submitSearch();" method="post" >
<button id="seeSQL" class="dashbutton"> SEE SQL </button>&nbsp;<button id="q_search" class="dashbutton"> SEARCH </button>
<button id="reset_search" class="dashbutton" onclick="document.location.href='search.php?r';return false;">RESET</button>
<button id="save_search" class="dashbutton" title="Download a file containing the current search configuration.">SAVE</button>
<button id="load_search" class="dashbutton" title="Upload a previously-saved search configuration file." onclick="$('#load_search').show();">LOAD</button>
<input type="hidden" id="q" name="q" value="duh" />
<input type="hidden" id="qdata" name="qdata" value="duh" />
</form>
<form name="loadsearch" id="loadsearch" enctype="multipart/form-data" action="qload.php" method="post" style="display: none;">
<input type="file" name="mapacafile" id="mapacafile" /><button class="savebutton" name="continueUL" id="continueUL" onclick="$('#loadsearch').submit();" >CONTINUE</button>&nbsp;&nbsp;&nbsp;<button class="savebutton" name="cancelUL" id="cancelUL" onclick="$('#loadsearch').submit();" >CANCEL</button>
</form>
<div id="searchprompt">.mapaca file downloaded. Use LOAD to restore search.</div>
</div>
</div>
<div id="reference_location">

<div id="referenceLocationCodesBox">
<strong>Referred Region:</strong><br />
<div>
<button class="savebutton radiochoice" name="LocationCode1" id="LocationCode1" data-name="North America" value="n%" />+</button> North America: <a id="monaon" href="#" onclick="$('#monorthamerica').show();$(this).hide();$('#monaoff').show();return false;" style="text-decoration: none;">&#9658;</a><a id="monaoff" href="#" onclick="$('#monorthamerica').hide();$(this).hide();$('#monaon').show();return false;" style="color:#A4B0C1;text-decoration: none;display: none;">&#9660;</a>
<div id="monorthamerica" style="display: none;">

&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode2" id="LocationCode2" data-name="New England" value="nbn" />+</button> New England<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode3" id="LocationCode3" data-name="NY NJ PA DE" value="nbm" />+</button> NY NJ PA DE<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode4" id="LocationCode4" data-name="MD VA NC SC GA" value="nbs" />+</button> MD VA NC SC GA<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode5" id="LocationCode5" data-name="British NA-unspecified" value="nb" />+</button> British NA-unspecified<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode6" id="LocationCode6" data-name="Spanish North America" value="ns" />+</button> Spanish North America<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode7" id="LocationCode7" data-name="French North America-southern" value="ifs" />+</button> French North America-southern
</div>
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode8" id="LocationCode8" data-name="England" value="b" />+</button> England
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode9" id="LocationCode9" data-name="Carribean" value="c" />+</button> Carribean
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode10" id="LocationCode10" data-name="Canada" value="%c" />+</button> Canada: <a id="mocanon" href="#" onclick="$('#mocanada').show();$(this).hide();$('#mocanoff').show();return false;" style="text-decoration: none;">&#9658;</a><a id="mocanoff" href="#" onclick="$('#mocanada').hide();$(this).hide();$('#mocanon').show();return false;" style="color:#A4B0C1; text-decoration: none;display: none;">&#9660;</a>
<div id="mocanada" style="display: none;">
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode11" id="LocationCode11" data-name="English Canada" value="nbc" />+</button> English Canada<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode12" id="LocationCode12" data-name="French Canada" value="nfc" />+</button> French Canada
</div>
</div>
<div><button class="savebutton radiochoice" name="LocationCode13" id="LocationCode13" data-name="Europe" value="e" />+</button> Europe</div>
<div><button class="savebutton radiochoice" name="LocationCode14" id="LocationCode14" data-name="Africa" value="af" />+</button> Africa</div>
<div><button class="savebutton radiochoice" name="LocationCode15" id="LocationCode15" data-name="South America" value="sa" />+</button> South America</div>
<div><button class="savebutton radiochoice" name="LocationCode16" id="LocationCode16" data-name="Asia and Pacific" value="a" />+</button> Asia and Pacific</div>
<div><button class="savebutton radiochoice" name="LocationCode17" id="LocationCode17" data-name="Near or Middle East" value="me" />+</button> Near or Middle East</div>
<div><button class="savebutton radiochoice" name="LocationCode18" id="LocationCode18" data-name="Other" value="o" />+</button> Other</div>
<div><button class="savebutton radiochoice" name="LocationCode19" id="LocationCode19" data-name="On ship at sea" value="sea" />+</button> On ship at sea</div>
<div><button class="savebutton radiochoice" name="LocationCode20" id="LocationCode20" data-name="unknown or unintended" value="u" />+</button> unknown or unintended</div>

<div id="LocationCodesTags">
</div>

</div>


<div style="clear:both;"></div>
<div id="referenceLocationBox" style="margin:5px;"><strong>Referred Location: </strong>
  <br />
  <input class="textinput" type="text" name="Location" id="Location" value="" />&nbsp;<button id="LocationAdd" name="LocationAdd" class="savebutton ignoretab">+</button><div class="varMatches" id="LocationMatches"></div>
  <div id="LocationTags">
</div>
</div>
  
<div style="clear:both;"></div>
<p class="instructions">This panel specifies filters based on what region the citation is referencing, e.g. New England</p>

</div>

<div style="clear:both;"></div><br />

<div id="thequery">
</div>
</div>


<div style="clear:both;"></div>


<div class='tag'>
  <div class='tagend'><button onclick='removeFilter(this.ID);'>x</button><select onchange='toggleANDOR(1);' class='minimenu'><option value=' AND '>AND</option><option value=' OR '>OR</option></select></div> 
<div class='taghead'><select class='minimenu' onchange='toggleLIKE()'><option value='q_in'>{..}</option><option value='q_notin'>! {..}</option></select></div>
<div class='tagbody'>
<div id='searchfield' class='tagtop'>hello</div>
<div id='searchvalue' class='tagbottom'>goodbye</div>
</div>
</div>
<? /**/ ?>
</div>

</body>
</html>