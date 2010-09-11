

function validateFields() {

	// hide the 'thank you email has been sent';
	$('status').innerHTML = '';

	var error = new Array();
	var errorCount = 0;
	
	if($('name').value == '') {
		error[errorCount] = "please enter your name\n";
		errorCount += 1;
	}
	if($('email').value == '') {
		error[errorCount] = 'please enter your email\n';
		errorCount += 1;
	} else {
	
		var str = $('email').value;
		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		
		var okEmail = true;
		if (str.indexOf(at)==-1){
		   okEmail = false;
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   okEmail = false;
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    okEmail = false;
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    okEmail = false;
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    okEmail = false;
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    okEmail = false;
		 }
		
		 if (str.indexOf(" ")!=-1){
		    okEmail = false;
		 }
		if(okEmail == false) {
			error[errorCount] = 'please enter valid email\n';
			errorCount += 1;
		}
		
	}
	if($('message').value == '') {
		error[errorCount] = 'please enter message\n';
		errorCount += 1;
	}
	if(errorCount > 0) {
		var errorStr = '';
		for(var i=0; i<error.length; i++) {
			errorStr += '<p class="error">' + error[i] + '</p>';
		}
		
		/* output error */
		$('status').innerHTML = errorStr;
	} else {
		//if no errors
		sendEmail();
	}
		
}

function sendEmail() {

	var postString = "name=" + document.getElementById("name").value + "&";
	postString += "email=" + document.getElementById("email").value + "&";
	postString += "message=" + document.getElementById("message").value + "&";
	postString += "submitted=" + document.getElementById("submitted").value;
	
	$('load').style.display = 'block';
	
	new ajax('sendEmail.php', {postBody: postString, onComplete: showResponse});
}

function showResponse(originalRequest) {
	var returnedData = originalRequest.responseText;
	$('load').style.display = 'none';
	$('status').innerHTML = '<p class="success">'+returnedData+'</p>';
	
	//reset the fields
	$('name').value = '';
	$('email').value = '';
	$('message').value = '';
}



