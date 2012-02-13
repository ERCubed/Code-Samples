<?
include_once "globals.php";

$pagetitle = "Create Account";

if (isset($_POST['username'])) {
	// Check the values
	$errcount = 0;
	
	if ($errcount == 0) {
		// If they're good, add them to the database and thank the user.
		setcookie('userid', $_POST['username'], time() + 1209600); // Set Cookie for two weeks out.		
		$salt = generateSalt($_POST['username']);
		$hash = generateHash($_POST['password'], $_POST['username']);

		$insertsql = "insert into users (username, hash, salt, first_name, last_name, account_type, status, created, email)
		values (
			'".$_POST['username']."', 
			'".$hash."', 
			'".$salt."', 
			'".$_POST['first_name']."', 
			'".$_POST['last_name']."', 
			'user',
			'new', 
			'".date('Y-m-d H:i:s')."', 
			'".$_POST['email']."'
		)";
	
		if (mysql_query($insertsql)) {
			$reg_msg = "<b>Thank you for registering, ".$_POST['first_name']."</b>";
		} else {
			$reg_msg = "<b> There was an issue creating a new user.</b>";
			$failed++;
		}

		
	} else {
		// Display errors and redisplay the form.
	}

} else {
	// New user registration. Display the form.
}


include_once "header.php";
//echo session_start();

include "page_header.php";
?>

<h3><? echo $pagetitle; ?></h3>

<? 
if (isset($_POST['username'])) {
	
	if ($errcount == 0) {
		echo $reg_msg;
		
	} else {
		// Display errors and redisplay the form.
		include "_create_account_form.php";
	}

} else {
	// New user registration. Display the form.
	include "_create_account_form.php";

}
?>

<? 
include "page_footer.php";
?>
