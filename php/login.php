<?php
	require("login-function.php");
	require("logging.php");
	
	// Check user against database //
	// - Email (hash)
	// - Password (hash)
	
	// Check if email/password has been supplied //
	if (!isset(	$_POST['email']) || !isset($_POST['pass'])) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Are you trying to test this system..?";
		exit();
	}
	
	// Run loggin
	$loggedin = login($_POST['email'], $_POST['pass']);
	
	// Log Login Request //
	logRequest($loggedin);
	
	if ($loggedin) {
		echo "Logged in successfully.";
	} else {
		header("HTTP/1.0 418 I'm a teapot");
		echo "User either does not exist, or password is incorrect.";
		exit();
	}
?>