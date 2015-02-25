<?php session_start(); 

$dberror = 0;
# connect to database
$c = array("moadsf_master", "localhost", "moadsf", "dia4pora");
$l = mysql_connect($c[1], $c[2], $c[3]);
if ($l) { mysql_select_db($c[0],$l); $dberror = 0; }
else { $dberror = 1; }
#--------------------

if ($dberror == 0) {
	$eq = "select * from `moad_news_entities` where `live` = '1'";
	$er = mysql_query($q,$l) or die($eq." ".mysql_error());
	
	$entities = array();
	while ($e = mysql_fetch_array($er)) { $entities[] = $e;	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>

$(document).ready(function(e) {
	
<?php if ($dberror == 0): ?>

	$("#entityList").hide();
	$("#entityListOn").show();
	
	$("#entityListOn").click( function() { $('#entityList').show(); $(this).hide(); $('#entityListOff').show(); return false; });
	$("#entityListOff").click(function() { $('#entityList').hide(); $(this).hide(); $('#entityListOn').show();  return false; });
	
<?php endif; ?>

});
</script>

<style>
#entityList { width: 810px; height: 320px; overflow:auto; display: none; }
#entityListOn, #entityListOff { text-decoration: none; color: inherit; display: none; }
.entityName, .entityGraphic, .entityEdit { display: block; float: left; min-width: 200px; height: 80px; line-height: 80px; vertical-align: middle; margin-right: 5px; }
.clearit { clear: both; }
</style>

</head>

<body>
<h1>PR Entities</h1>

<?php if ($dberror == 0): ?>

<div>
<strong>Edit by name:</strong><br />
<input type="text" name="searchentity" id="searchentity" value="" />&nbsp;&nbsp;&nbsp;<button id="editentity">EDIT ENTITY</button>&nbsp;<button id="deleteentity">DELETE ENTITY</button>
</div>
<div>
<strong>Add new entity:</strong><br />
<label>* Name: </label><input type="text" name="addentity" id="addentity" value="" /><br />
<label>* Graphic: </label><input type="file" name="entityfile" id="entityfile" value="" /><br />
<label>Main URL: </label><input type="text" name="entityurl" id="entityurl" value="" /><br />
* Required<br />
</div>
<div>
<strong>Edit by list: <a id="entityListOn" href="#">&#9658;</a><a id="entityListOff" href="#">&#9660;</a></strong><br />
<div id="entityList">
<?php for ($i=0;$i<count($entities);$i++): ?>
<div class="entityName"><?php echo $entities[$i]["name"]; ?></div>
<div class="entityGraphic"><img src="images/<?php echo $entities[$i]["graphic"]; ?>" width="100" height="33" /></div>
<div class="entityEdit"><button class="editb" data-id="<?php echo $i; ?>">EDIT</button>&nbsp;<button class="deleteb" data-id="<?php echo $i; ?>">DELETE</button></div>
<div class="clearit"></div>
<?php endfor; ?>
</div>
</div>

<?php else: ?>

<h1>There was a database connection error. Please try again later.</h1>

<?php endif; ?>

</body>
</html>