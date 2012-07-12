<script>
	function checkStatus() {
		var key = localStorage.getItem("key");
		var hemail = localStorage.getItem("email");
		var hpass = localStorage.getItem("pass");
		var email = localStorage.getItem("pt-email");
		
		// Mark user as logged in if details are currently stored in localStorage //
		if (key != null && email != null && hpass != null && hemail != null) {
			// User is logged in //
			$("nav").show();
			$("#pane").show();
			
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
			//$("#doclist").css("margin-top", "-15px");
			$("#doclist").show();
			
			return true;
		} else {
			// User is not logged in //
			//alert("Please log to edit a document.");
			//$(location).attr("href","./");
			return false;
		}
	}
</script>