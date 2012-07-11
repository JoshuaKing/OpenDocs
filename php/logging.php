<?php
	require_once("class.browser.php");
	
	// Logging //
	// - Could be quite useful to provide stats to users of documents
	// - Can also see which document recieves the most views
	// - Which users are actually active
	// - Where optimisations should be focused
	// - Possible attempts at attacks
	
	// Note:
	// Users should expect logging (besides, the hoster has logs anyway) -
	// we record *visits* NOT their data (it is impossible)
	
	
	function language() {
		// Note: This code was modified from http://www.thefutureoftheweb.com/blog/use-accept-language-header //
		$langs = array();
		
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
			
			if (count($lang_parse[1])) {
				// create a list like "en" => 0.8
				$langs = array_combine($lang_parse[1], $lang_parse[4]);
				
				// set default to 1 for any without q factor
				foreach ($langs as $lang => $val) {
					if ($val === '') $langs[$lang] = 1;
				}
				
				// sort list based on value	
				arsort($langs, SORT_NUMERIC);
			}
		}
		
		return $langs;
	}
	
	function logRequest($userid) {
		global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;
		
		// Connect to database //
		$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
		if(mysqli_connect_errno()) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Log Error 1 encountered.";
			return false;
		}
		
		$S = $_SERVER;
		if (!$userid) $userid = 0;
		$language = array_keys(language());
		$language = $language[0];
		ini_set("ini.browscap", "lite_php_browscap.ini");
		$browser = new browser();
		$browsername = $browser->browser['browser']['title'];
		$browserversion = $browser->browser['browser']['version'];
		$os = $browser->browser['os']['title'];
		$osversion = $browser->browser['os']['version'];
		
		$ip = $S['REMOTE_ADDR'];	// Will always have a value for this
		$host = $S['REMOTE_HOST'] ? $S['REMOTE_HOST'] : "";
		$ref = $S['HTTP_REFERER'] ? $S['HTTP_REFERER'] : "";
		$ua = $S['HTTP_USER_AGENT'] ? $S['HTTP_USER_AGENT'] : "";
		$method = $S['REQUEST_METHOD'] ? $S['REQUEST_METHOD'] : "";
		$uri = $S['REQUEST_URI'] ? $S['REQUEST_URI'] : "";
		$query = $S['QUERY_STRING'] ? $S['QUERY_STRING'] : "";
		
		
		// Log to the database //
		$stmt = $mysqli->prepare("INSERT INTO logs"
				. " (ip, host, uri, query, method, referrer, useragent, browser, browserversion, os, osversion, primarylanguage)"
				. " VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		if(!$stmt) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Log Error 2 encountered.";
			$mysqli->close();
			return false;
	    }
	    
	    $stmt->bind_param("ssssssssssss", $ip, $host, $uri, $query, $method, $ref, $ua, $browsername, $browserversion, $os, $osversion, $language);
	    $res = $stmt->execute();
	    if(!$stmt) {
			header("HTTP/1.0 500 Internal Server Error");
			echo "SQL Log Error 3 encountered. [" . $mysqli->error . "]";
		    $stmt->close();
			$mysqli->close();
			return false;
	    }
	    $stmt->close();
	    $mysqli->close();
		return true;
	}
?>