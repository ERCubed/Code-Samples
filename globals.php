<?php
/* *****
* Global variables
* 	- Procedures were split out into global_procedures.php and are
*	  included at the bottom of this page.
***** */

include("dbconnect.php");

/*****
* NOTE: In the long run, the site administration values
* will need to be handled more efficiently than one
* DB Query per value
*****/

// Check to see if the site should be in maintenance mode or not.
$checkmaintenance = mysql_query("select * from siteadministration where optionname = 'maintenance'");
while ($cm = mysql_fetch_array($checkmaintenance)) {
	if ($cm['value'] == "1" && strpos($_SERVER['REQUEST_URI'],"smithing") <= 0 && strpos($_SERVER['REQUEST_URI'],"maintenance.php") <= 0) {
		header( 'Location: maintenance.php' ) ;
		exit;
	}
}

// Check the e-commerce variable. Determine if it should be displayed or not.
$checkecom = mysql_query("select * from siteadministration where optionname = 'ecommerce'");
while ($ce = mysql_fetch_array($checkecom)) {
	$showecom = $ce['value'];
}

include("session.php");

$bg1 = "#ffffff";
$bg2 = "#D9DCAD";
$bgh = "#cccc99";
$bg = $bg1;

// come up with a main location to edit the number of items to display at once on the gallery
$gallerysize = 9;

if ($_REQUEST['print'] != "1") {
	$tablewidth = "830";
} else {
	$tablewidth = "720";
}

/* *****
* Handle sorting the columns on display pages
***** */
if (isset($_REQUEST['sortby'])) {
	$sortby = $_REQUEST['sortby'];

	if ($sortby == "price") {
		$sortquery = "i.price, i.orderby, i.name";
	} elseif ($sortby == "price2") {
		$sortquery = "i.price desc, i.orderby, i.name";
	} elseif ($sortby == "piece") {
		$sortquery = "c.name, i.orderby, i.name";
	} elseif ($sortby == "piece2") {
		$sortquery = "c.name desc, i.orderby, i.name";
	} elseif ($sortby == "pattern") {
		$sortquery = "p.name, i.orderby, i.name";
	} elseif ($sortby == "pattern2") {
		$sortquery = "p.name desc, i.orderby, i.name";
	} elseif ($sortby == "manufacturer") {
		$sortquery = "m.name, i.orderby, i.name";
	} elseif ($sortby == "manufacturer2") {
		$sortquery = "m.name desc, i.orderby, i.name";
	} elseif ($sortby == "quantity") {
		$sortquery = "i.quantity asc, i.name";
	} elseif ($sortby == "quantity2") {
		$sortquery = "i.quantity desc, i.name";
	} else {
		$sortquery = "i.orderby, i.name";
	}

} else {
	$sortquery = "p.name, i.orderby, i.name";
}

$arraytype = array (
	"Silver" => "Sterling",
	"Plate" => "Silver Plate",
	"Hollow" => "Holloware",
	"China" => "China",
	"Crystal" => "Crystal",
	"Gifts" => "Gift Item",
	"Stainless" => "Stainless",
	"Jewelry" => "Jewelry",
	"null" => "Other",
);

$patterndirectory = array (
	"Silver" => "sterling/",
	"Plate" => "silverplate/",
	"Hollow" => "holloware/",
	"China" => "china/",
	"Crystal" => "crystal/",
	"Gifts" => "gifts/",
	"Stainless" => "stainless/",
	"Jewelry" => "jewelry/",
	"null" => "other",
);

$patternpage = array (
	"Silver" => "sterling.php",
	"Plate" => "silverplate.php",
	"Hollow" => "holloware.php",
	"China" => "china.php",
	"Crystal" => "crystal.php",
	"Gifts" => "gifts.php",
	"Stainless" => "stainless.php",
	"Jewelry" => "jewelry.php",
	"null" => "search.php",
);

if ($_REQUEST['pattern']) {
	$pattern = $_REQUEST['pattern'];
	$querystring = "pattern=".$pattern;
	$result = mysql_query("
		select name from pattern where id = '$pattern'
	");
	while ($row = mysql_fetch_assoc($result)) {
		$pattern_name = $row['name'];
	}
}
if ($_REQUEST['manufacturer']) {
	$manufacturer = $_REQUEST['manufacturer'];
	$querystring = "manufacturer=".$manufacturer;
	$result = mysql_query("
		select name, hallmark, other_information from manufacturer where id = '$manufacturer'
	");
	while ($row = mysql_fetch_assoc($result)) {
		$manufacturer_name = $row['name'];
		$hallmark = $row['hallmark'];
		$other_information = $row['other_information'];
	}
}
if ($_REQUEST['category']) {
	$category = $_REQUEST['category'];
	$querystring = "category=".$category;
	$result = mysql_query("
		select name from category where id = '$category'
	");
	while ($row = mysql_fetch_assoc($result)) {
		$category_name = $row['name'];
	}
}

include("global_functions.php");


?>
