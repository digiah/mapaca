<?php session_start(); 

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

$pubtitlesq = "select `Gen_ttl`, `BiblioTitle_ID`, `id` from `_dighum_music_bibliotitle` order by `Gen_ttl` asc";
$pubtitlesr = $mysqli->query($pubtitlesq) or die (mysqli_error($mysqli));

$pubcities = array();
$pubcolonies = array();
$years = array();
$locations = array();
$locationcodes = array();
$pubtitles = array();
$pubcodes = array();

while ($pc = mysqli_fetch_array($pubcityr)) { $pubcities[] = $pc[0]; }
while ($pc = mysqli_fetch_array($pubcolonyr)) { $pubcolonies[] = $pc[0]; }
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
<script>
<?php #include("js.php"); ?>

var musicSearch = { }

musicSearch.nowValue = "";
musicSearch.nowField = "";
musicSearch.tagID = 0;
musicSearch.searchTags = new Array();
musicSearch.numTags = musicSearch.searchTags.length;
musicSearch.possibleLocations = null;
musicSearch.possiblePublicationTitles = null;

function searchTag (searchfield, searchvalue, searchtype, andor, tagID) {
	this.searchfield = searchfield; /* the name of the field being searched */
	this.searchvalue = searchvalue; /* the value of the field being searched */
	this.searchtype = searchtype;   /* LIKE / NOT LIKE */
	this.andor = andor;				/* AND or OR operator */
	this.id = tagID+1;				/* The index in the query chain */
	this.html = "<div class='tag' id='tag_"+this.id+"'><div class='tagend'><button class='tagendX' onclick='killTag("+this.id+");'>x</button></div><div class='taghead'><select id='q_"+this.id+"' class='minimenu' onchange='toggleLIKE("+this.id+")'><option value='LIKE'>IN</option><option value='NOT LIKE'>NOT IN</option></select></div><div class='tagbody'><div id='searchfield_"+this.id+"' class='tagtop'>"+this.searchfield+"</div><div id='searchvalue_"+this.id+"' class='tagbottom'>"+this.searchvalue+"</div></div></div>";
	
	/* y'olde and/or selection menu 
	<select onchange='toggleANDOR("+this.id+");' class='minimenu'><option value=' AND '>AND</option><option value=' OR '>OR</option></select>
	*/
	
	this.getSearchField = function() { return this.searchfield; };
	this.getSearchValue = function() { return this.searchvalue; };
	this.getSearchType = function() { return this.searchtype; };
	this.getSearchAndor = function() { return this.andor; };
	this.getAndOr = function() { return this.andor; };
	this.getID = function() { return this.id; };
	this.getHTML = function() { return this.html; };
	
	this.setSearchField = function(value) { this.searchfield = value; };
	this.setSearchValue = function(value) { this.searchvalue = value; };
	this.setSearchType = function(value) { this.searchtype = value; };
	this.setSearchAndor = function(value) { this.andor = value; };
	this.setAndOr = function(value) { this.andor = value; };
	this.setID = function(value) { this.id = value; };
	this.setHTML = function(value) { this.html = value; };
		
}


