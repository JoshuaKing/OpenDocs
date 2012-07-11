var editableDoc = true;

function view(docid, htmlDocId) {
	var key = localStorage.getItem("key");
	var hemail = localStorage.getItem("email");
	var hpass = localStorage.getItem("pass");
	var htmlDoc = $("#" + htmlDocId)
	
	// User does not have to be logged in to view public docs //
	/*if (hpass === null || hemail === null || key === null) {
		alert("Please make sure you are logged in!");
		return;
	}
	
	if (!docid) {
		alert("Please enter in a document ID");
	}
	if ($("#doc").html() != "" || $("#doctitle").val() != "Untitled Document") {
		save('doc');
	} else {
		// Empty document
	}*/
	
	$.post(
		"php/view.php",
		{
			"email": hemail,
			"pass": hpass,
			"docid": docid
		}
	).success(function(data, text, jqxhr) {
		// Make sure pane is visible //
		$("#doctitle").show();
		$("#pane").show();
		
		// Seperate document and key //
		var response = jqxhr.responseText.split('|');
		
		// Decrypt Key //
		var docKey = "???";
		if (window.location.hash.length > 1) {
			docKey = hex2a(window.location.hash.substr(1));
		} else {
			docKey = Aes.Ctr.decrypt(response[0], key, 256);
		}
		
		// Decrypt Title //
		var docTitle = Aes.Ctr.decrypt(response[1], docKey, 256);
		$("#doctitle").val(docTitle.replace(new RegExp("[<>]", 'g'),""));
		
		// Decrypt Document //
		var doc = Aes.Ctr.decrypt(response[2], docKey, 256);
		
		// Check if document is editable //
		if (response[3] == '0') {
			editableDoc = false;
			$("nav").hide();
		}
		
		// Set public flag checkbox //
		if (response[4] == '1') {
			$("#publicCheckbox").attr("checked", "checked");
		}
		
		// Set attributes for updating //
		htmlDoc.attr("data-docid", docid);
		htmlDoc.attr("data-key", response[0]);
		
		// Replace all [ with [LBRACKET] and ] with [RBRACKET] //
		doc = doc.replace(/\[/g, "[LBRACKET]");
		doc = doc.replace(/\]/g, "[RBRACKET]");
				
		// Parse the document for nice HTML //
		doc = doc.replace(new RegExp("<(/?)([biusp])>", 'g'),"[$1$2]");		// Bold, Italic, Underline, Strikethrough, Paragraph
		doc = doc.replace(new RegExp("<(/?)(h[0-9])>", 'g'),"[$1$2]");		// Headings
		doc = doc.replace(new RegExp("<(/?)(ol|ul|li|hr|br|sub|sup|div|font|strike)>", 'g'),"[$1$2]");	// Lists, Misc
		
		// Parse complex <font> tags //
		var tag = (new RegExp("[<]([a-z0-9]+) ([^>]+)", "i")).exec(doc);
		while (tag != null) {
			if (!/h[0-9]|div|sub|sup|font|strike|span|li|ul|ol|p/.test(tag[1])) {
				alert(tag[1] + " is not allowed.");
				doc = doc.replace(new RegExp("[<][a-z0-9]+ [^>]+>", 'i'), "");
				tag = (new RegExp("[<]([a-z0-9]+) ([^>]+)>", "i")).exec(doc);
				continue;
			}
			
			var tagSafe = "[" + tag[1];
			var fontFamily = (new RegExp("face=\"([a-z, \']+)\"", "i")).exec(tag[2]);
			if (fontFamily != null) {
				tagSafe += " face=\"" + fontFamily[1] + "\"";
			}
			
			var fontColour = (new RegExp("color=\"(#[a-f0-9]+)\"", "i")).exec(tag[2]);
			var fontColourStyle = (new RegExp("color:[ ]+([a-frg0-9(),# ]+);", "i")).exec(tag[2]);
			
			if (fontColourStyle != null) {
				tagSafe += " colour=\"" + fontColourStyle[1] + "\"";
			} else if (fontColour != null) {
				tagSafe += " colour=\"" + fontColour[1] + "\"";
			}
			
			var fontSize = (new RegExp("font-size:[ ]+([pxem%0-9]+);", "i")).exec(tag[2]);
			if (fontSize != null) {
				tagSafe += " size=\"" + fontSize[1] + "\"";
			}
			
			var textAlign = (new RegExp("text-align:[ ]+([a-z]+);", "i")).exec(tag[2]);
			if (textAlign != null) {
				tagSafe += " align=\"" + textAlign[1] + "\"";
			}
			
			
			tagSafe += "]";
			doc = doc.replace(new RegExp("[<][a-z0-9]+ [^>]+>", 'i'),tagSafe);
			tag = (new RegExp("[<]([a-z0-9]+) ([^>]+)>", "i")).exec(doc);
		}
		
		// Make sure doc is safe //
		doc = doc.replace(new RegExp("<[^>]*>", 'g'),"");
		
		// Undo parsing for nice html
		doc = doc.replace(new RegExp("\\[(/?)([biusp])\\]", 'g'),"<$1$2>");	// Bold, Italic, Underline, Strikethrough, Paragraph
		doc = doc.replace(new RegExp("\\[(/?)(h[0-9])\\]", 'g'),"<$1$2>");	// Headings
		doc = doc.replace(new RegExp("\\[(/?)(ol|ul|li|hr|br|sub|sup|div|font|strike|span)\\]", 'g'),"<$1$2>");	// Lists, Misc
		
		// Undo parsing of complex <font> tags //
		var tag = (new RegExp("\\[([a-z0-9]+) ([^\\]]+)\\]", "i")).exec(doc);
		while (tag != null) {
			var tagSafe = "<" + tag[1];
			
			var fontFamily = (new RegExp("face=\"([a-z, \\']+)\"", "i")).exec(tag[2]);
			if (fontFamily != null) {
				tagSafe += " face=\"" + fontFamily[1] + "\"";
			}
			
			tagSafe += " style=\"";
			var fontColour = (new RegExp("colour=\"([a-frg0-9(),# ]+)\"", "i")).exec(tag[2]);
			if (fontColour != null) {
				tagSafe += " color: " + fontColour[1] + ";";
			}
			
			var fontSize = (new RegExp("size=\"([pxem%0-9]+)\"", "i")).exec(tag[2]);
			if (fontSize != null) {
				tagSafe += " font-size: " + fontSize[1] + ";";
			}
			
			var textAlign = (new RegExp("align=\"([a-z]+)\"", "i")).exec(tag[2]);
			if (textAlign != null) {
				tagSafe += " text-align: " + textAlign[1] + ";";
			}
			
			tagSafe += "\"";	// style
			tagSafe += ">";
			doc = doc.replace(new RegExp("\\[[a-z0-9]+ [^\\]]+\\]", 'i'),tagSafe);
			tag = (new RegExp("\\[([a-z0-9]+) ([^\\]]+)\\]", "i")).exec(doc);
		}
		
		// Replace all [LBRACKET] with [ and [RBRACKET] with ] //
		doc = doc.replace(/\[RBRACKET\]/g, "]");
		doc = doc.replace(/\[LBRACKET\]/g, "[");
		
		// Display document //
		$("#" + htmlDocId).html(doc);
	}).error(function(jqxhr) {
		alert("Error viewing document: " + jqxhr.status + " - " + jqxhr.responseText);
	});
}

