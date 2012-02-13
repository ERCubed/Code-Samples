<?
/* *****
* Global Functions
***** */


/* *****
* Takes a user's ID and returns a concatenated string
* of the user's first and last names.
***** */
function getUserName($id) {
	$q = "select first_name, last_name
		from user
		where id = ".$id;
	$namearray = mysql_query($q);
	while ($n = mysql_fetch_array($namearray)) {
		$name = $n['first_name']." ".$n['last_name'];
	}
	return $name;
}

/* *****
* Takes a user's ID and returns user's first name.
***** */
function getUserFirstName($id) {
	$q = "select first_name
		from user
		where id = ".$id;
	$namearray = mysql_query($q);
	while ($n = mysql_fetch_array($namearray)) {
		$name = $n['first_name'];
	}
	return $name;
}

/* *****
* Takes a user's ID and returns user's last name.
***** */
function getUserLastName($id) {
	$q = "select last_name
		from user
		where id = ".$id;
	$namearray = mysql_query($q);
	while ($n = mysql_fetch_array($namearray)) {
		$name = $n['last_name'];
	}
	return $name;
}

/* *****
* Takes a product ID and returns the Manufacturers name
***** */
function getManufacturerName($pid) {
	$q = "select distinct m.name
		from items i, manufacturer m
		where i.pattern_id = ".$pid."
		and m.id = i.manufacturer_id
		order by m.name
		";
	$mname = "";
	$x = 1;
	$marray = mysql_query($q);

	while ($m = mysql_fetch_array($marray)) {
		if ($x == 1) {
			$mname = $m['name'];
		} else {
			$mname = $mname.", ".$m['name'];
		}
		$x++;
	}
	return $mname;
}

/* *****
* Takes a product ID and returns the item name
***** */
function getItemName($pid) {
	$q = "select name
		from items
		where id = '".$pid."'
		";
	$iarray = mysql_query($q);
	while ($i = mysql_fetch_array($iarray)) {
		$iname = $i['name'];
	}
	return $iname;
}

/* *****
* Takes a product ID and returns the item name and description
***** */
function getExtendedItemName($pid) {
	$q = "select name, description
		from items
		where id = '".$pid."'
		";
	$iarray = mysql_query($q);
	while ($i = mysql_fetch_array($iarray)) {
		if ($i['description'] == "") {
			$iname = $i['name'];
		} else {
			$iname = $i['name']." - ".$i['description'];
		}
	}
	return $iname;
}

/* *****
* Takes a product ID and returns the item price
***** */
function getItemPrice($pid) {
	$q = "select price
		from items
		where id = '".$pid."'
		";
	$iarray = mysql_query($q);
	while ($i = mysql_fetch_array($iarray)) {
		$iprice = $i['price'];
	}
	return $iprice;
}

/* *****
* Takes a Pattern ID and returns the Production Information
***** */
function getProductionInformation($pid) {
	$q = "select produced
		from pattern
		where id = '".$pid."'
		";
	$pname = "";
	$parray = mysql_query($q);

	while ($p = mysql_fetch_array($parray)) {
		$pname = $p['produced'];
	}

	return $pname;
}

/* *****
* Takes a Pattern ID and returns the Product Image
***** */
function getPatternImage($pid) {
	global $patterndirectory;

	$q = "select image, description
		from pattern
		where id = '".$pid."'
		";
	$pname = "";
	$parray = mysql_query($q);

	while ($p = mysql_fetch_array($parray)) {
		$pimage = $patterndirectory[$p['description']].$p['image'];
	}

	return $pimage;
}

/* *****
* Takes a piece ID and returns the associated name
***** */
function getPieceName($pid) {
	$q = "select * from category
		where id = '".$pid."'
		and status = 'ACTIVE'
		order by name
	";
	$pname = "";
	$parray = mysql_query($q);

	while ($p = mysql_fetch_array($parray)) {
		$pname = $p['name'];
	}

	return $pname;
}

/* *****
* Takes a pattern name and strips off the NEW and ESTATE
* clarification so it can be used in more general queries
***** */
function cleanName($name) {
	$n = str_replace(" - ESTATE", "", $name);
	$n = str_replace(" - NEW", "", $n);
	return $n;
}

/* *****
* Takes a pattern name and checks to see if there is any
* NEW and/or ESTATE associated with it.
***** */
function checkForNewEstate($name, $type) {
	$n = cleanName($name);
	$checkQuery = "
		select * from pattern
		where name like '%".$n."%'
		and name not like '%OLD MARK%'
		and description = '".$type."'
	";
	$result = mysql_query($checkQuery);
	$x = 1;
	$check = "";
	while ($r = mysql_fetch_array($result)) {
		if ($r['is_new'] == 1) {
			$check = $check."NEW";
		} else {
			$check = $check."ESTATE";
		}
		$x++;
	}
	return $check;
}

