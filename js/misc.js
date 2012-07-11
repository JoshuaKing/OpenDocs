function logout() {	
	localStorage.removeItem("key");
	localStorage.removeItem("email");
	localStorage.removeItem("pt-email");
	localStorage.removeItem("pass");
	$(location).attr("href","./");
}