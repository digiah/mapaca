<?php session_start();
# get the uploaded file
if ($_FILES["mapacafile"]["error"] == 0) {
	# make sure it's actually a csv file
	if (!strstr($_FILES["mapacafile"]["name"],".mapaca")) {
		$_SESSION["fileuploadmessage"] = "Please upload a .mapaca file, not a file of type \"".$_FILES["mapacafile"]["type"]."\".";
	}
	else {
		if (!move_uploaded_file($_FILES["mapacafile"]["tmp_name"], "csv/".$_FILES["mapacafile"]["name"])) {
			$_SESSION["fileuploadmessage"] = "Could not move uploaded file.";
		}
		else {
			$file = fopen("csv/".$_FILES["mapacafile"]["name"],"r");
			$_SESSION["qdata"] = fgets($file);
			unlink("csv/".$_FILES["mapacafile"]["name"]);
			fclose($file);
		}	
	}
}
else {
	$_SESSION["fileuploadmessage"] = "There was an error uploading the file.";
}

header("location:search.php");

?>
