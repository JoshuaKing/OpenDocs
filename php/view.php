<?php
	require("login-function.php");
	require("logging.php");
	
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
	if (!isset(	$_POST['email']) || !isset($_POST['pass']) || !isset($_POST['docid'])) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Are you trying to test this system..?";
		logRequest(false); // Log Odd Behaviour
		exit();
	}
	
	// Check User is logged in - User does not have to be logged in to view public docs //
	$userid = login($_POST['email'], $_POST['pass']);
	
	// Log View Request //
	logRequest($userid);
	
	// Connect to database //
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
	if(mysqli_connect_errno()) {
		header("HTTP/1.0 500 Internal Server Error");
		echo "SQL Error 1 encountered.";
		exit();
	}
	
	// Display the document + key //
	$stmt = $mysqli->prepare("SELECT document, title, dockey, deleted, userid, public FROM documents WHERE id=?");
	if(!$stmt) {
		header("HTTP/1.0 500 Internal Server Error");
		echo "SQL Error 2 encountered.";
		$mysqli->close();
		exit();
	}
	
	$stmt->bind_param("i", $_POST['docid']);
	$stmt->execute();
	$stmt->bind_result($document, $title, $dockey, $deleted, $docuserid, $docpublic);
	$stmt->fetch();
	$stmt->close();
	
	if (!$docpublic && !$userid) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "You must be logged in to view a document online!";
		exit();
	}
	
	// Check if user can see this file //
	if (!$docpublic && $docuserid != $userid) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Sorry, this document does not belong to you.";
		exit();
	}
	
	// Check if doc has been deleted (there is no data in 'document', 'title', 'dockey' anyway) //
	if ($deleted) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Sorry, this document has been deleted.";
		exit();
	}
	
	if (strpos($document . $dockey . $title, '|') === false) {
		$editable = $docuserid == $userid ? "1" : "0";
		$publicflag = $docpublic ? "1" : "0";
		echo $dockey . "|" . $title . "|" . $document . "|" . $editable . "|" . $publicflag;
		exit();
	} else {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Possible malicious document detected. Sorry, you cannot view this.";
		exit();
	}
?>