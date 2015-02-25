

/* Monitors user typing, narrowing down values as they go... */
function guessText(id) {
	/* get ALL the possible values for this filter */
	var guessArray = eval("musicSearch.possible"+id);
	
	/* if user has typed something */
	if ($("#"+id).val().length > 0) {
		var found = new Array();
		var p=new RegExp("^"+$("#"+id).val(),"i");
		for (i=0;i<guessArray.length;i++) {
			/* if what the user has typed is a substring of anything in the possible values array...*/
			if (p.test(guessArray[i])) {
				/* save the HTML */
				found.push("<a href='#' onclick='setText("+i+",\""+id+"\");return false;'>"+guessArray[i].replace(new RegExp($("#"+id).val(),"ig"), "<span class='red'>"+$("#"+id).val().toUpperCase()+"</span>")+"</a>");
			}
		}
		/* render the HTML */
		$("#"+id+"Matches").html("<button class='savebutton' onclick='hideAll(\""+id+"\");'>x</button><hr />"+found.join(", ")+"<hr /><button class='savebutton' onclick='hideAll(\""+id+"\");'>x</button>");
		
		/* show the div */
		if (found.length > 0) {
			$("#"+id+"Matches").fadeIn(150);
		}
		else {
			$("#"+id+"Matches").fadeOut(150);
			$("#"+id+"Matches").html("");
		}
	}
	else {
		$("#"+id+"Matches").fadeOut(150);
		$("#"+id+"Matches").html("");
	}	
}

/* 
Generate the search tag if the user clicks on a possible value.
*/
function setText(index, id) {
	
	var guessArray = eval("musicSearch.possible"+id);
	if (id == "PubColony") {
		/* 
		There is a special case here for the situation where the user is 
		typing the full colony name or just the abbreviation.
		*/
		if (guessArray[index].length > 2) {
			/* full colony name */
			var value = guessArray[index];
			for (key in musicSearch.PubColonyNames) { if (value == musicSearch.PubColonyNames[key]) { var dataValue = key; } }
		}
		else {
			/* abbreviation */
			var value = musicSearch.PubColonyNames[guessArray[index]];
			var dataValue = guessArray[index];
		}
	}
	
	else if (id == "Location") {
		if (index == -1) {
			var value = $("#"+id).val();
		}
		else {
			var value = guessArray[index];
		}
		var dataValue = value;
	}
	
	else {
		var value = guessArray[index];
		var dataValue = value;
	}
	
	$("#"+id).val(dataValue);
	addFilter(id, dataValue, index);
	$("#"+id+"Matches").fadeOut(150);
	$("#"+id+"Matches").html("");
}

/* Shows the possible values associated with certain text fields */
function showAll(id) { 
	guessArray = eval("musicSearch.possible"+id);
	var found = new Array();
	for (i=0;i<guessArray.length;i++) {
		found.push("<a href='#' onclick='setText("+i+",\""+id+"\");return false;'>"+guessArray[i]+"</a>");
	}
	$("#"+id+"Matches").html("<button class='savebutton' onclick='hideAll(\""+id+"\");'> CLOSE </button><hr />"+found.join(", ")+"<hr /><button class='savebutton' onclick='hideAll(\""+id+"\");'> CLOSE </button>");
	$("#"+id+"Matches").fadeIn(150);
}

/* Hides the possible values associated with certain text fields */
function hideAll(id) {
	$("#"+id+"Matches").fadeOut(150);
	$("#"+id+"Matches").html("");
}



/* 
Create and add the filter's HTML widget, based on the #id, 
the value of the text field or option, 
and the 1 of X index we're working with.

This function is called after the user clicks 
the ADD button that appears next to each filter.
*/

