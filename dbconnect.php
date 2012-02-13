<?

if (strpos($_SERVER['HTTP_REFERER'],"copperlamp")) {
	// Use Live Values
	$dbserver = "LIVE_SERVER_NAME";
	$dbuser = "LIVE_DB_NAME";
	$dbpass = "LIVE_PASSWORD";
} else {
	// Use Local Values
	$dbserver = "localhost";
	$dbuser = "LOCAL_DB_NAME";
	$dbpass = "LOCAL_PASSWORD";
}


if (!mysql_connect($dbserver,$dbuser,$dbpass)) {
	echo "Unable to connect: ".$TEXT['cds-error'];
	die();
}
mysql_select_db("NAME_OF_DB");
?>