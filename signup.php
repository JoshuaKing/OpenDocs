<?php
	require("login-function.php");
	
	// Add user to database //
	// - Email (hash)
	// - Password (hash)
	
	// Check if email/password has been supplied //
	if (!isset(	$_POST['email']) || !isset($_POST['pass'])) {
		header("HTTP/1.0 418 I'm a teapot");
		echo "Are you trying to test this system..?";
		exit();
	}
	
	// Connect to database //
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
	if(mysqli_connect_errno()) {
	  header("HTTP/1.0 500 Internal Server Error");
	  echo "SQL Error 1 encounted.";
      exit();
   }
   
   // Check if a user already exists with that email //
   $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE email=?");
   if(!$stmt) {
	  header("HTTP/1.0 500 Internal Server Error");
	  echo "SQL Error 2 encounted.";
      exit();
   }
   
   $stmt->bind_param("s", $_POST['email']);
   $stmt->execute();
   $stmt->bind_result($rows);
   $stmt->fetch();
   $stmt->close();
   if ($rows) {
	  header("HTTP/1.0 418 I'm a teapot");
	  echo "User with that account already exists, please choose another.";
      exit();
   }
   
   // Insert user into database //
   $stmt = $mysqli->prepare("INSERT INTO users SET email=?, password=?");
   if(!$stmt) {
	  header("HTTP/1.0 500 Internal Server Error");
	  echo "SQL Error 3 encounted.";
      exit();
   }
   
   $stmt->bind_param("ss", $_POST['email'], $_POST['pass']);
   $stmt->execute();
   
   // Close connections/statements //
   $stmt->close();
   $mysqli->close();
   echo "Successfully added user.";
?>