function addFilter(id, value, index) {
	
	/* Hide any "matches" windows that are showing */
	$('.varMatches').each(function(i, obj) {
		$(this).fadeOut(150); 
	});
	
	/* 
	Various chunks of canned HTML for the UI widget that pops up when user adds a search element.
	Some of these start empty and are modified by specific filers.
	*/
	var killbutton = "<button onclick='removeFilter(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\");' class='switchred'>x</button>";
	var andorswitch = "<button id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"_AND' onclick='toggleANDOR(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\", \"_AND\"); this.blur();' class='switchgreen'>AND</button><button id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"_OR' onclick='toggleANDOR(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\", \"_OR\"); this.blur();' class='switchgray'>OR</button>";
	var likebutton = "<button id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"_NOT' onclick='toggleLIKE(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\");this.blur();' class='switchgreen'>LIKE</button>";
	var editbutton = "";
	var datacontent = "";
	var extra = "";
	
	var nogo = false;
	
	/* 
	This cascade of IF statements has conditionals that determine 
	response depending on which filter we're working with.
	*/
	if (id == "Title") {
		
		if ($("#"+id).val().length > 0) {
			datacontent = " data-content='"+musicSearch.publicationCodes[index]+"' ";
			var tagvalue = "<div class='tagcopy'>"+$("#"+id).val()+"</div>";
		}
		else {
			nogo = true;
		}	
	}
	
	else if (id == "Year") {
		
		/* first year not set, second set */
		if (($("#y2").val() != "%%") && ($("#y1").val() == "%%")) {
			var tagvalue = "<div class='tagcopy'>&mdash;"+$("#y2").val()+"</div>";
			value = "-"+$("#y2").val();
		}
		/* first year set, second year not set */
		if (($("#y2").val() == "%%") && ($("#y1").val() != "%%")) {
			var tagvalue = "<div class='tagcopy'>"+$("#y1").val()+"&mdash;"+"</div>";
			value = $("#y1").val()+"-";
		}
		/* first year set, second year set */
		if (($("#y2").val() != "%%") && ($("#y1").val() != "%%")) {
			if ($("#y2").val() == $("#y1").val()) {
				var tagvalue = "<div class='tagcopy'>"+$("#y1").val()+"</div>";
				value = $("#y1").val();	
			}
			else {
				var tagvalue = "<div class='tagcopy'>"+$("#y1").val()+"&mdash;"+$("#y2").val()+"</div>";
				value = $("#y1").val()+"-"+$("#y2").val();
			}	
			value = $("#y1").val()+"-"+$("#y2").val();	
		}
		/* first year not set, second year not set */
		if (($("#y2").val() == "%%") && ($("#y1").val() == "%%")) {
			/* Don't add the widget if no year(s) have been selected */
			nogo = true;
		}
			
		$("#y1").val('%%');	
		$("#y2").val('%%');

	}
	else if (id == "Content") {
		/* Is there content typed into the field? */
		if ($("#ContentSort").val().length > 0) {
			likebutton = "";
			editbutton = "<button id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"_EDIT' onclick=\"$('#"+id+"').val('"+$("#"+id).val()+"');$('#ContentSort').val('"+$("#ContentSort").val()+"');removeFilter('"+id+"Tag"+eval("musicSearch."+id+"TagID")+"');\" class='switchgreen'>EDIT</button>";
			
			datacontent = " data-content='"+$("#ContentSort").val()+"' "
			var tagvalue = "<div class='tagcopy'>[ <em>"+$("#ContentSort").val()+"</em> ] "+$("#"+id).val()+"</div>";
			
			/* strip extra spaces */
			$("#"+id).val($("#"+id).val().replace(/\s{2,}/g, ' '));
			
			/* Flip the pull-down menu back to "all" */
			$("#ContentSort").val("all");	
		}
		else {
			/* Don't add the widget if the field is empty */
			nogo = true;
		}
	}
	else if (id.search("LocationCode") > -1) {
		if ($("#"+id).attr("data-name").length > 0) {
			
			var tagvalue = "<div class='tagcopy'>"+$("#"+id).attr("data-name")+"</div>";
			/* 
			"extra" is exclusive to this filter and holds the full spelling of the location.
			The database holds a more cryptic code in "data-name"
			*/
			extra = " data-name='"+$("#"+id).attr("data-name")+"' ";
			
			id = "LocationCodes";
			killbutton = "<button onclick='removeFilter(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\");' class='switchred'>x</button>";
			andorswitch = "<button id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"_AND' onclick='toggleANDOR(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\", \"_AND\"); this.blur();' class='switchgreen'>AND</button><button id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"_OR' onclick='toggleANDOR(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\", \"_OR\"); this.blur();' class='switchgray'>OR</button>";
			likebutton = "<button id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"_NOT' onclick='toggleLIKE(\""+id+"Tag"+eval("musicSearch."+id+"TagID")+"\");this.blur();' class='switchgreen'>LIKE</button>";
		}
		else {
			/* Don't add the widget if the field is empty */
			nogo = true;
		}
	}
	else {
		if (($("#"+id).val().length > 0) || (index > -1)) {
			var tagvalue = "<div class='tagcopy'>"+$("#"+id).val()+"</div>";
			var value = $("#"+id).val();
		}
		else {
			/* Don't add the widget if the field is empty */
			nogo = true;
		}
	}
	
	
	if (nogo == false) {
		/* Generate the HTML */
		var newTag = "<div class='subTag' id='"+id+"Tag"+eval("musicSearch."+id+"TagID")+"' "+
		datacontent+
		extra+
		" data-andor='AND' data-like='LIKE' data-value='"+
		value+
		"'>"+
		killbutton+
		likebutton+
		tagvalue+
		editbutton+
		andorswitch+
		"<div style='clear:both;'></</div>";
		
		/* Append it to the appropriate DIV */
		$("#"+id+"Tags").append(newTag);
		
		/* Increment the appropriate count */
		eval("musicSearch."+id+"TagID++");
		eval("musicSearch."+id+"TagCount++");
		
		/* Clear the filter's field */
		$("#"+id).val("");
	}
}

/* This function is called when the user clicks the little red X box in the HTML widgets */
function removeFilter(id) {
	/* remove the DIV from the DOM */
	$("#"+id).remove();
	generateTags(1);
	var temp = "";
	/* remove the appropriate entry from the array that tracks the tags for this filter */
	for (i=0;i<id.length;i++) { if (!$.isNumeric(id[i])) { temp += id[i]; } else break; }
	/* decrement the counter for this filter */
	eval("musicSearch."+temp+"Count--");
}

/* Switch the logical operator associated with the widget... the last one is irrelevant */
function toggleANDOR(id, op) {
	if (op == "_AND") {
		$("#"+id).attr("data-andor", "AND");
		$("#"+id+"_AND").attr("class","switchgreen");
		$("#"+id+"_OR").attr("class","switchgray");
	}
	else {
		$("#"+id).attr("data-andor", "OR");	
		$("#"+id+"_AND").attr("class","switchgray");
		$("#"+id+"_OR").attr("class","switchgreen");
	}
}

/* Switch the LIKE / NOT LIKE operator associated with the widget... */
function toggleLIKE(id) {
	op = $("#"+id).attr("data-like");
	if (op == " NOT LIKE ") {
		$("#"+id).attr("data-like", " LIKE ");
		$("#"+id+"_NOT").attr("class","switchgreen");
	}
	else {
		$("#"+id).attr("data-like", " NOT LIKE ");	
		$("#"+id+"_NOT").attr("class","switchgray");
	}
}


/*

This function takes the contents of the "data-*" attributes of
the HTML widgets generated for each filter and generates the
SQL query.

It runs through all of the children of each of the filters' DIVs
that contain the widgets, creating SQL and also saving the data
in an object that will be used for reconstructing this state when
the user refines a search.

tempTag is this object. In each for loop that cycles through the
DIV's children, this object is serialized and pushed into an array.

The function concludes by writing the completed SQL into a DIV on
the page.

*/

function generateTags(preview) {
	
	var query = "";
	var contentquery = "";
	var i = 0;
	var tempq = "";
	var qarray = new Array();
	
	/* commit any textfields */
	$('.textinput').each(function(i, obj) {
		if ($(this).val().length > 0) {
			addFilter($(this).attr("id"), $(this).val()); 
		}
	});
	
	/* commit year... */
	if ( $("#y1").val() + $("#y2").val() != "%%%%" ) { addFilter("Year",""); }
	
	/* set up the object so that we can save the state of the search for later */
	var tempTag = Object.create(musicSearch.searchTag);
	
	$('#TitleTags').children().each(function () {
				
		contentquery += "(`BiblioTitle_ID` "+$(this).attr("data-like")+" '"+$(this).attr("data-content")+"')";
		
		if (i < musicSearch.TitleTagCount-1) {
			contentquery += " "+$(this).attr("data-andor")+" ";
		}
		
		if (preview < 2) {
			
			tempTag.id = "Title";
			tempTag.index = i;
			tempTag.data_content = $(this).attr("data-content");
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
			musicSearch.allSearchTags.push(tempTag.serialize());
		
		}
		
		i++;
		
	});
	
	
	if (contentquery.length > 0) {
		query += contentquery+" ";
		qarray.push("("+contentquery+")");
	}
	
	
	contentquery = "";
	i=0;	
	tempq = "";
	
	$('#PubColonyTags').children().each(function () {
		contentquery += "(`PubColony` "+$(this).attr("data-like")+" '%"+$(this).attr("data-value")+"%')";
		if (i < musicSearch.PubColonyTagCount-1) {
			contentquery += " "+($(this).attr("data-andor"))+" ";
		}		
		
		if (preview < 2) {
			tempTag.id = "PubColony";
			tempTag.index = i;
			tempTag.data_content = "*";
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
			musicSearch.allSearchTags.push(tempTag.serialize());
		}
		
		i++;	
	});
	
	if (contentquery.length > 0) {
		query += contentquery+" ";
		qarray.push("("+contentquery+")");
	}
	
	contentquery = "";
	i=0;	
	tempq = "";
		
	$('#PubCityTags').children().each(function () {
		contentquery += "(`PubCity` "+$(this).attr("data-like")+" '%"+$(this).attr("data-value").replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();})+"%')";
		if (i < musicSearch.PubCityTagCount-1) {
			contentquery += " "+$(this).attr("data-andor")+" ";
		}
		
		if (preview < 2) {
			tempTag.id = "PubCity";
			tempTag.index = i;
			tempTag.data_content = "*";
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
			musicSearch.allSearchTags.push(tempTag.serialize());
		}
		i++;	
	});
	
	if (contentquery.length > 0) {
		query += contentquery+" ";
		qarray.push("("+contentquery+")");
	}
	
	contentquery = "";
	i=0;	
	tempq = "";
	
	$('#YearTags').children().each(function () {		
		years = $(this).attr("data-value").split("-");
		
		if (years.length > 1) {
			if (years[1].length == 0) { 
				/* start year */
				contentquery += "(`Year` >= "+years[0]+")";	 
			}
			else if (years[0].length == 0) {
				/* end year */
				contentquery += "(`Year` <= "+years[1]+")";
			}
			else { 
				/* year range */
				if ($(this).attr("data-like") == "LIKE") {
					if (years[0] == years[1]) {
						contentquery += "(`Year` = "+years[0]+")"; 
					}
					else {
						contentquery += "(`Year` >= "+years[0]+") AND (`Year` <= "+years[1]+")"; 
					}
				}
				else {
					if (years[0] == years[1]) {
						contentquery += "(`Year` != "+years[0]+")"; 
					}
					else {
						contentquery += "(`Year` <= "+years[0]+") AND (`Year` >= "+years[1]+")";
					}
				}
			}
		}
		else {
			contentquery += "(`Year` = "+years[0]+")";
		}	
		
		if (i < musicSearch.YearTagCount-1) {
			contentquery += " "+$(this).attr("data-andor")+" ";
		}
		
		if (preview < 2) {
			tempTag.id = "Year";
			tempTag.index = i;
			tempTag.data_content = "*";
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
			musicSearch.allSearchTags.push(tempTag.serialize());
		}
		i++;
			
	});
	
	if (contentquery.length > 0) {
		query += contentquery+" ";
		qarray.push("("+contentquery+")");
	}
	
	contentquery = "";
	i=0;	
	tempq = "";
	
	$('#LocationTags').children().each(function () {	
		contentquery += "(`Location` "+$("#LocationTag"+i).attr("data-like")+" '"+$("#LocationTag"+i).attr("data-value")+"')";
		if (i < musicSearch.LocationTagCount-1) {
			contentquery += " "+$("#LocationTag"+i).attr("data-andor")+" ";
		}
					
		if (preview < 2) {
			tempTag.id = "Location";
			tempTag.index = i;
			tempTag.data_content = "*";
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
		}
		
		musicSearch.allSearchTags.push(tempTag.serialize());
			
		i++;
	});
	
	if (contentquery.length > 0) {
		query += contentquery+" ";
		qarray.push("("+contentquery+")");
	}
	
	contentquery = "";
	i=0;	
	tempq = "";
	
	$('#ContentTags').children().each(function () {
	
		if ($(this).attr("data-content") == "all") {
			var terms = $(this).attr("data-value").split(" ");
			var tempq = new Array();
			contentquery += "(";
			for (j=0;j<terms.length;j++) {
				tempq.push("(`Content` LIKE '%"+terms[j]+"%')");
			}
			contentquery += tempq.join(" AND ");
			contentquery += ")";
		}
		else if ($(this).attr("data-content") == "exact") {
			contentquery += "(`Content` LIKE '%"+$(this).attr("data-value")+"%')";
		}
		else if ($(this).attr("data-content") == "any") {
			var terms = $(this).attr("data-value").split(" ");
			var tempq = new Array();
			contentquery += "(";
			for (j=0;j<terms.length;j++) {
				tempq.push("(`Content` LIKE '%"+terms[j]+"%')");
			}
			contentquery += tempq.join(" OR ");
			contentquery += ")";
		}
		else if ($(this).attr("data-content") == "none") {
			var terms = $(this).attr("data-value").split(" ");
			var tempq = new Array();
			contentquery += "(";
			for (j=0;j<terms.length;j++) {
				tempq.push("(`Content` NOT LIKE '%"+terms[j]+"%')");
			}
			contentquery += tempq.join(" AND ");
			contentquery += ")";
		}
		else if ($(this).attr("data-content") == "exclude") {
			contentquery += "(`Content` NOT LIKE '%"+$(this).attr("data-value")+"%')";
		}
		else {
			alert("highly unlikely: invalid value in data-content "+i);	
		}

		if (i < musicSearch.ContentTagCount-1) {
			contentquery += " "+$(this).attr("data-andor")+" ";
		}
		
		
		if (preview < 2) {
			tempTag.id = "Content";
			tempTag.index = i;
			tempTag.data_content = $(this).attr("data-content");
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
			musicSearch.allSearchTags.push(tempTag.serialize());
		}
		
		i++;
	});
	
	if (contentquery.length > 0) {
		query += contentquery+" ";
		qarray.push("("+contentquery+")");
	}
	
	contentquery = "";
	i=0;	
	tempq = "";
		
	$('#LocationCodesTags').children().each(function () {
		contentquery += "(`LocationCodes` "+$("#LocationCodesTag"+i).attr("data-like")+" '"+$("#LocationCodesTag"+i).attr("data-value")+"')";
		if (i < musicSearch.LocationCodesTagCount-1) {
			contentquery += " "+$("#LocationCodesTag"+i).attr("data-andor")+" ";
		}
		
		
		if (preview < 2) {
			tempTag.id = "LocationCodes";
			tempTag.index = i;
			tempTag.data_content = "*";
			tempTag.data_like = $(this).attr("data-like");
			tempTag.data_andor = $(this).attr("data-andor");
			tempTag.data_value = $(this).attr("data-value");
			tempTag.data_name = $(this).attr("data-name");
			musicSearch.allSearchTags.push(tempTag.serialize());
		}
		
		i++;
	});
	if (contentquery.length > 0) {
		query += contentquery+" ";
		qarray.push("("+contentquery+")");
	}
	
	contentquery = "";
	i=0;	
	tempq = "";
	
	/* Display the resulting SQL */
	if (qarray.length > 1) {
		// fails on CR
		$("#thequery").html(qarray.join(" AND "));
		if (preview == 0) {
			return (true);
		}
		else {
			return(false);
		}
	}
	else if (qarray.length == 1) {
		$("#thequery").html(qarray[0]);
		if (preview == 0) {
			return (true);
		}
		else {
			return(false);
		}
	}
	else {
		//$("#thequery").html("Please specify search critria.");
		return (false);
	}	
	
}


/*
This function submits the SQL query to search.php, which displays the results.
*/

function submitSearch() {
	/* If the user has already generated the SQL */
	if ($("#thequery").html().length > 1) {
		$("#q").val(encodeURIComponent($("#thequery").html()));
		$("#qdata").val(musicSearch.allSearchTags.join("[|]"));
		return true;
	}
	else {
		return false;	
	}
}


/*

This script is called when the pages loads, IFF $_SESSION["qdata"] is set, 
thereby indicating that we're coming back from search.php.

Generally, it is an analog to addFilter above, except it is not triggered by
UI events.

*/

function decodeSavedSearchTags() {
	
	for (i=0;i<musicSearch.allSearchTags.length;i++) {
		musicSearch.allSearchTags[i] = musicSearch.allSearchTags[i];	
	}
	
	/* render all the tags again */
	for (i=0;i<musicSearch.allSearchTags.length;i++) {
		
		tag = musicSearch.allSearchTags[i].split(")|(");
		
		/* Set up the proper CSS class for the AND / OR buttons. */
		var switchAND = "switchgreen";
		var switchOR = "switchgray";
		if (tag[3] == "OR") { switchAND = "switchgray"; switchOR = "switchgreen"; }
		
		/* pre-cooked HTML in variables */
		var killbutton = "<button onclick='removeFilter(\""+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"\");' class='switchred'>x</button>";
		var andorswitch = "<button id='"+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"_AND' onclick='toggleANDOR(\""+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"\", \"_AND\"); this.blur();' class='"+switchAND+"'>AND</button><button id='"+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"_OR' onclick='toggleANDOR(\""+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"\", \"_OR\"); this.blur();' class='"+switchOR+"'>OR</button>";
		
		switchLIKE = "switchgreen";
		if (tag[4].indexOf("NOT") > -1) { switchLIKE = "switchgray"; }
		var likebutton = "<button id='"+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"_NOT' onclick='toggleLIKE(\""+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"\");this.blur();' class='"+switchLIKE+"'>LIKE</button>";
		var editbutton = "";
		var datacontent = "";
		var dataname = ""; /* only used for LocationCodes */
	
		/* filer-specific modifications to the pre-cooked HTML */
		if (tag[0] == "Content") {
			likebutton = "";
			editbutton = "<button id='"+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"_EDIT' onclick=\"$('#"+tag[0]+"').val('"+tag[5]+"');$('#ContentSort').val('"+tag[2]+"');removeFilter('"+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"');\" class='switchgreen'>EDIT</button>";
			datacontent = " data-content='"+tag[2]+"' "
			var tagvalue = "<div class='tagcopy'>[ <em>"+tag[2]+"</em> ] "+tag[5]+"</div>";	
		}
	
		if (tag[0] == "Year") {
			var tagvalue = "<div class='tagcopy'>"+tag[5]+"</div>";
		}
		
		if (tag[0] == "PubColony") {
			var tagvalue = "<div class='tagcopy'>"+musicSearch.PubColonyNames[tag[5]]+"</div>";
		}
		
		if (tag[0] == "PubCity") {
			var tagvalue = "<div class='tagcopy'>"+tag[5]+"</div>";
		}
		
		if (tag[0] == "Title") {
			datacontent = " data-content='"+tag[2]+"' ";
			var tagvalue = "<div class='tagcopy'>"+tag[5]+"</div>";
		}
		
		if (tag[0] == "LocationCodes") {
			dataname = " data-name='"+tag[6]+"' ";
			var tagvalue = "<div class='tagcopy'>"+tag[6]+"</div>";	
		}
		
		if (tag[0] == "Location") {
			var tagvalue = "<div class='tagcopy'>"+tag[5]+"</div>";
		}
		
		var newTag = "<div class='subTag' id='"+tag[0]+"Tag"+eval("musicSearch."+tag[0]+"TagID")+"' "+datacontent+dataname+" data-andor='"+tag[3]+"' data-like='"+tag[4]+"' data-value='"+tag[5]+"'>"+killbutton+likebutton+tagvalue+editbutton+andorswitch+"<div style='clear:both;'></</div>";
		$("#"+tag[0]+"Tags").append(newTag);
		eval("musicSearch."+tag[0]+"TagID++");
		eval("musicSearch."+tag[0]+"TagCount++");
	}
	
	/* then clear out the allsearchtags array 
	*/
	musicSearch.allSearchTags = new Array();
}
