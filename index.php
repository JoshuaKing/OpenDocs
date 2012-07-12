<html>
	<head>
		<script src="js/jquery-1.7.1.min.js"></script>
		
		<!-- Security Scripts -->
		<script src="js/sha/sha512.js"></script>
		<script src="js/js-aes.js"></script>
		
		<!-- Document Editing Scripts -->
		<script src="js/signup.js"></script>
		
		<!-- Styling For Editor -->
		<link rel="stylesheet" href="css/index.css" type="text/css" />
		
		<script>			
			function login() {
				var email = $("#login-email").val();
				var pass = $("#login-password").val();				
				submitLogin(email, pass);
			}
			
			function submitLogin(email, pass) {
				var hemail = new jsSHA(email, "ASCII").getHash("SHA-512", "HEX");
				var hpass = new jsSHA(pass + email, "ASCII").getHash("SHA-512", "HEX");
				
				$.post(
					"php/login.php",
					{
						"email": hemail,
						"pass": hpass
					}
				).success(function(data, text, jqxhr) {
					// Store details //
					localStorage.setItem("key", pass + email);	// For decrypting each document's key
					localStorage.setItem("email", hemail);		// For easy authentication usage
					localStorage.setItem("pt-email", email);	// For "logged in as xxx" usage
					localStorage.setItem("pass", hpass);		// For submitting on each request
					
					// Update the 'logged in as' code //
					//checkStatus();
					$(location).attr("href", "home.php");
				}).error(function(jqxhr) {
					alert("Error logging in: " + jqxhr.status + " - " + jqxhr.responseText);
				});
			}
			
			function checkStatus() {
				var key = localStorage.getItem("key");
				var hemail = localStorage.getItem("email");
				var hpass = localStorage.getItem("pass");
				var email = localStorage.getItem("pt-email");
				
				// Mark user as logged in if details are currently stored in localStorage //
				if (key != null && email != null && hpass != null && hemail != null) {
					// User is logged in //
					$(location).attr("href", "home.php");
				}
			}
			
			$(document).ready(function() {		
				checkStatus();
			});
			
			function passwordIndicator() {
				var upper = /[A-Z]/;
				var lower = /[a-z]/;
				var numeric = /[0-9]/;
				var symbols = /[^\d\w]/i;
				
				var password = $("#signup-password").val();
				
				if (password.search(upper) != -1 && password.search(lower) != -1) {
					$("#mixedcase").attr("src", "images/tick.png");
				} else {
					$("#mixedcase").attr("src", "images/cross.png");
				}
				
				if (password.search(numeric) != -1) {
					$("#numbers").attr("src", "images/tick.png");
				} else {
					$("#numbers").attr("src", "images/cross.png");
				}
				
				if (password.search(symbols) != -1) {
					$("#symbols").attr("src", "images/tick.png");
				} else {
					$("#symbols").attr("src", "images/cross.png");
				}
				
				if (password.length >= 8) {
					$("#eightchar").attr("src", "images/tick.png");
				} else {
					$("#eightchar").attr("src", "images/cross.png");
				}
			}
		</script>
<!-- Google Analytics -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19917902-4']);
  _gaq.push(['_setDomainName', '.freshte.ch']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	</head>
	<body>
		<!-- Header -->
		<header>
			<div>Freshte.ch Documents</div>
			<span style="background-color:#6443D5">&nbsp;</span>
			<span style="background-color:#D7D86F">&nbsp;</span>
			<span style="background-color:#00C942">&nbsp;</span>
		</header>
		<section>
			<img src="images/warrant.png"/>
			<img src="images/anonhackers.png"/>
			<img src="images/padlock.png"/>
			<span style="color:#6443D5;">Private</span><span style="color:#D7D86F;">Secure</span><span style="color:#00C942;">Safe</span>
			<span class="description" style="margin-left: 7.5%;">Your data is not decryptable by us - not even warrants will magically make us able to.</span>
			<span class="description">Designed with solid security in mind - even against MySQL injections and XSS attacks.</span>
			<span class="description">We have your security and privacy in mind - your content is for your eyes only.</span>
		</section>
		<div id="login">
			<form id="login-details">
				<div>Login</div>
				<input id="login-email" placeholder="Email Address"></input>
				<input id="login-password" placeholder="Enter password" type="password"></input>
				<input class="button" id="login-button" type="submit" value="Log In" onclick="login(); return false;"></input>
			</form>
			<form id="password-strength-form">
				<div>Password Strength</div>
				<span>If you would like a memorable password, try Word + birthyear/age + symbols. Eg. Potato85$</span>
				<div class="indicator"><img id="mixedcase" src="images/cross.png"/>Mixed Case</div>
				<div class="indicator"><img id="eightchar" src="images/cross.png"/>8 Characters or more</div>
				<div class="indicator"><img id="symbols" src="images/cross.png"/>Symbols (eg. !@#$%^)</div>
				<div class="indicator"><img id="numbers" src="images/cross.png"/>Numbers</div>
			</form>
			<form id="signup-details">
				<div>Signup</div>
				<input type="email" id="signup-username" placeholder="Email Address"/>
				<input type="password" id="signup-password" placeholder="Password" onchange="passwordIndicator();" onkeyup="passwordIndicator();"/>
				<span>Note: We do not store your email or password in plain text (they are hashed with SHA-512)</span>
				<input class="button" type="submit" value="Sign Up" onclick="attemptSignup(); return false;"/>
			</form>
		</div>
	</body>
</html>