$(document).ready(function(e) {
	
	/* field events */	
	$(".modifiers").change(function () { addModifier(this.id); } );
	$(".modifiers").mouseup(function () { $("#dashboardblocker").hide(); } );

	/* radio button events */
	$(".radiochoice").click(function() { addModifier(this.id); this.blur(); });
	
	/* year selection events */
	$("#Year").click(function() { addModifier(this.id); this.blur(); });
	$("#y1").change(function () { $("#y2").val(($(this).val())); addModifier(this.id); this.blur(); } );
	
	/* dashboard button events */
	$("#closedash").click(function() { closeDash(); this.blur(); });
	$("#q_in").click(function() { setQ('LIKE'); this.blur(); });
	$("#q_notin").click(function() { setQ('NOT LIKE'); this.blur(); });
	$("#q_search").click(function() { buildQuery(); });
	
	$("#dashboardblocker").css({ opacity: 0.5 });
	$("#Content").click(function() { if ($(this).val() == "Enter search text and press enter") { $(this).val(""); } });
  	$("#Content").keypress(function( event ) { if (event.which==13) { addModifier(this.id); } });
	
	$("#locmatches").css( { left: $("#t5").outerWidth()-10 } );
	$("#locmatches").css( { minHeight: $("#t5").outerHeight()-10 } );
	
  	//$("#citation").blur(function() { addModifier(this.id); });
	
	/* text lookahead arrays */
	musicSearch.possibleLocations = new Array(<?php echo implode(", ",$locations); ?>);
	musicSearch.possiblePublicationTitles = new Array(<?php echo implode(", ",$pubtitles); ?>);
	musicSearch.publicationCodes = new Array(<?php echo implode(", ",$pubcodes); ?>);
	
	/* key-based events */
	$("#Location").keyup( function() { getLoc(); });
	$("#Title").keyup( function() { getTitle(); });
	
	
});

function getLoc() {
	if ($("#Location").val().length > 0) {
		$("#locmatches").fadeIn(150);
		/* see if the current value of looky is a substring in any of the entries in compoundclasses */
		var found = new Array();
		for (i=0;i<musicSearch.possibleLocations.length;i++) {
			var p=new RegExp("^"+$("#Location").val(),"i");
			if (p.test(musicSearch.possibleLocations[i])) {
				found.push("<a href='#' onclick='setLoc("+i+");return false;'>"+musicSearch.possibleLocations[i].replace(new RegExp($("#Location").val(),"ig"), "<span class='red'>"+$("#Location").val()+"</span>")+"</a>");
			}
		}
		$("#locmatches").html(found.join(", "));
	}
	else {
		$("#locmatches").fadeOut(150);
		$("#locmatches").html("");
	}
}

function setLoc(index) {
	$("#Location").val(musicSearch.possibleLocations[index]);
	addModifier("Location");
	$("#locmatches").fadeOut(150);
	$("#locmatches").html("");
}



function getTitle() {
	if ($("#Title").val().length > 0) {
		$("#titlematches").fadeIn(150);
		/* see if the current value of looky is a substring in any of the entries in compoundclasses */
		var found = new Array();
		for (i=0;i<musicSearch.possiblePublicationTitles.length;i++) {
			var p=new RegExp("^"+$("#Title").val(),"i");
			if (p.test(musicSearch.possiblePublicationTitles[i])) {
				found.push("<a href='#' onclick='setTitle("+i+");return false;'>"+musicSearch.possiblePublicationTitles[i].replace(new RegExp($("#Title").val(),"ig"), "<span class='red'>"+$("#Title").val()+"</span>")+"</a>");
			}
		}
		$("#titlematches").html(found.join(", "));
	}
	else {
		$("#titlematches").fadeOut(150);
		$("#titlematches").html("");
	}
	
}


function setTitle(index) {
	$("#Title").val(musicSearch.possiblePublicationTitles[index]);
	$("#Title").attr("data-code",musicSearch.publicationCodes[index]);
	addModifier("Title");
	$("#titlematches").fadeOut(150);
	$("#titlematches").html("");
}


