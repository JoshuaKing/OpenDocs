<html>
	<head>
		<script src="js/jquery-1.7.1.min.js"></script>
		
		<!-- Security Scripts -->
		<script src="js/sha/sha512.js"></script>
		<script src="js/js-aes.js"></script>
		
		<!-- Document Editing Scripts -->
		<script src="js/editor-control.js"></script>
		<script src="js/editor.js"></script>
		<script src="js/hex.js"></script>
		<script src="js/misc.js"></script>

		<!-- Styling For Editor -->
		<link rel="stylesheet" href="css/editor.css" type="text/css" />
		<link rel="stylesheet" href="css/editor-header.css" type="text/css" />
		<link rel="stylesheet" href="css/nav.css" type="text/css" />
		<link rel="stylesheet" href="css/news.css" type="text/css" />
		
		<?php require_once("php/js/view-and-new.php"); ?>
		
		<script>
			function randomString(len, charSet) {
				charSet = charSet || "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]\{}|;':\";,./<>?`~";
				var randomString = '';
				for (var i = 0; i < len; i++) {
					var randomPoz = Math.floor(Math.random() * charSet.length);
					randomString += charSet.substring(randomPoz,randomPoz+1);
				}
				return randomString;
			}
			
			
			
			$(document).ready(function() {		
				if (!checkStatus())	$(location).attr("href","./");
				saveChanges("", "", false);
				$("#doc").keydown(documentKeyDown);
			});
		</script>
		<?php require_once("php/html/analytics.php"); ?>
	</head>
	<body>
		<!-- Header -->
		<div id="locktop">
			<header>
				<div id="logged-in-as">Please Login.</div>
				<a href="./home.php">Freshte.ch Docs</a><br/>
				<span style="background-color:#6443D5">&nbsp;</span>
				<span style="background-color:#D7D86F">&nbsp;</span>
				<span style="background-color:#00C942">&nbsp;</span>
			</header>
			
			<?php require_once("php/html/editor-toolbar.php"); ?>
			<input id="doctitle" value="Untitled Document"></input>
		</div>
		<div id="pane"><div id="doc" onclick="checkStates();" onkeyup="checkStates();" contentEditable></div></div>
	</body>
</html>