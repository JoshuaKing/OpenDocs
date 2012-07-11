function selectFont(htmlId) {
	var ul = $("#" + htmlId + ">span");
	if (ul.css("display") == "none") {
		ul.css("display", "inline");
	} else {
		ul.css("display", "none");
	}
	
}

function changeFont(item) {
	var font = $(item).css("font-family");
	document.execCommand('fontName',false,font);
	$(item).parent().css("display", "none");
	$(item).parent().parent().children("input").eq(0).css("font-family", font);
	$(item).hide();
}

function selectFontSize() {
	var ul = $("#font-size>span");
	if (ul.css("display") == "none") {
		ul.css("display", "inline");
	} else {
		ul.css("display", "none");
	}
}

function changeFontSize(size) {
	document.execCommand('fontSize',false, 7);
	$('#doc font[size=7]').css("font-size", size + "px").removeAttr("size");
	$("#font-size>span").hide();
}

function selectFontColour() {
	var ul = $("#font-colour>span");
	if (ul.css("display") == "none") {
		ul.css("display", "inline");
	} else {
		ul.css("display", "none");
	}
}

function changeFontColour(colour) {
	document.execCommand('foreColor',false, colour);
	$("#font-colour>input").css("background", colour);
	$("#font-colour>span").hide();
}

function checkStates() {
	if (document.queryCommandState('bold')) {
		$("nav #bold").addClass("enabledFeature");
	} else {
		$("nav #bold").removeClass("enabledFeature");
	}
	
	if (document.queryCommandState('italic')) {
		$("nav #italic").addClass("enabledFeature");
	} else {
		$("nav #italic").removeClass("enabledFeature");
	}
	
	if (document.queryCommandState('underline')) {
		$("nav #underline").addClass("enabledFeature");
	} else {
		$("nav #underline").removeClass("enabledFeature");
	}
	
	if (document.queryCommandState('strikethrough')) {
		$("nav #strikethrough").addClass("enabledFeature");
	} else {
		$("nav #strikethrough").removeClass("enabledFeature");
	}
	
	var foreColor = document.queryCommandValue('foreColor');
	$("nav #font-colour>input").css("background-color", foreColor);
	
	var heading = document.queryCommandValue('formatBlock');
	if (/h[0-3]/.test(heading)) {
		$("nav #h1").removeClass("enabledFeature");
		$("nav #h2").removeClass("enabledFeature");
		$("nav #h3").removeClass("enabledFeature");
		$("nav #" + heading).addClass("enabledFeature");
	} else {
		$("nav #h1").removeClass("enabledFeature");
		$("nav #h2").removeClass("enabledFeature");
		$("nav #h3").removeClass("enabledFeature");
	}
	
	
	var fontSize = document.queryCommandValue('fontSize');
	//$("nav #font-size>input").val(fontSize);
	
	
}

function command(command, option) {
	if (typeof option == "undefined") option = null;
	document.execCommand(command, false, option);
	checkStates();
}