function save(htmlDocId) {
	if (!editableDoc) return;
	
	var key = localStorage.getItem("key");
	var hemail = localStorage.getItem("email");
	var hpass = localStorage.getItem("pass");
	var htmlDoc = $("#" + htmlDocId);
	var doc = htmlDoc.html();
	var docTitle = $("#doctitle").val();
	var docid = htmlDoc.attr("data-docid");
	var docSecureKey = htmlDoc.attr("data-key");
	
	if (hpass === null || hemail === null || key === null) {
		alert("Please make sure you are logged in!");
		return;
	}
	
	// Check if document should be updated or created //
	if (docid && docSecureKey) {
		// Update existing document //
		// Decrypt key //
		var docKey = Aes.Ctr.decrypt(docSecureKey, key, 256);
		
		// Encrypt document title with key //
		var secureDocTitle = Aes.Ctr.encrypt(docTitle, docKey, 256);
		
		// Encrypt document //
		var secureDoc = Aes.Ctr.encrypt(doc, docKey, 256);
		
		// Submit document safely //
		$.post(
			"php/save.php",
			{
				"email": hemail,
				"pass": hpass,
				"doc": secureDoc,
				"title": secureDocTitle,
				"docid": docid
			}
		).success(function(data, text, jqxhr) {
			//alert(jqxhr.responseText);
			if ($("#savestatus").html() == "Saving...") {
				$("#savestatus").html("Saved.");
			}
			
			// Re-retrieve title's in case it's changed //
			populate('doclist');
		}).error(function(jqxhr) {
			$("#savestatus").html("Error saving document: " + jqxhr.status + " - " + jqxhr.responseText);
			alert("Error saving document: " + jqxhr.status + " - " + jqxhr.responseText);
		});
	} else {
		// Create new document //
		// Create new document key //
		var hashDoc = new jsSHA(doc, "ASCII").getHash("SHA-512", "HEX");
		var randString = randomString(50);	// TODO: remove - no longer used
		var newKey = randomString(20); //hashDoc + randString;
		
		// Encrypt document title with key //
		var secureDocTitle = Aes.Ctr.encrypt(docTitle, newKey, 256);
		
		// Encrypt document with key //
		var secureDoc = Aes.Ctr.encrypt(doc, newKey, 256);
		
		// Encrypt document key with account key //
		var secureDocKey = Aes.Ctr.encrypt(newKey, key, 256);
		
		// Submit document safely //
		$.post(
			"php/save.php",
			{
				"email": hemail,
				"pass": hpass,
				"doc": secureDoc,
				"title": secureDocTitle,
				"key": secureDocKey
			}
		).success(function(data, text, jqxhr) {
			var response = jqxhr.responseText.split('|');
			
			htmlDoc.attr("data-docid", response[0]);
			htmlDoc.attr("data-key", secureDocKey);
			
			// Re-retrieve title's in case it's changed //
			populate('doclist');
		}).error(function(jqxhr) {
			alert("Error creating document: " + jqxhr.status + " - " + jqxhr.responseText);
		});
	}
}

