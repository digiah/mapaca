var musicSearch = { }

musicSearch.nowValue = "";
musicSearch.nowField = "";
musicSearch.tagID = 0;
musicSearch.searchTags = new Array();
musicSearch.numTags = musicSearch.searchTags.length;
musicSearch.possibleLocations = null;

function searchTag (searchfield, searchvalue, searchtype, andor, tagID) {
	this.searchfield = searchfield;
	this.searchvalue = searchvalue;
	this.searchtype = searchtype;
	this.andor = andor;
	this.id = tagID+1;
	this.html = "<div class='tag' id='tag_"+this.id+"'><div class='tagend'><button class='tagendX' onclick='killTag("+this.id+");'>x</button></div><div class='taghead'><select id='q_"+this.id+"' class='minimenu' onchange='toggleLIKE("+this.id+")'><option value='LIKE'>IN</option><option value='NOT LIKE'>NOT IN</option></select><select onchange='toggleANDOR("+this.id+");' class='minimenu'><option value=' AND '>AND</option><option value=' OR '>OR</option></select></div><div class='tagbody'><div id='searchfield_"+this.id+"' class='tagtop'>"+this.searchfield+"</div><div id='searchvalue_"+this.id+"' class='tagbottom'>"+this.searchvalue+"</div></div></div>";
	
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

	$("#Year").click(function() { addModifier(this.id); this.blur(); });
	
	$("#y1").change(function () { $("#y2").val(($(this).val())); addModifier(this.id); this.blur(); } );
	
	/* dashboard button events */
	$("#closedash").click(function() { closeDash(); this.blur(); });
	$("#q_in").click(function() { setQ('LIKE'); this.blur(); });
	$("#q_notin").click(function() { setQ('NOT LIKE'); this.blur(); });
	$("#q_search").click(function() { buildQuery(); });
	
	$("#dashboardblocker").css({ opacity: 0.5 });
	$("#citation").click(function() { if ($(this).val() == "Enter search text and press enter") { $(this).val(""); } });
  	$("#citation").keypress(function( event ) { if (event.which==13) { addModifier(this.id); } });
	
	$("#locmatches").css( { left: $("#t5").outerWidth()-10 } );
	$("#locmatches").css( { minHeight: $("#t5").outerHeight()-10 } );
	
	console.log($("#t5").css("padding"));
  	//$("#citation").blur(function() { addModifier(this.id); });
	
	/* text lookahead arrays */
	musicSearch.possibleLocations = new Array(<?php echo implode(", ",$locations); ?>);
	
	/* key-based events */
	$("#Location").keyup( function() { getLoc(); });
	
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
	$("#locmatches").fadeOut(150);
	$("#locmatches").html("");
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

	/* go through the list */
	for (i=0;i<musicSearch.numTags;i++) {
		if (musicSearch.searchTags[i].getSearchField() == "Year") {
			/* should we split the year? */
			years = musicSearch.searchTags[i].getSearchValue().split("-");
			if (years.length > 1) {
				if (years[1].length == 0) { yq = "(`Year` >= "+years[0]+")"; }
				else { yq = " (`Year` >= "+years[0]+") AND (`Year` <= "+years[1]+") "; }
			}
			else {
				yq = " `Year` "+musicSearch.searchTags[i].getSearchType()+" "+years[0]+" ";
			}
			query += yq;
		}
		else {
			query += " `"+musicSearch.searchTags[i].getSearchField()+"` "+musicSearch.searchTags[i].getSearchType()+" '%"+musicSearch.searchTags[i].getSearchValue()+"%' ";
		}
		if ((i+1) != musicSearch.numTags) { query += musicSearch.searchTags[i].getSearchAndor()+" "; }
	}
	
	$("#query").html(query);
}

function addModifier(id) {
	var found = false;
	if ($("#"+id).val() != "%%") {
		/* is this value already in the query? */
		for (i=0;i<musicSearch.numTags;i++) {
			if (musicSearch.searchTags[i].getSearchValue() == $("#"+id).val()) {
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
				else {
					musicSearch.nowValue = $("#"+id).val();	
				}
				
				musicSearch.nowField = id;
				temp = new searchTag (musicSearch.nowField, musicSearch.nowValue, searchtype, andor, musicSearch.tagID);
				musicSearch.tagID++;
				musicSearch.numTags++;
				musicSearch.searchTags.push(temp);
				$("#tagarea").prepend(temp.getHTML());
				$("#q_"+musicSearch.tagID).val(searchtype);
				$("#tag_"+musicSearch.tagID).fadeIn(225);	
			
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
			if (musicSearch.searchTags[index].getSearchField() == "t4") {
				
				text = $("#"+musicSearch.searchTags[index].getSearchField()).html();
				
				text = text.replace("<span class=\"hilite\">"+musicSearch.searchTags[index].getSearchValue()+"</span>",musicSearch.searchTags[index].getSearchValue());
				
				$("#"+musicSearch.searchTags[index].getSearchField()).html(text);
				
				/*
				$("#"+musicSearch.searchTags[index].getSearchField()).html($("#"+musicSearch.searchTags[index].getSearchField()).html().replace("<span class=\"hilite\">"+musicSearch.searchTags[index].getSearchValue()+"</span>",musicSearch.searchTags[index].getSearchValue));
				*/
			}
			/* splice */
			musicSearch.searchTags.splice(index,1);
			/* remove the HTML */
			$("#tag_"+id).remove();
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
		console.log(musicSearch.searchTags[index].getAndOr());
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