/* *****
* Takes an ID and ID identifier to determine the appropriate
* last-update / revised date stamp and returns the
* last updated meta tag.
***** */
function getLastUpdated ($id, $idtype) {
	switch ($idtype) {
		case "p":
			$wc = "where pattern_id = '".$id."'";
			break;
		case "m":
			$wc = "where manufacturer_id = '".$id."'";
			break;
		case "c":
			$wc = "where category_id = '".$id."'";
			break;
	}
	$query = "
		select max(last_modified) as lm
		from items ".$wc;
	$result = mysql_query($query);
	$updated = "";
	while ($r = mysql_fetch_array($result)) {
		$updated = "<META HTTP-EQUIV='Last-Update' CONTENT='".$r['lm']." GMT'>";
	}
	return $updated;
}

/* *****
* Takes a pattern ID and returns the appropriate keywords for the
* item types we have listed on the site (based off orderby column values)
***** */
function getItemKeywords ($id) {
	$query = "
		select distinct i.orderby, i.type, p.is_new
		from items i, pattern p
		where i.pattern_id = '".$id."'
		and i.pattern_id = p.id
		order by i.orderby asc
		";
	$result = mysql_query($query);
	$keywords = "";
	$loop = "1";
	while ($r = mysql_fetch_array($result)) {
		if ($loop == "1") {		// We only want to have this keyword once...
			if ($r['is_new'] == '0') {
				$keywords .= "Estate, ";
			} else {
				$keywords .= "New, ";
			}
		}
		if ($r['type'] == "Silver" || $r['type'] == "Plate" || $r['type'] == "Stainless") {
			switch ($r['orderby']) {
				case 1:
					$keywords .= "Place Setting, ";
					break;
				case 2:
					$keywords .= "Place Piece, Fork, Spoon, Knife, ";
					break;
				case 3:
					$keywords .= "Serving Piece, ";
					break;
				case 4:
					$keywords .= "Complete Set, ";
					break;
			}
		} else if ($r['type'] == "China") {
			switch ($r['orderby']) {
				case 10:
					$keywords .= "Place Setting, ";
					break;
				case 11:
					$keywords .= "Place Piece, Dinner Plate, Salad Plate, Bread and Butter, Tea Cup, ";
					break;
				case 12:
					$keywords .= "Place Accessory, ";
					break;
				case 13:
					$keywords .= "Serving Piece, ";
					break;
			}
		} else {
			$keywords = $r['type'].", ";
		}
	$loop++;
	}
	return $keywords;
}


/* *****
* Sanitize any information about to be added to a SQL Query
***** */
function sql_quote( $value ) {
	if( get_magic_quotes_gpc() ) {
		  $value = stripslashes( $value );
	}
	//check if this function exists
	if( function_exists( "mysql_real_escape_string" ) ) {
		  $value = mysql_real_escape_string( $value );
	} else {
		//for PHP version < 4.3.0 use addslashes
		  $value = addslashes( $value );
	}
	return $value;
}


function parse_datetime($datetime) {
     $currentTime = time();
     $offset = date("Z", $currentTime);

     $matches = array();

     // Check to see if we're dealing with a UTC dateTime (ends in 'Z')
     // or if there's an offset specified (ends in '[+-]hh:mm’).
     if(preg_match("/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})([+-])(\d{2}):(\d{2})$/",
          $datetime, $matches) === 1) {
     	// Offset specified.
     	$dateString = $matches[1];

     	// Calculate the custom offset.
     	$customOffset = $matches[3] * 60 * 60;
     	$customOffset += $matches[4] * 60;

     	// Invert the custom offset as necessary.
	if($matches[2] == "+") {
     		$customOffset = -1 * $customOffset;
     	}

     	// Add the custom offset to the UTC offset to get the offset
     	// from the local timezone.
     	$offset += $customOffset;
     }
     else if(preg_match("/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})Z$/",
           $datetime, $matches) === 1) {
     	// Using the UTC timezone.
     	$dateString = $matches[1];
     }

     // Parse the date and time portion of the string.
     $datetimeArray = strptime($dateString, "%Y-%m-%dT%H:%M:%S%");

     // Generate the UNIX time. Note that this will be in the wrong timezone.
     $time = mktime($datetimeArray['tm_hour'],
     		$datetimeArray['tm_min'],
     		$datetimeArray['tm_sec'],
     		$datetimeArray['tm_mon'] + 1,
		$datetimeArray['tm_mday'] ,
     		$datetimeArray['tm_year'] + 1900);

      // Return the calculated UNIX time from above along with the offset
      // necessary to correct for the timezone specified.
      return $time + $offset;
}

?>