function buildQuery() {
	/*
	Location
	Location Code
	Year
	Colony
	City
	Citation
	*/
	
	var location = "";
	var locationcode = "";
	var year = "";
	var colony = "";
	var city = "";
	var citation = "";
	
	var query = "select * from `_dighum_music_citation` where ";

	/* go through the list (i=0;i<musicSearch.numTags;i++)*/
	for (i=musicSearch.numTags-1;i>=0;i--) {
		if (musicSearch.searchTags[i].getSearchField() == "Year") {
			/* should we split the year? */
			years = musicSearch.searchTags[i].getSearchValue().split("-");
			if (years.length > 1) {
				if (years[1].length == 0) { 
					/* just one year */
					yq = "( `Year` "+musicSearch.searchTags[i].getSearchType()+" "+years[0]+" ) ";	 
				}
				else { 
					/* year range */
					if (musicSearch.searchTags[i].getSearchType() == "LIKE") {
						yq = " ( (`Year` >= "+years[0]+") AND (`Year` <= "+years[1]+") ) "; 
					}
					else {
						yq = " ( (`Year` <= "+years[0]+") AND (`Year` >= "+years[1]+") ) ";
					}
				}
			}
			else {
				yq = " ( `Year` "+musicSearch.searchTags[i].getSearchType()+" "+years[0]+" ) ";
			}
			query += yq;
		}
		else if (musicSearch.searchTags[i].getSearchField() == "Title") {
			query += " (`BiblioTitle_ID` "+musicSearch.searchTags[i].getSearchType()+" '"+$("#Title").attr("data-code")+"') ";
		}
		else {
			query += " ( `"+musicSearch.searchTags[i].getSearchField()+"` "+musicSearch.searchTags[i].getSearchType()+" '%"+musicSearch.searchTags[i].getSearchValue()+"%' ) ";
		}
		/*
		else if (musicSearch.searchTags[i].getSearchField() == "Content") {
			  var terms = musicSearch.searchTags[i].getSearchValue().split(",");
			   trim leading spaces later $.trim(...) 
			  subq = " ( ";
			  if (terms.length > 1) {
				  var tempq = new Array();
				  for (j=0;j<terms.length;j++) {
					  tempq.push(" ( `Content` LIKE '%"+$.trim(terms[j])+"%' ) ");
				  }
				  subq += tempq.join(" AND ");
			  }
			  else {
				  subq += "`Content` LIKE '%"+$.trim(terms[0])+"%'";
			  }
			  subq += " ) ";
			  query += " "+subq+" ";
		}
		*/
		if (i != 0) { query += musicSearch.searchTags[i].getSearchAndor()+" "; }
	}
	
	$("#query").html(query);
}

function addModifier(id) {
	var found = false;
	var chain = false;
	if ($("#"+id).val() != "%%") {
		/* is this value already in the query? */
		for (i=0;i<musicSearch.numTags;i++) {
			var compareto = $("#"+id).val();
			if ($("#"+id).attr("name") == "LocationCode") { compareto = $("#"+id).attr("data-name"); }
			if (musicSearch.searchTags[i].getSearchValue() == compareto) {
				found = true;
				break;
			}
		}
		if (found == true) {
			alert("This value is already in your query.");
		}	
		else {
			if (id != "y1") {
				
				$("#dashboardblocker").hide();
				var andor = " AND ";
				var searchtype = "LIKE";
				
				if (id == "Year") {
					if ($("#y1").val() == $("#y2").val()) { musicSearch.nowValue = $("#y1").val(); }
					else {
						if ($("#y2").val() == "%%") {
							musicSearch.nowValue = $("#y1").val()+"-";
						}
						else {
							musicSearch.nowValue = $("#y1").val()+"-"+parseInt($("#y2").val());
						}
						
					}
				}
				else if ($("#"+id).attr("name") == "LocationCode") {
					musicSearch.nowValue = $("#"+id).attr("data-name");
				}
				else if (id == "Content") {
					var terms = $("#Content").val().split(",");
					if (terms.length > 1) {
						chain = true;
						for (j=0;j<terms.length;j++) {
							musicSearch.nowValue = $.trim(terms[j]);
							musicSearch.nowField = id;
							temp = new searchTag (musicSearch.nowField, musicSearch.nowValue, searchtype, andor, musicSearch.tagID);
							musicSearch.tagID++;
							musicSearch.numTags++;
							musicSearch.searchTags.push(temp);
							if (musicSearch.numTags > 1) {
								/* add a logic box */
								$("#tagarea").prepend("<div id='logicbox"+musicSearch.tagID+"' class='logicbox'><select onchange='toggleANDOR("+musicSearch.tagID+");'><option value=' AND '>AND</option><option value=' OR '>OR</option></select></div>");
							}
							$("#tagarea").prepend(temp.getHTML());
							
							$("#q_"+musicSearch.tagID).val(searchtype);
							$("#tag_"+musicSearch.tagID).fadeIn(225);	
						}
					}
					else {
						musicSearch.nowValue = terms[0];
					}
								
				}
				else {
					musicSearch.nowValue = $("#"+id).val();	
				}
				
				if (chain == false) {
					
					musicSearch.nowField = id;
					temp = new searchTag (musicSearch.nowField, musicSearch.nowValue, searchtype, andor, musicSearch.tagID);
					musicSearch.tagID++;
					musicSearch.numTags++;
					musicSearch.searchTags.push(temp);
					if (musicSearch.numTags > 1) {
						/* add a logic box */
						$("#tagarea").prepend("<div id='logicbox"+musicSearch.tagID+"' class='logicbox'><select onchange='toggleANDOR("+musicSearch.tagID+");'><option value=' AND '>AND</option><option value=' OR '>OR</option></select></div>");
					}
					$("#tagarea").prepend(temp.getHTML());
					$("#q_"+musicSearch.tagID).val(searchtype);
					$("#tag_"+musicSearch.tagID).fadeIn(225);	
				
				}
			
			}
		}
	}
	else {
		/* need to remove all searchtags that are related to this id */
		for (i=0;i<musicSearch.numTags;i++) {
			if (musicSearch.searchTags[i].getSearchField() == id) {
				/* remove the HTML */
				$("#tag_"+musicSearch.searchTags[i].getID()).remove();
				musicSearch.numTags--;			
				/* splice */
				musicSearch.searchTags.splice(i,1);
				i = -1;
			}
		}
	}
}

