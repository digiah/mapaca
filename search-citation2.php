<?php session_start(); 

unset($_SESSION["q"],$_SESSION["id_array"],$_SESSION["allq"]);

if (isset($_GET["r"])) { unset($_SESSION["qdata"]); header("location:search-citation2.php"); }

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

$(document).ready(function(e) {
	
	<?php if (isset($_SESSION["qdata"])): ?>
	
	/* we have a set of saved search tags */
	musicSearch.allSearchTags = "<?php echo $_SESSION["qdata"]; ?>".split("[&]");
	decodeSavedSearchTags();
	
	<?php unset($_SESSION["qdata"]); ?>
	
	<?php else: ?>
	
	musicSearch.allSearchTags = new Array();
	
	<?php endif; ?>
	
	/* radio button bindings for LocationCodes*/
	$(".radiochoice").click(function() { addFilter(this.id, $("#"+this.id).attr("value")); this.blur(); });
	
	/* year selection bindings */
	$("#Year").click(function() { addFilter(this.id, ""); this.blur(); });
	$("#y1").change(function () { $("#y2").val(($(this).val())); this.blur(); } );
	
	/* intitalizations and bindings for the Content field */
	$("#Content").click(function() { if ($(this).val() == "Enter search text and press enter") { $(this).val(""); } });
	$("#ContentAdd").click(function() { addFilter("Content", $("#Content").val()); this.blur(); });
  	$("#Content").keypress(function( event ) { if (event.which==13) { addFilter(this.id, this.value); } });
	
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
  	$("#Location").keypress(function( event ) { if (event.which==13) { addFilter(this.id, this.value); } });
	$("#Title").keyup (function() { guessText("Title"); });
	$("#PubColony").keyup (function() { guessText("PubColony"); });
  	$("#PubColony").keypress(function( event ) { if (event.which==13) { addFilter(this.id, this.value); } });
	$("#PubCity").keyup (function() { guessText("PubCity"); });
  	$("#PubCity").keypress(function( event ) { if (event.which==13) { addFilter(this.id, this.value); } });
	
	/* "SHOW ALL" button bindings to reveal the entire list of possible values */
	$("#PubColonyShow").click(function() { showAll('PubColony'); });
	$("#PubCityShow").click(function() { showAll('PubCity'); });
	$("#TitleShow").click(function() { showAll('Title'); });
	
	/* "SHOW ALL" via double-clicks on the text fields */
	$("#PubColony").dblclick(function() { showAll('PubColony'); });
	$("#PubCity").dblclick(function() { showAll('PubCity'); });
	$("#Title").dblclick(function() { showAll('Title'); });
	
});

</script>

<script language="javascript" src="support.js" type="text/javascript"></script>

</head>

<body>

<h1>Music in the Performing Arts in Colonial American Newspapers</h1>
<div id="column1" style="width: auto;float:left;display:block;">

<div id="publication_location" style="width: 420px; padding: 10px; display: block; background-color:#E6E6E6;float: left;">

<div style="display: block;margin: 10px;"><strong>Specify how to filter based on publication data: include or exclude based on title, colony, publishing year, and citation.</strong></div>

<div id="publicationContentBox"><strong>Content:</strong>
<select name="ContentSort" id="ContentSort">
<option value="all">ALL OF THESE WORDS</option>
<option value="any">ANY OF THESE WORDS</option>
<option value="none">NONE OF THESE WORDS</option>
<option value="exact">EXACTLY THIS PHRASE</option>
<option value="exclude">EXCLUDE THIS PHRASE</option>
</select>
<br />
<input type="text" class="textinput" name="Content" id="Content" value="Enter search text and press enter" style="width: 320px"/>
<button id="ContentAdd" name="ContentAdd" class="savebutton"> ADD </button>
<div style="clear:both;"></div>
<div id="ContentTags">
</div>
</div>
<div style="clear:both;"></div>


<div id="publicationYearBox"><strong>Year:</strong>
<select name="y1" id="y1"><option value='%%'>ANY</option><?php for ($i=0;$i<count($years);$i++): ?><option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option><?php endfor; ?></select>&nbsp;&mdash;&nbsp;<select name="y2" id="y2"><option value='%%'>ANY</option><?php for ($i=0;$i<count($years);$i++): ?><option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option><?php endfor; ?></select>&nbsp;<button id="Year" name="Year" class="savebutton"> ADD </button>
<div id="YearTags">
</div>
</div>
<div style="clear:both;"></div>

<div id="publicationColonyBox"><strong>Publication Colony: </strong><br />
<input data-code="" class="textinput" type="text" name="PubColony" id="PubColony" value="" />&nbsp;<button id="PubColonyShow" class="savebutton"> SHOW ALL </button><div id="PubColonyMatches"></div>
<div id="PubColonyTags">
</div>
</div>

<div style="clear:both;"></div>


<div id="publicationCityBox"><strong>Publication City: </strong><br />
<input data-code="" class="textinput" type="text" name="PubCity" id="PubCity" value="" />&nbsp;<button id="PubCityShow" class="savebutton"> SHOW ALL </button><div id="PubCityMatches"></div>
<div id="PubCityTags">
</div>
</div>
<div style="clear:both;"></div>

<div id="publicationTitleBox"><strong>Publication Title: </strong><br />
<input data-code="" class="textinput" type="text" name="Title" id="Title" value="" />&nbsp;<button id="TitleShow" class="savebutton"> SHOW ALL </button><div id="TitleMatches"></div>
<div id="TitleTags">
</div>
</div>
  