function deleteDoc(docid, img) {
	if (!editableDoc) return;

	var key = localStorage.getItem("key");
	var hemail = localStorage.getItem("email");
	var hpass = localStorage.getItem("pass");
	var title = $(img).attr("data-doctitle");
	var currentlyOpenId = $('#doc').attr("data-docid");
	
	if (hpass === null || hemail === null || key === null) {
		alert("Please make sure you are logged in!");
		return;
	}
	
	if (currentlyOpenId == docid) {
		alert("The document is currently open, please open a different one before deleting");
		return;
	}
	
	// Confirm if the user wants to delete //
	var c = confirm("Are you sure you want to delete '" + title + "'?");
	if (!c) return;
	
	// Submit delete request //
	$.post(
		"php/deletedoc.php",
		{
			"email": hemail,
			"pass": hpass,
			"docid": docid
		}
	).success(function(data, text, jqxhr) {
		// Re-retrieve title's in case it's changed //
		populate('doclist');
	}).error(function(jqxhr) {
		alert("Error creating document: " + jqxhr.status + " - " + jqxhr.responseText);
	});
}

function populate(htmlId) {
	var key = localStorage.getItem("key");
	var hemail = localStorage.getItem("email");
	var hpass = localStorage.getItem("pass");
	var docs = $("#" + htmlId);
	
	if (hpass === null || hemail === null || key === null) {
		alert("Please make sure you are logged in!");
		return;
	}
	
	// Request titles //
	$.post(
		"php/titles.php",
		{
			"email": hemail,
			"pass": hpass
		}
	).success(function(data, text, jqxhr) {
		docs.html("");	// Clear titles
		var response = jqxhr.responseText.split(';');
		
		for (var i = 0; i < response.length; i++) {
			if (!response[i]) {
				continue;	// For the last response - it is still included so skip it
			}
			
			var result = response[i].split('|');
			var id = result[0];
			
			// Decrypt document key //
			var docKey = Aes.Ctr.decrypt(result[1], key, 256);
			
			// Decrypt Title //
			var title = Aes.Ctr.decrypt(result[2], docKey, 256);
			
			title = title.replace(new RegExp("<[^>]*>", 'g'),"");
			docs.append("<li><a href=\"view.html?" + id + "#" + a2hex(docKey) + "\">" + title + "</a><img data-doctitle='" + title + "' src=\"editor-images/cross_grey.png\" onclick=\"deleteDoc(" + id + ", this);\" /></li>");
		}
		
		// Add 'new document' option //
		docs.append("<li id=\"new-document-li\"><a href=\"new.html\"><span id='new-document'>+</span></a></li>");
		if (response.length == 1) {
			// No documents created - could be a new user //
			$('body').append("<div id='new-document-hint'><span class='rotated-square'>&nbsp;</span><span class='close' onclick=\"closeNewHint();\">x</span></div>");
			$("#new-document-hint").append("<span class='new-hint'>New Document</span>");
			$("#new-document-hint").append("<div>To create a new document, click the + button to the left.  This button will always be available for easy creation of new documents later.</div>");
		} else {
			//$("#new-document-hint").remove();
		}
	}).error(function(jqxhr) {
		alert("Error getting document titles: " + jqxhr.status + " - " + jqxhr.responseText);
	});
}