function getSelectionText(id) {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }
	if (text.length > 0) {
		$("#dashboardblocker").hide();
		musicSearch.nowValue = text;
		musicSearch.nowField = id;
		/*
		$("#searchvalue").html();
		$("#searchfield").html();
		*/
	}
	else {
		$("#dashboardblocker").show();
	}
}

function closeDash() { $("#dashboardblocker").show(); }

function setQ(searchtype) {
	var andor = " AND ";
	/* musicSearch.nowValue = $.trim(musicSearch.nowValue); */
	/* stripping out all punctuation may not be necessary... 
	musicSearch.nowValue = musicSearch.nowValue.replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()]/g,"");
	*/
	
	temp = new searchTag (musicSearch.nowField, musicSearch.nowValue, searchtype, andor, musicSearch.tagID);
	musicSearch.tagID++;
	musicSearch.numTags++;
	musicSearch.searchTags.push(temp);
	closeDash();
	$("#tagarea").prepend(temp.getHTML());
	$("#q_"+musicSearch.tagID).val(searchtype);
	$("#tag_"+musicSearch.tagID).fadeIn(225);
}


function killTag(id) {
	/* remove the OBJECT */
	var index = getTagIndex(id);
	if (index >= 0) {
		if (musicSearch.searchTags[index].getID() == id) {
			/* splice */
			musicSearch.searchTags.splice(index,1);
			/* remove the HTML */
			$("#tag_"+id).remove();
			if ($("#logicbox"+id).length > 0) {
				$("#logicbox"+id).remove();
			}
			if ((id == 1) && (musicSearch.numTags > 1)) {
				/* remove the logicbox of the *next* tag */
				for (j=2;j<musicSearch.tagID;j++) {
					if ($("#logicbox"+j).length > 0) {
						$("#logicbox"+j).remove();
						break;
					}
				}
			}
			musicSearch.numTags--;
		}
	}
	else {
		alert("unlikely! but this id wasn't found!"); 	
	}
}

function toggleANDOR(id) {
	var index = getTagIndex(id);
	if (index >= 0) {
		if (musicSearch.searchTags[index].getAndOr() == " AND ") {
			musicSearch.searchTags[index].setAndOr(" OR ");
		}
		else {
			musicSearch.searchTags[index].setAndOr(" AND ");
		}
	}
	else {
		alert("unlikely! but this id wasn't found!"); 	
	}	
}

