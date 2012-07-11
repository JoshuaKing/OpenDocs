<?php
	require("login-function.php");
	
	// Delete a document from the database //
	// Check if user is owner of document
	// - Email (hash)
	// - Password (hash)
	// - Owner ID
	// Set 'deleted' flag, and erase encrypted key + document //
	// - Deleted flag
	// - Encrypted key
	// - Encrypted document
	
	// Check if email/password has been supplied //
	if (!isset(	$_POST['email']) || !isset($_POST['pass']) || !isset($_POST['docid'])) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Are you trying to test this system..?";
		exit();
	}
	
	// Check user is logged in //
	$userid = login($_POST['email'], $_POST['pass']);
	if (!$userid) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "You must be logged in delete a document!";
		exit();
	}
	
	// Connect to database //
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
	if(mysqli_connect_errno()) {
		header("HTTP/1.0 500 Internal Server Error");
		echo "SQL Error 1 encountered.";
		exit();
	}
	
	if ($_POST['docid'] != "") {
		// Update the document //
		$stmt = $mysqli->prepare("UPDATE documents SET document='', title='', dockey='', deleted=1, userid=0 WHERE id=? AND userid=? LIMIT 1");
		if(!$stmt) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Error 2 encountered.";
			$mysqli->close();
			exit();
	    }
		
		$stmt->bind_param("ii", $_POST['docid'], $userid);
		$stmt->execute();
		$stmt->close();
		
		// Check if document was actually updated //
		if ($mysqli->affected_rows) {
			echo "Document deleted successfully.";
			$mysqli->close();
			exit();
		} else {
			header("HTTP/1.0 500 Internal Server Error");
			echo "Could not delete document, make sure you are the correct owner of the document.";
			$mysqli->close();
			exit();
		}
	}
?>