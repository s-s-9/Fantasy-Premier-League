function change(){
	console.log('aaaaa');
	xmlHttp = new XMLHttpRequest();
	if(xmlHttp.readyState==0 || xmlHttp.readyState==4){
		n = encodeURIComponent(document.getElementById('textbox').value);
		xmlHttp.open('GET', 'usernamecheck.php?n=' + n);
		xmlHttp.onreadystatechange = handleResponse;
		xmlHttp.send();
	}
	else{
		setTimeout('change', 1000);
	}
}

function handleResponse(){
	console.log('aaaaa');
	if(xmlHttp.readyState==4){
		if(xmlHttp.status==200){
			var response = xmlHttp.responseText;
			var newdiv = document.getElementById('newdiv');
			if(response=='1'){
				newdiv.innerHTML = "Username not available!";
			}
			else if(response=='0'){
				newdiv.innerHTML = "Great username!";
			}
			change();
		}
	}
	
}
window.onload = change;

/*$.ajax({
	url: "usernamecheck.php",
	data: {
		n: "Shiny"
	},
	success: function(result){
		if(result=='1'){
			$("#newdiv").html("Username not available!")
		}
		else{
			$("#newdiv").html("Great username!");
		}
	}
});*/