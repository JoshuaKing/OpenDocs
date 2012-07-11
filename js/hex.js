function hex2a(hex) {
    var str = '';
    for (var i = 0; i < hex.length; i += 2)
        str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
    return str;
}

function a2hex(ascii) {
	var str = '';
	for (var c in ascii) {
		str += ascii.charCodeAt(c).toString(16);
	}
	return str;
}