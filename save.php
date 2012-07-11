<?php
	require("login-function.php");
	
	// Save a document to the database //
	// - Owner ID
	// - Document ID (if none, insert new)
	// - Encrypted key
	// - Encrypted Document
	
	// Check if email/password has been supplied //
	if (!isset(	$_POST['email']) || !isset($_POST['pass']) || !isset($_POST['doc']) || !isset($_POST['title'])) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Are you trying to test this system..?";
		exit();
	}
	
	// Check user is logged in //
	$userid = login($_POST['email'], $_POST['pass']);
	if (!$userid) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "You must be logged in to save a document online!";
		exit();
	}
	
	// Connect to database //
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
	if(mysqli_connect_errno()) {
		header("HTTP/1.0 500 Internal Server Error");
		echo "SQL Error 1 encounted.";
		exit();
	}
	
	if (isset($_POST['docid']) && $_POST['docid'] != "") {
		// Update the document //
		$stmt = $mysqli->prepare("UPDATE documents SET document=?, title=? WHERE id=? AND userid=? LIMIT 1");
		if(!$stmt) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Error 2 encounted.";
			$mysqli->close();
			exit();
	    }
		
		$stmt->bind_param("ssii", $_POST['doc'], $_POST['title'], $_POST['docid'], $userid);
		$stmt->execute();
		$stmt->close();
		
		// Check if document was actually updated //
		if ($mysqli->affected_rows) {
			echo "Document updated successfully.";
			$mysqli->close();
			exit();
		} else {
			header("HTTP/1.0 500 Internal Server Error");
			echo "Could not save document, make sure you are the correct owner of the document.";
			$mysqli->close();
			exit();
		}
	} else if (isset($_POST['key'])) {
		// Create new document //
		$stmt = $mysqli->prepare("INSERT INTO documents (created, document, userid, dockey, title) VALUES (NOW(), ?, ?, ?, ?)");
		if(!$stmt) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Error 3 encounted.";
			$mysqli->close();
			exit();
	    }
		
		$stmt->bind_param("siss", $_POST['doc'], $userid, $_POST['key'], $_POST['title']);
		if (!$stmt->execute()) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "Could not create document.";
			$mysqli->close();
			exit();
		}
		
		$docid = $mysqli->insert_id;
		$stmt->close();
		echo "$docid|Successfully created document #{$docid}.";
		$mysqli->close();
		exit();
	} else {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Are you trying to test this system..?";
		exit();
	}
?>