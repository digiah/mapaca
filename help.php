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
	$("#hot1").click(function(e) {
        document.location.href="hot1.php";
    });
	$("#home").click(function(e) {
        document.location.href="index.php";
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

.leadimage {
	border: 1px solid #e15c11;
	padding: 3px;
	overflow: hidden;	
}
.emph { font-weight: bold; color: #e15c11; }

#key {
	background-color: #f4d7b7;color: #000; padding: 5px; width: 300px; margin: auto;
	font-family: 'Crimson Text', serif;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	margin-bottom: 15px;
	background-color:#F0EEEA;
			
}
</style>
  </head>
  <body>
    <div class="container">
      <h1 style="float: left;"><span id="home">MAPACA</span> <span style="font-size: 0.5em;"
          class="creamcopy">Music and the Performing Arts in Colonial America</span></h1>
      <div style="clear: both;"></div>
      <h1>Help</h1>
      <p>The first thing to know about searching <span class="orangecopy">MAPACA</span>
        is that it makes use of multiple fields that help you drill down on
        specific tables in the database. This is not a single-field search
        engine, but works along the lines of Google's <a target="new" href="http://www.google.com/advanced_search">advanced
          search</a> options. If this page does not answer your questions, try
        the <a href="http://www.hawaii.edu/arthum/digital/forum/index.php?board=2.0"
          title="MAPACA Forum">MAPACA forum</a>.</p>
      <p>The search fields are divided into two columns. On the <span class="emph">left</span>
        are the full text content search <span class="emph">( 1 )</span>, and
        filters that narrow your search according to information about the
        publication such as the date range of newspapers to search <span class="emph">(
          2 )</span>, publication colonies <span class="emph">( 3 )</span> and
        cities <span class="emph">( 4 )</span>, and newpaper titles <span class="emph">(
          5 )</span>, so for example, you could search for all occurrences of
        the word "bells" in newspapers published between 1755 and 1766 in
        Massachusetts not including Boston, or all occurrences in the <em>Pennsylvania
          Gazette</em>. The content field <span class="emph">( 1 )</span> has a
        powerful drop-down menu that when combined with <span class="emph">chaining</span>
        (see below) allows complex full text searches within the content of
        articles. </p>
      <p>The <span class="emph">right</span> column presents the geographic
        location of the <em>events</em> that take place in the articles, for
        example bells <em>ringing in England</em>. The first group ("referred
        region") allows you to filter for events by region <span class="emph">(
          11 )</span>. North America and Canada can also be filtered by
        sub-regions and European political affiliation by expanding the menus,
        for example New England, or French Canada. "Referred location" <span class="emph">(
          12 )</span> is a specific town or city where the event took place. </p>
      <p>SQL queries can be previewed by clicking "SEE SQL" <span class="emph">(
          6 )</span>, searches are executed with the "SEARCH" button <span class="emph">(
          7 )</span>, and the query builder can be reset via the "RESET" buton <span
          class="emph">( 8 )</span>.
      </p>
      <p>Users can SAVE their searches for later modification by clicking "SAVE"
        <span class="emph">( 9 )</span>. This will download a .mapaca file to
        the user's computer. To work with a saved .mapaca file, click "LOAD" <span
          class="emph">( 10 )</span>. The user will be prompted to upload a
        previously-downloaded .mapaca file which, if successfully uploaded, will
        populate the interface with saved queries.
      </p>
      <p><img style="width:100%;height:auto;" src="images/mapaca-interface.jpg" />
      </p>
      <p>You can <span class="emph">chain</span> multiple search terms by
        clicking the plus sign <img width="37" height="18" src="images/plus-sign.jpg" />next
        to the appropriate fields. </p>
      <p><img width="302" height="57" src="images/chain-sample.jpg" /> </p>
      <p>Each chained search item has a set of options: </p>
      <p>Each item can be deleted with the X, edited, or toggled between AND and
        OR relationship with the following item in the chain. The example above
        (working with the CONTENT field) would search for the <em>exact phrase</em>
        "british sailors" <strong>AND</strong> the term "fiddle." Making
        subsequent specifications on fields such as year, colony or title would
        hone the results. All groups of chains are joined by an AND operation
        when submitted to the search engine. </p>
      <p>All text fields except for CONTENT make use of look-ahead typing,
        facilitating quick drills on the existing data sets.</p>
      <p>Another way to think about the data relationships is to consider Figure
        1. The location of the publication is somewhere in the colonies and its
        citations refer to somewhere in the world. The MAPACA search fields tie
        these references together, as seen in Figure 2.
      </p>
      <div class="leadimage"> <img style="width:100%;height:auto;" src="images/lead-help.jpg" /><br />
        Figure 1. </div>
      <p></p>
      <div class="leadimage"> <img style="width:100%;height:auto;" src="images/help-key.jpg" /><br />
        Figure 2.<br />
        <div id="key">
          <table width="200" cellspacing="0" cellpadding="5" border="0" style="margin: auto;">
            <tbody>
              <tr>
                <td colspan="8">Key</td>
              </tr>
              <tr>
                <td style="background-color:#EB212D;width: 12px; height: 12px;"> </td>
                <td style="background-color:#FFF;border: 3px solid #EB212D;padding: 5px;">Publication</td>
                <td> </td>
                <td style="background-color:#7E4296;width: 12px; height: 12px;"> </td>
                <td style="background-color:#FFF;border: 3px solid #7E4296;padding: 5px;">Title</td>
                <td> </td>
                <td style="background-color:#2F3490;width: 12px; height: 12px;"> </td>
                <td style="background-color:#FFF;border: 3px solid #2F3490;padding: 5px;">Year</td>
              </tr>
              <tr>
                <td style="background-color:#149348;width: 12px; height: 12px;"> </td>
                <td style="background-color:#FFF;border: 3px solid #149348;padding: 5px;">City</td>
                <td> </td>
                <td style="background-color:#E05E30;width: 12px; height: 12px;"> </td>
                <td style="background-color:#FFF;border: 3px solid #E05E30;padding: 5px;">Content</td>
                <td> </td>
                <td style="background-color:#FEDC3A;width: 12px; height: 12px;"> </td>
                <td style="background-color:#FFF;border: 3px solid #FEDC3A;padding: 5px;">Colony</td>
              </tr>
              <tr>
                <td style="background-color:#000;width: 12px; height: 12px;"> </td>
                <td style="background-color:#FFF;border: 3px solid #000;padding: 5px;">Location</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <p>Once you have a set of results you want to further analyze, you can
        download them from the results page as a CSV file which can be imported
        into excel, R, or the statistical package of your choice. Future
        versions of the database will incorporate some of these functions along
        with visualizations into the web interface.</p>
      <p>
        Here are is an example of a complex search to get an idea of the power
        of the search interface. <br />
      </p>
      <p>Find all articles relating to bells being rung that were <strong>published
          in New England</strong> and <strong>refer to bells rung in North
          America, excluding New England</strong>. This is an indicator of
        inter-regional solidarity as it changed over time. Compare with all
        bells reports published in NE about bells rung in NE, and same for bells
        rung in Old England. Change over time indicates increasing association
        with other colonies over time and decreasing association with London,
        with intra-regional (i.e. bells rung in NE and published in NE)
        remaining constant. This could be used as an indicator for emerging
        American identity. You could then run the search on the whole database
        rather than just on bells as a comparison.<br />
        <button id="hot1" class="dashbutton">SAMPLE SEARCH</button></p>
      <p> <button id="getstarted" class="dashbutton">GET STARTED</button>
      </p>
    </div>
  </body>
</html>
