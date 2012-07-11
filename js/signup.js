function submitSignup(email, pass) {	
	var hemail = new jsSHA(email, "ASCII").getHash("SHA-512", "HEX");
	var hpass = new jsSHA(pass + email, "ASCII").getHash("SHA-512", "HEX");
	
	$.post(
		"php/signup.php",
		{
			"email": hemail,
			"pass": hpass
		}
	).success(function(data, text, jqxhr) {
		alert(jqxhr.responseText);
		
		// Now log in //
		submitLogin(email, pass);
	}).error(function(jqxhr) {
		alert("Error signing up: " + jqxhr.status + " - " + jqxhr.responseText);
	});
}

function attemptSignup() {
	var pass = $("#signup-password").val();
	var username = $("#signup-username").val();
	
	// Check username is an email (because we prefer it, not absolutely required though) //
	var emailRegEx = /^[a-z0-9._%+-]+@[A-Z0-9.-]+\.[a-z]{2,4}$/i;
	if (username.search(emailRegEx) == -1) {
		$("#signup-username").css("border", "1px red solid");
		alert("Please enter a valid email.");
		return;
	} else {
		$("#signup-username").css("border", "none");
	}
	
	// Check password length //
	if (pass.length < 8) {
		$("#signup-password").css("border", "1px red solid");
		alert("Please enter a password 8 characters or more.\nMust include mixed case letters, numbers, and symbols.");
		return;
	} else {
		$("#signup-username").css("border", "none");
	}
	
	// Check password complexity //
	var upper = /[A-Z]/;
	var lower = /[a-z]/;
	var numeric = /[0-9]/;
	var symbols = /[^\d\w]/i;
	if (pass.search(upper) == -1 || pass.search(lower) == -1
			|| pass.search(numeric) == -1 || pass.search(symbols) == -1) {
		$("#signup-password").css("border", "1px red solid");
		alert("Please enter a password which includes mixed case letters, numbers, and symbols.\nMust be 8 characters or more.");
		return;
	} else {
		$("#signup-username").css("border", "none");
	}
	
	submitSignup(username, pass);
	//cancelSignup();
}