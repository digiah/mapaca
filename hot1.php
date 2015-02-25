<?php session_start(); $_SESSION["q"] = "((`PubColony` LIKE '%MA%') OR (`PubColony` LIKE '%CT%') OR (`PubColony` LIKE '%RI%') OR (`PubColony` LIKE '%VT%') OR (`PubColony` LIKE '%NH%')) AND ((( `Content` LIKE '%bells%' )) AND ((`Content` LIKE '%ring%') OR (`Content` LIKE '%rang%') OR (`Content` LIKE '%rung%') OR (`Content` LIKE '%toll%') OR (`Content` LIKE '%tolled%'))) AND ((`LocationCodes` LIKE 'n%') AND (`LocationCodes` NOT LIKE 'nbn'))";
#$_SESSION["q"] = "((`Year` >= 1714)) AND (((`Content` LIKE '%bells%')))";
$_SESSION["pre"] = true;
header("location:search.php"); ?>