function toggleLIKE(id) {
	var index = getTagIndex(id);
	if (index >= 0) {
		musicSearch.searchTags[index].setSearchType($("#q_"+id).val());
		console.log(musicSearch.searchTags[index].getSearchType());
	}
	else {
		alert("unlikely! but this id wasn't found!"); 	
	}	
}

function getTagIndex(id) {
	for (i=0;i<musicSearch.numTags;i++) {
		if (musicSearch.searchTags[i].getID() == id) { break;}
	}
	if (i < musicSearch.numTags ) { return i; }
	else { return -1; }
}
</script>
</head>

<body>
<div id="title_related" style="padding: 10px; display: block; background-color:#E6E6E6;float: left;margin-bottom: 10px;">
<div id="loc" style="margin:5px;"><strong>Title: </strong>
  <input data-code="" class="textinput" type="text" name="Title" id="Title" value="" /><div id="titlematches"></div></div>
</div>

<div style="clear:both;"></div>

<div id="location_related" style="padding: 10px; display: block; background-color:#E6E6E6;float: left;">

<div id="column1" style="width: auto;float:left;display:block;">
<div id="t6" style="margin:5px;">
<strong>Region:</strong><br />
<div>
<button class="savebutton radiochoice" name="LocationCode" id="LocationCode1" data-name="North America" value="%n%" />ADD</button> North America: <a id="monaon" href="#" onclick="$('#monorthamerica').show();$(this).hide();$('#monaoff').show();return false;" style="text-decoration: none;">&#9658;</a><a id="monaoff" href="#" onclick="$('#monorthamerica').hide();$(this).hide();$('#monaon').show();return false;" style="color:#A4B0C1;text-decoration: none;display: none;">&#9660;</a>
<div id="monorthamerica" style="display: none;">
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode2" data-name="New England" value="nbn" />ADD</button> New England<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode3" data-name="NY NJ PA DE" value="nbm" />ADD</button> NY NJ PA DE<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode4" data-name="MD VA NC SC GA" value="nbs" />ADD</button> MD VA NC SC GA<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode5" data-name="British NA-unspecified" value="nb" />ADD</button> British NA-unspecified<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode6" data-name="Spanish North America" value="ns" />ADD</button> Spanish North America<br /> 
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode7" data-name="French North America-southern" value="ifs" />ADD</button> French North America-southern
</div>
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode" id="LocationCode8" data-name="England" value="b" />ADD</button> England
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode" id="LocationCode9" data-name="Carribean" value="c" />ADD</button> Carribean
</div>

<div>
<button class="savebutton radiochoice" name="LocationCode" id="LocationCode10" data-name="Canada" value="%c" />ADD</button> Canada: <a id="mocanon" href="#" onclick="$('#mocanada').show();$(this).hide();$('#mocanoff').show();return false;" style="text-decoration: none;">&#9658;</a><a id="mocanoff" href="#" onclick="$('#mocanada').hide();$(this).hide();$('#mocanon').show();return false;" style="color:#A4B0C1; text-decoration: none;display: none;">&#9660;</a>
<div id="mocanada" style="display: none;">
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode11" data-name="English Canada" value="nbc" />ADD</button> English Canada<br />
&nbsp;&nbsp;&nbsp;<button class="savebutton radiochoice" name="LocationCode" id="LocationCode12" data-name="French Canada" value="nfc" />ADD</button> French Canada
</div>
</div>

<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode13" data-name="Europe" value="e" />ADD</button> Europe</div>
<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode14" data-name="Africa" value="af" />ADD</button> Africa</div>
<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode15" data-name="South America" value="sa" />ADD</button> South America</div>
<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode16" data-name="Asia and Pacific" value="a" />ADD</button> Asia and Pacific</div>
<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode17" data-name="Near or Middle East" value="me" />ADD</button> Near or Middle East</div>
<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode18" data-name="Other" value="o" />ADD</button> Other</div>
<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode19" data-name="On ship at sea" value="sea" />ADD</button> On ship at sea</div>
<div><button class="savebutton radiochoice" name="LocationCode" id="LocationCode20" data-name="unknown or unintended" value="u" />ADD</button> unknown or unintended</div>
</div>
</div>

