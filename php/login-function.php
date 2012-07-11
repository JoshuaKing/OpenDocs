<?php
	require_once("mysql-details.php");
	
	function login($user, $pass) {
		global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
		
		// Connect to database //
		$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
		if(mysqli_connect_errno()) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Error 1 encountered.";
			return false;
		}
	   
		// Check if a user already exists with that email //
		$stmt = $mysqli->prepare("SELECT id FROM users WHERE email=? AND password=? LIMIT 1");
		if(!$stmt) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Error 2 encountered.";
			$mysqli->close();
			return false;
	    }
	   
	    $stmt->bind_param("ss", $user, $pass);
	    $stmt->execute();
	    $stmt->bind_result($rows);
	    $stmt->fetch();
	    $stmt->close();
	    if (!$rows) {
			$mysqli->close();
			return false;
		}
		
	    // Close connections/statements //
	    $mysqli->close();
		return $rows;
    }

?>