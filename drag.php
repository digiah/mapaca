<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<style>
#foo { margin: 10px; border: 5px solid #F00; width: 100px; height: 100px; display: block; }
#bar { margin: 10px; border: 5px solid #F00; width: 100px; height: 100px; display: block; }
.draggy { margin: 2px; padding: 2px; background-color:#9C0; }
</style>
<script>

function drag(e) { e.dataTransfer.setData("Text",e.target.id); }

function allowDrop(e) { e.preventDefault(); }

function drop(e) { 
	e.preventDefault();
	var data=e.dataTransfer.getData("Text");
	document.getElementById("bar").appendChild(document.getElementById(data));
}

</script>
</head>

<body>
<div id="foo">
<div class="draggy" id="drag1" draggable="true" ondragstart="drag(event);">woo fa!</div>
<div class="draggy" id="drag2" draggable="true" ondragstart="drag(event);">foo wa!</div>
<div class="draggy" id="drag3" draggable="true" ondragstart="drag(event);">boo sa!</div>
<div class="draggy" id="drag4" draggable="true" ondragstart="drag(event);">soo ba!</div>
</div>

<div id="bar" ondrop="drop(event);" ondragover="allowDrop(event);">
</div>
</body>
</html>