<div id="column2" style="width: auto;float:left;display:block;">

<div id="t5" style="margin:5px;"><strong>Referred Location: </strong>
  <input class="textinput" type="text" name="Location" id="Location" value="" /><div id="locmatches"></div></div>

<div style="clear:both;"></div>

<div id="t2" style="margin:5px;"><strong>Publication Colony: </strong>
  <select class="modifiers" name="PubColony" id="PubColony"><option value='%%'>ANY</option><?php for ($i=0;$i<count($pubcolonies);$i++): ?><option value="<?php echo $pubcolonies[$i]; ?>"><?php echo $pubcolonies[$i]; ?></option><?php endfor; ?></select></div>

<div style="clear:both;"></div>

<div id="t3" style="margin:5px;"><strong>Publication City: </strong>
  <select class="modifiers" name="PubCity" id="PubCity"><option value='%%'>ANY</option><?php for ($i=0;$i<count($pubcities);$i++): ?><option value="<?php echo $pubcities[$i]; ?>"><?php echo $pubcities[$i]; ?></option><?php endfor; ?></select></div>

<div style="clear:both;"></div>

</div>

<div style="clear:both;"></div>

</div>

<div id="time_related" style="padding: 10px; display: block; background-color:#E6E6E6;float: left;margin-left:10px;">

<div id="t1" style="margin:5px;"><strong>Year:</strong>
<select name="y1" id="y1"><option value='%%'>ANY</option><?php for ($i=0;$i<count($years);$i++): ?><option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option><?php endfor; ?></select>&nbsp;&mdash;&nbsp;<select name="y2" id="y2"><option value='%%'>ANY</option><?php for ($i=0;$i<count($years);$i++): ?><option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option><?php endfor; ?></select>&nbsp;<button id="Year" name="Year" class="savebutton modifiers"> ADD </button></div>

<div style="clear:both;"></div>

</div>

<div style="clear:both;"></div>

<div id="t4">
  <div id="citationholder"><strong>Content:</strong></div><br /><input type="text" name="Content" id="Content" value="Enter search text and press enter" /><button id="ContentAdd" name="ContentAdd" class="savebutton modifiers" style="margin-left: 15px;" onclick="addModifier('Content');this.blur();"> ADD </button>
  <div style="clear:both;"></div>
Enclose phrases in quotation marks: <strong>&quot;&quot;</strong>. Separate multiple search items with commas. You can perform &quot;and&quot; and &quot;or&quot; logical operators on the resulting query chain.</div>

<div id="dashboardholder">
<div id="dashboardblocker"></div>
<div id="dashboard">
<button id="q_search" class="dashbutton"> SEARCH </button>
</div>
</div>

<div id="tagarea">
<div style="clear:both;"></div>
</div>

<div id="query">
</div>
<? /*<div class='switch'><img class='switchor' id='switch_1' name='switch_1' onclick='toggleANDOR(1);' src='images/andor-switch.jpg' width='80' height='20' /></div>
<div class='tag'>
<div class='tagend'><button onclick='killTag(this.ID);'>x</button><select onchange='toggleANDOR(1);' class='minimenu'><option value=' AND '>AND</option><option value=' OR '>OR</option></select></div> 
<div class='taghead'><select class='minimenu' onchange='toggleLIKE()'><option value='q_in'>{..}</option><option value='q_notin'>! {..}</option></select></div>
<div class='tagbody'>
<div id='searchfield' class='tagtop'>hello</div>
<div id='searchvalue' class='tagbottom'>goodbye</div>
</div>
</div>
*/ ?>


</body>
</html>