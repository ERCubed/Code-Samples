<?
// local values: "localhost", "root", ""
// live values: "p50mysql211.secureserver.net","copperlampdb","aSimple1"

if (strpos($_SERVER['HTTP_REFERER'],"copperlamp")) {
	// Use Live Values
	$dbserver = "p50mysql211.secureserver.net";
	$dbuser = "copperlampdb";
	$dbpass = "aSimple1";
} else {
	// Use Local Values
	$dbserver = "localhost";
	$dbuser = "copperlampdb";
	$dbpass = "aSimple1";
}


if (!mysql_connect("localhost","copperlampdb","aSimple1")) {
	echo "Unable to connect: ".$TEXT['cds-error'];
	die();
}
mysql_select_db("copperlampdb");
?>