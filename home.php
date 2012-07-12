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
		<link rel="stylesheet" href="css/editor-header.css" type="text/css" />
		<link rel="stylesheet" href="css/nav.css" type="text/css" />
		<link rel="stylesheet" href="css/news.css" type="text/css" />
		
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
			
			function checkStatus() {
				var key = localStorage.getItem("key");
				var hemail = localStorage.getItem("email");
				var hpass = localStorage.getItem("pass");
				var email = localStorage.getItem("pt-email");
				
				// Mark user as logged in if details are currently stored in localStorage //
				if (key != null && email != null && hpass != null && hemail != null) {
					// User is logged in //
					$("#login").hide();
					
					// Show 'logged in as ___' code //
					var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
					if (localStorage["pt-email"].search(emailRegEx) == -1) {
						$("#logged-in-as").html("<div>Logged in.</div>");
					} else {
						$("#logged-in-as").html("<div>Logged in as " + localStorage["pt-email"] + ".</div>");
					}
					$("#logged-in-as").append("<input type='button' onclick='logout();' value='Logout'/>");
					
					// Update appearance //
					populate("doclist");
					$("#doclist").css("margin-top", "5px");
					$("#doclist").show();
					
					return true;
				} else {
					// User is not logged in //
					//alert("Please log in first.");
					$(location).attr("href","./");
				}
			}
			
			$(document).ready(function() {		
				checkStatus();
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
			<ul id="doclist"></ul>
		</div>
		<div id="news">
			<span class='quarter' style="z-index: -10; width: 65%;">
				<header style="display:inline; margin-bottom: inherit; text-align: left;">Welcome,</header>
				<article style="display: inline;">
					and thank you for using Freshte.ch Documents - the safe online document editor.<br/>
					<br/>
					This document editor was designed from the ground up to be <i>very</i> secure, whilst maintaining functionality.  We hope you enjoy your time, and feel free to provide feedback.</ul>
				</article>
			</span>
			
			<span class='quarter' style="z-index: -1; width: 50%; float: right; top: -100px;">
				<header style="text-align: center;">Developers</header>
				<article>
					This project is available on Github at:<br/>
					<a href="https://github.com/Vierware/OpenDocs"><img src="images/Octocat.png"/></a>
					<div style="text-align: center;"><a href="https://github.com/Vierware/OpenDocs">Github - OpenDocs</a></div>
				</article>
			</span>
			
			<span class='quarter' style="z-index: -5; width: 80%; top: -200px;">
				<header>Technical Details</header>
				<article>
					<span style="display:inline-block;	max-width: 50%;">Your email, password, and documents remain always safe - they are never sent to us without first being encrypted (or hashed).</span><br/>
					This means we are physically incapable of divulging your private information - intentionally or otherwise.<br/>
					<br/>
					<b><i>What is sent to us (and we store):</i></b>
					<ul>
						<li>Hashed email (SHA-512)</li>
						<li>Hashed password (SHA-512 with plaintext email as salt)</li>
						<li>Encrypted document data (AES-256 with a new key for each document)</li>
						<li>Encrypted document keys (AES-256 with password+email as the encryption key)</li>
					</ul>
					<b><i>What about when you open links?</i></b><br/>
					Your keys are not betrayed as they are part of the "hash" segment (the part after the hash # character).<br/>
					<br/>
					This segment is never given to the server - so this site can never log it.
				</article>
			</span>
		</div>
	</body>
</html>