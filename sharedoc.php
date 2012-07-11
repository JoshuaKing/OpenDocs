<?php
	require("login-function.php");
	
	// Set a document as public in database //
	// Check if user is owner of document
	// - Email (hash)
	// - Password (hash)
	// - Owner ID
	// Set 'public' flag //
	// - Public flag
	
	// Check if email/password has been supplied //
	if (!isset(	$_POST['email']) || !isset($_POST['pass']) || !isset($_POST['docid']) || !isset($_POST['public'])) {
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
		echo "SQL Error 1 encounted.";
		exit();
	}
	
	$stmt = $mysqli->prepare("UPDATE documents SET public=? WHERE id=? AND userid=? LIMIT 1");
	if(!$stmt) {
		header("HTTP/1.0 500 Internal Server Error");
		echo "SQL Error 2 encounted.";
		$mysqli->close();
		exit();
    }
	
	$stmt->bind_param("iii", intval($_POST['public']), $_POST['docid'], $userid);
	$stmt->execute();
	$stmt->close();
	
	// Check if document was actually updated //
	if ($mysqli->affected_rows) {
		echo "Document is now " . ($_POST['public'] == "1" ? "Public" : "Private");
		$mysqli->close();
		exit();
	} else {
		header("HTTP/1.0 500 Internal Server Error");
		echo "Could not update document access state, make sure you are the correct owner of the document.";
		$mysqli->close();
		exit();
	}
?>