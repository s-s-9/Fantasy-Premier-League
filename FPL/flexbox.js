$(window).on('load', startdoingstuffs);
function startdoingstuffs(){
	//draw shapes beside the navigation bar
	var c = $('#navcanvas');
	canvas = c[0].getContext('2d');
	canvas.fillStyle = "rgb(56, 0, 60)";
	canvas.beginPath();
	canvas.moveTo(300, 0);
	canvas.lineTo(120, 60);
	canvas.lineTo(300, 60);
	canvas.closePath();
	canvas.fill();
	canvas.moveTo(300, 40);
	canvas.lineTo(0, 150);
	canvas.lineTo(300, 150);
	canvas.closePath();
	canvas.fill();
	//Retrieve username from php session
	$.ajax({
		url: "loggedin.php",
		data: {
			USER: '?'
		},
		success: function(result){
			if(result!='No session'){
				window.location="loggedin.html";
			}
			//alert(result);
		}
	});
	//select proper tabs (home/prizes/the scout...)
	tabs = document.getElementsByClassName('ptabs');
	for(var i = 0; i<tabs.length; i++){
		selected = 'tab-1';
		var currentTab = tabs[i];
		document.getElementById(currentTab.id).addEventListener('click', 
		function(event){
			event.preventDefault();
			selected = this.id;
			var cont = this.id.replace("tab", "content");
			document.getElementById(cont).style.display = "-webkit-box";
			document.getElementById(this.id).style.background = "white";
			document.getElementById(this.id).style.color = "black";
			for(var j = 0; j<tabs.length; j++){
				if(tabs[j].id != this.id){
					var cont2 = tabs[j].id.replace("tab", "content");
					document.getElementById(cont2).style.display = "none";
					document.getElementById(tabs[j].id).style.background = "#02894E";
					document.getElementById(tabs[j].id).style.color = "white";
				}
			}
		});
		document.getElementById(currentTab.id).addEventListener('mouseover', 
		function(){
			if(this.id != selected){
				document.getElementById(this.id).style.background = "#1B381A";
			}
		});
		document.getElementById(currentTab.id).addEventListener('mouseout', 
		function(){
			if(this.id != selected){
				document.getElementById(this.id).style.background = "#02894E";
			}
		});
	}
	
	//bring menus on hovering navigation tabs (premier league/fantasy/...)
	var navuls = document.getElementsByClassName('nav_ul');
	for(var i = 0; i<navuls.length; i++){
		document.getElementById(navuls[i].id).addEventListener('mouseover',
		function(){
			document.getElementById(this.id).style.borderBottom = "5px solid rgb(233, 0, 82)";
		}
		);
		document.getElementById(navuls[i].id).addEventListener('mouseout',
		function(){
			document.getElementById(this.id).style.borderBottom = "0px solid rgb(233, 0, 82)";
		});
	}
	
	//highlighting login button
	document.getElementById('loginbutton').addEventListener('mouseover', 
	function(){
		document.getElementById(this.id).style.background = "black";
	});
	document.getElementById('loginbutton').addEventListener('mouseout', 
	function(){
		document.getElementById(this.id).style.background = "rgb(56, 0, 60)";
	});
	
	//requesting ajax for logging the user in
	document.getElementById('loginform').addEventListener('submit', 
	function(event){
		event.preventDefault();
		var u = document.getElementById('email').value;
		var p = document.getElementById('pass').value;
		console.log(u, p);
		$.ajax({
			url: "flexbox.php",
			data: {
				USERNAME: u, PASSWORD: p
			},
			success: function(result){
				if(result=='Y'){
					window.location="loggedin.html";
				}
				else if(result=='N'){
					alert('Wrong username/password combination');
				}
			}
		});
	});
}


