<?php
	require("login-function.php");
	
	// Send document to the database //
	// Check if 'public' flag is set //
	// - Public flag
	// If set, return document //
	// Check if user is owner of document
	// - Email (hash)
	// - Password (hash)
	// - Owner ID
	// If not, return error //
	// Otherwise, return encrypted document+encrypted key //
	// - Encrypted key
	// - Encrypted Document
	
	// Check if email/password has been supplied //
	if (!isset(	$_POST['email']) || !isset($_POST['pass'])) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Are you trying to test this system..?";
		exit();
	}
	
	// Check user is logged in //
	$userid = login($_POST['email'], $_POST['pass']);
	if (!$userid) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "You must be logged in to view a document online!";
		exit();
	}
	
	// Connect to database //
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
	if(mysqli_connect_errno()) {
		header("HTTP/1.0 500 Internal Server Error");
		echo "SQL Error 1 encountered.";
		exit();
	}
	
	// Display the document + key //
	$stmt = $mysqli->prepare("SELECT id, title, dockey FROM documents WHERE userid=?");
	if(!$stmt) {
		header("HTTP/1.0 500 Internal Server Error");
		echo "SQL Error 2 encountered.";
		$mysqli->close();
		exit();
	}
	
	$stmt->bind_param("i", $userid);
	$stmt->execute();
	$stmt->bind_result($docid, $title, $dockey);
	while ($stmt->fetch() ) {
		if (strpos($dockey . $title, '|') === false && strpos($dockey . $title, ';') === false) {
			echo "$docid|$dockey|$title;";
		} else {
			header("HTTP/1.0 418 I'm a teapot");
			echo "Possible malicious document detected. Sorry, you cannot view this.";
			exit();
		}
	}
	$stmt->close();
?>