function closeNewHint() {
	$('#new-document-hint').remove();
}

function saveChanges(oldId, oldHash, saveMod) {
	if (!editableDoc) return;
	
	var doc = $("#doc").html();
	var title = $("#doctitle").val();
	var id = $("#doc").attr("data-docid");
	var hdoc = new jsSHA(title + doc, "ASCII").getHash("SHA-512", "HEX");
	
	if (id == oldId && oldHash != hdoc) {
		// Changes have occured in the document //
		$("#savestatus").html("Document Changed.");
		setTimeout("saveChanges(" + id + ", '" + hdoc + "', true);", 2000);
		return;
	}
	
	if (saveMod) {
		// User has stopped typing for about 3 seconds, save changes now //
		$("#savestatus").html("Saving...");
		save('doc');
	} else {
		// Nothing changed, keep going. //
		$("#savestatus").html("No Changes.");
	}
	
	// Check for changes in 2 seconds //
	setTimeout("saveChanges(" + id + ", '" + hdoc + "', false);", 2000);
}

function documentKeyDown(event) {
	var shift = event.shiftKey;
	var key = event.keyCode;
	// Key Codes:
	// 8 - Backspace
	// 32 - Space
	// 9 - Tab
	// 83 - 's'
	
	var control = navigator.userAgent.search('Macintosh');
	control = control < 0 ? event.ctrlKey : event.metaKey;
	
	if (key == 83  && control) {
		$("#savestatus").html("Saving...");
		save('doc');	// TODO: Still saves if changes made (redundant save) 
		event.preventDefault();
		return false;
	} else if (key == 9 && !shift) {
		document.execCommand('indent',false, null);
		return false;
	} else if (key == 9 && shift) {
		document.execCommand('outdent',false, null);
		return false;
	}
	
	if (navigator.userAgent.search("Macintosh") >= 0) {
		if (control && key == 221) {
			document.execCommand('indent',false, null);
			return false;
		} else if (control && key == 219) {
			document.execCommand('outdent',false, null);
			return false;
		}
	}
	
	// Resize if necessary //
	var paneHeight = $('#pane').height();
	var docHeight = $('#doc').height() * 1.1;	// 110% for padding
	
	if (paneHeight < docHeight) {
		 $('#pane').height(docHeight);
	}
}

function publicAccess(el) {
	if (!editableDoc) return;

	var key = localStorage.getItem("key");
	var hemail = localStorage.getItem("email");
	var hpass = localStorage.getItem("pass");
	var docid = $('#doc').attr("data-docid");
	var public = ($(el).attr("checked") == "checked") ? "1" : "0";
	
	$.post(
		"php/sharedoc.php",
		{
			"email": hemail,
			"pass": hpass,
			"docid": docid,
			"public": public
		}
	).success(function(data, text, jqxhr) {
		// Do nothing //
		alert(jqxhr.responseText);
	}).error(function(jqxhr) {
		alert("Error updating document access: " + jqxhr.status + " - " + jqxhr.responseText);
	});
}