<div style="clear:both;"></div>

</div>

</div>

<div id="reference_location" style="width: 420px;padding: 10px; display: block; background-color:#E6E6E6;float: left;margin-left:10px;">

<div style="display: block;margin: 10px;"><strong>Specify how to filter based on the locations and regions of references in the publication(s).</strong></div>

<div id="referenceLocationCodesBox" style="margin:5px;">
<strong>Referred Region:</strong><br />
<div>
<button class="savebutton radiochoice" name="LocationCode1" id="LocationCode1" data-name="North America" value="n%" />ADD</button> North America: <a id="monaon" href="#" onclick="$('#monorthamerica').show();$(this).hide();$('#monaoff').show();return false;" style="text-decoration: none;">&#9658;</a><a id="monaoff" href="#" onclick="$('#monorthamerica').hide();$(this).hide();$('#monaon').show();return false;" style="color:#A4B0C1;text-decoration: none;display: none;">&#9660;</a>
<div id="monorthamerica" style="display: none;">
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode2" id="LocationCode2" data-name="New England" value="nbn" />ADD</button> New England<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode3" id="LocationCode3" data-name="NY NJ PA DE" value="nbm" />ADD</button> NY NJ PA DE<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode4" id="LocationCode4" data-name="MD VA NC SC GA" value="nbs" />ADD</button> MD VA NC SC GA<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode5" id="LocationCode5" data-name="British NA-unspecified" value="nb" />ADD</button> British NA-unspecified<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode6" id="LocationCode6" data-name="Spanish North America" value="ns" />ADD</button> Spanish North America<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode7" id="LocationCode7" data-name="French North America-southern" value="ifs" />ADD</button> French North America-southern
</div>
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode8" id="LocationCode8" data-name="England" value="b" />ADD</button> England
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode9" id="LocationCode9" data-name="Carribean" value="c" />ADD</button> Carribean
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode10" id="LocationCode10" data-name="Canada" value="%c" />ADD</button> Canada: <a id="mocanon" href="#" onclick="$('#mocanada').show();$(this).hide();$('#mocanoff').show();return false;" style="text-decoration: none;">&#9658;</a><a id="mocanoff" href="#" onclick="$('#mocanada').hide();$(this).hide();$('#mocanon').show();return false;" style="color:#A4B0C1; text-decoration: none;display: none;">&#9660;</a>
<div id="mocanada" style="display: none;">
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode11" id="LocationCode11" data-name="English Canada" value="nbc" />ADD</button> English Canada<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode12" id="LocationCode12" data-name="French Canada" value="nfc" />ADD</button> French Canada
</div>
</div>

<div><button class="savebutton radiochoice" name="LocationCode13" id="LocationCode13" data-name="Europe" value="e" />ADD</button> Europe</div>
<div><button class="savebutton radiochoice" name="LocationCode14" id="LocationCode14" data-name="Africa" value="af" />ADD</button> Africa</div>
<div><button class="savebutton radiochoice" name="LocationCode15" id="LocationCode15" data-name="South America" value="sa" />ADD</button> South America</div>
<div><button class="savebutton radiochoice" name="LocationCode16" id="LocationCode16" data-name="Asia and Pacific" value="a" />ADD</button> Asia and Pacific</div>
<div><button class="savebutton radiochoice" name="LocationCode17" id="LocationCode17" data-name="Near or Middle East" value="me" />ADD</button> Near or Middle East</div>
<div><button class="savebutton radiochoice" name="LocationCode18" id="LocationCode18" data-name="Other" value="o" />ADD</button> Other</div>
<div><button class="savebutton radiochoice" name="LocationCode19" id="LocationCode19" data-name="On ship at sea" value="sea" />ADD</button> On ship at sea</div>
<div><button class="savebutton radiochoice" name="LocationCode20" id="LocationCode20" data-name="unknown or unintended" value="u" />ADD</button> unknown or unintended</div>

<div id="LocationCodesTags">
</div>

</div>


<div style="clear:both;"></div>
<div id="referenceLocationBox" style="margin:5px;"><strong>Referred Location: </strong>
  <br />
  <input class="textinput" type="text" name="Location" id="Location" value="" />&nbsp;<button class="savebutton" onclick="showAll('Location');"> SHOW ALL </button><div id="LocationMatches"></div>
  <div id="LocationTags">
</div>
</div>
  
<div style="clear:both;"></div>


</div>

<div style="clear:both;"></div><br />
<div id="searchTagchain" style="width: 420px;padding: 10px; display: block; background-color:#E6E6E6;">
<form name="searchnow" id="searchnow" enctype="multipart/form-data" action="search.php" onsubmit="return submitSearch();" method="post" >
<button id="g_search" class="dashbutton" onclick="generateTags();return false;"> GENERATE SEARCH TAGS </button>&nbsp;<button id="q_search" class="dashbutton" onclick="if ($('#thequery').html().length > 1) { document.forms[0].submit(); } else { alert('Please make selections and click \'Generate Search Tags\' before submitting a search.'); }"> SEARCH </button>
<button id="reset_search" class="dashbutton" onclick="document.location.href='search-citation2.php?r';return false;">RESET</button>
</form>
<hr />

<div id="querydebug">

</div>
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


</body>
</html>