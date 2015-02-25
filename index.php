<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html
  xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
    <title>Welcome to Mapaca</title>
    <link type="text/css" rel="stylesheet" href="search-citation.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="support.js" language="javascript"></script>
    <script>

$(document).ready(function(e) {
	$("#getstarted").click(function(e) {
        document.location.href="search.php";
    });
	$("#gethelp").click(function(e) {
        document.location.href="help.php";
    });
});

</script>
    <style>

@import url(http://fonts.googleapis.com/css?family=Crimson+Text|Averia+Libre);

.container {
	min-width: 4in;
	max-width: 8in;
	margin: auto;
	margin-top: 25px;
	padding: 15px;
	border: 1px solid #e15c11;
	font-family: 'Crimson Text', serif;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;	
}
.smaller {
	font-size: 0.85em;	
}

#leadimage {
	border: 1px solid #e15c11;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	overflow: hidden;	
}

</style>
  </head>
  <body>
    <div class="container">
      <h1>Welcome to MAPACA</h1>
      <h2 style="margin-top: -25px;">Music and the Performing Arts in Colonial
        America</h2>
      <p><span class="orangecopy">MAPACA</span> is database of more than 53,000
        newspaper articles, advertisements, and illustrations that refer to or
        include music, poetry, or the performing arts in British North America
        between 1704 and 1783. Besides the text, each entry has location
        information about both the publisher and the event reported on, so for
        example, you can ask how many times Boston newspapers reported on bells
        being rung in New York in the wake of the Stamp Act's repeal in 1766, or
        how many times Philadelphia reported on plays performed in other
        colonies or London, mapping the change in frequency between 1704 and
        1783. These sorts of queries can be used, for example, to ask questions
        about the extent of the network of print and the timing of its emergence
        into what Benedict Anderson calls the imagined community in the decades
        before and during the American Revolution. To master the search
        interface's capabilities, we encourage you to read the <a href="http://www.hawaii.edu/arthum/digital/mapaca/help.php"
          title="Help">help file</a>.  Additional support is available from the
        <a href="http://www.hawaii.edu/arthum/digital/forum/index.php?board=2.0">Forum</a>,
        where you can report bugs and suggest features as well.</p>
      <p>This is the 1.0 release. Advanced queries are possible, but the data is
        returned as a Google-like list of results. The results can be downloaded
        as a CSV file which can then be analyzed using spreadsheet, statistical,
        and data mining software. Searches can be saved and uploaded for later
        use as well.  Contingent on continued support, we plan on incorporating
        cross-tabulated tables and graphing and visualization features for the
        2.0 release.</p>
      <p>Note on Scope: The use of "America" is aspirational at this point.  The
        original data set was compiled as part of a federal US grant, thus the
        current data is solely a subset of British North American newspapers
        limited anachronistically to the thirteen colonies that would become the
        United States. We hope in the future to expand the data set to include
        the Americas proper, with Canada, the Caribbean, and Central and South
        America represented, but did not want to delay release. The colonial
        moniker holds, since all the presses at this time were in colonies. 
        Plus we like the sound of "MAPACA."</p>
      <div id="leadimage"> <img style="width:100%;height:auto;" src="images/lead-help.jpg" />
      </div>
      <button id="getstarted" class="dashbutton">GET STARTED</button>
      <button id="gethelp" class="dashbutton">HELP</button>
      <p style="border-top: 1px solid #e15c11;padding-top:15px;" class="smaller">The
        database was originally compiled by Mary Jane Corry, Kate Van Winkle
        Keller, and Robert M. Keller and released as a CD, <a target="_blank" href="http://www.colonialmusic.org/PAC-cdr.htm">The
          Performing Arts in Colonial American Newspapers, 1690-1783</a> (New
        York: University Music Editions, 1997). The full list of people who
        worked on the CD version can be found in the <a href="http://www.colonialdancing.org/PacanNew/UsersGuide.pdf">user
          guide</a> for their <a target="_blank" href="http://www.colonialdancing.org/PacanNew/Index.htm">web
          site</a>. This interface and the port to a web-based MySQL database is
        a project of the <a target="_blank" href="http://www.hawaii.edu/arthum/digital/">Digital
          Arts and Humanities Initiative</a> at the University of Hawaiʿi at
        Mānoa. <a target="_blank" href="http://www2.hawaii.edu/%7Errath">Richard
          Cullen Rath</a> ported the database to MySQL and designed a user
        interface with David Goldberg, who programmed the new interface. This
        new work was made possible with the generous support of the <a target="_blank"
          href="http://www.hawaii.edu/arthum/">College of Arts and Humanities</a>
        at UHM.</p>
    </div>
  </body>
</html>
