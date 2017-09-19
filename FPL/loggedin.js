$(window).on('load', startDoingStuffs);

function canvasStuffs(){
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
}

function disableSaveTeam(){
	document.getElementById('saveteam').disabled = true;
	document.getElementById('saveteam').style.background = 'gray';
}

function retrieveUsername(){
	$.ajax({
		url: "loggedin.php",
		data: {
			USER: '?'
		},
		success: function(result){
			console.log(result);
			//alert(result);
		}
	});
}

function doTheTabbing(){
	var tabs = document.getElementsByClassName('ptabs');
	for(var i = 0; i<tabs.length; i++){
		var selected = 'tab-1';
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
}

function hoverNavTabs(){
	var navuls = document.getElementsByClassName('nav_ul');
	for(var i = 0; i<navuls.length; i++){
		document.getElementById(navuls[i].id).addEventListener('mouseover',
		function(){
			document.getElementById(this.id).style.borderBottom = "5px solid rgb(233, 0, 82)";
		});
		document.getElementById(navuls[i].id).addEventListener('mouseout',
		function(){
			document.getElementById(this.id).style.borderBottom = "0px solid rgb(233, 0, 82)";
		});
	}
}

function pitchviewListview(){
	document.getElementById('pitchview').addEventListener('click', 
	function (){
		this.style.background = "white";
		this.style.color = "black";
		var listview = document.getElementById('listview');
		listview.style.background = "rgb(56, 0, 60)";
		listview.style.color = "white";
	});
	document.getElementById('listview').addEventListener('click', 
	function (){
		this.style.background = "white";
		this.style.color = "black";
		var listview = document.getElementById('pitchview');
		listview.style.background = "rgb(56, 0, 60)";
		listview.style.color = "white";
	});
}

function viewPlayers(pageNo, pos){
	positionforpagination = 'all';
	$.ajax({
		url: "loggedin.php",
		data: {
			PAGENO: pageNo, POS: pos
		},
		success: 
		function(result){
			document.getElementById('viewedplayers').innerHTML = result;
			
			
			if(document.getElementById('gkslekha')){
				document.getElementById('gkslekha').addEventListener('click', 
				function(){
					viewPlayers(1, 'GK');
					for(var i = 1; i<=10; i++){
						document.getElementById('paging' + i).addEventListener('click', 
						function(){
							p = this.innerHTML;
							viewPlayers(p, 'GK');
						});
					}
				});
			}
			if(document.getElementById('defslekha')){
				document.getElementById('defslekha').addEventListener('click', 
				function(){
					viewPlayers(1, 'DEF');
					for(var i = 1; i<=10; i++){
						document.getElementById('paging' + i).addEventListener('click', 
						function(){
							p = this.innerHTML;
							viewPlayers(p, 'DEF');
						});
					}
				});
			}
			if(document.getElementById('midslekha')){
				document.getElementById('midslekha').addEventListener('click', 
				function(){
					viewPlayers(1, 'MID');
					for(var i = 1; i<=10; i++){
						document.getElementById('paging' + i).addEventListener('click', 
						function(){
							p = this.innerHTML;
							viewPlayers(p, 'MID');
						});
					}
				});
			}
			if(document.getElementById('fwdslekha')){
				document.getElementById('fwdslekha').addEventListener('click', 
				function(){
					viewPlayers(1, 'FWD');
					for(var i = 1; i<=10; i++){
						document.getElementById('paging' + i).addEventListener('click', 
						function(){
							p = this.innerHTML;
							viewPlayers(p, 'FWD');
						});
					}
				});
			}
			var viewedp = document.getElementsByClassName('viewedp');
			var clubs = {BOU: 0, ARS: 0, BUR: 0, CHE: 0, CRY: 0, 
						 EVE: 0, HUL: 0, LIV: 0, LEI: 0, MCI: 0,
						 MUN: 0, MID: 0, STO: 0, SOU: 0, SWA: 0,
						 SUN: 0, WAT: 0, WBA: 0, WHU: 0, TOT: 0};
			Array.from(viewedp).forEach(function(element){
				element.addEventListener('mouseover', 
				function(){
					this.style.cursor = 'hand';
				});
				element.addEventListener('click', 
				function(){
					if(this.style.background=='rgb(0, 255, 135)'){
						alert('already in your team');
						return;
					}
					var currg = document.getElementsByClassName('gk').length+1;
					var currd = document.getElementsByClassName('def').length+1;
					var currm = document.getElementsByClassName('mid').length+1;
					var currf = document.getElementsByClassName('fwd').length+1;
					var name = this.children[1].firstChild.firstChild.data;
					var club = this.children[1].children[2].firstChild.data;
					var pos = this.children[2].firstChild.data.toLowerCase();
					var id = this.id;
					
					if(clubs[club]==3 ){
						   alert('Cannot have more than 3 players from one team');
						   return;
					}
					
					//console.log(id);
					//console.log(name, club, pos);
					if(pos=='gk'){
						if(currg==3){
							alert('you cannot have more than 2 gks');
							return;
						}
						//console.log(document.getElementById('gk1pack').className);
						//console.log(document.getElementById('gk2pack').className);
						if(document.getElementById('gk1pack').className != 'gk'){
							poscurr = 'gk1';
						}
						else if(document.getElementById('gk2pack').className != 'gk'){
							poscurr = 'gk2';
						}
					}
					else if(pos=='def'){
						if(currd==6){
							alert('you cannot have more than 5 defs');
							return;
						}
						if(document.getElementById('def1pack').className != 'def'){
							poscurr = 'def1';
						}
						else if(document.getElementById('def2pack').className != 'def'){
							poscurr = 'def2';
						}
						else if(document.getElementById('def3pack').className != 'def'){
							poscurr = 'def3';
						}
						else if(document.getElementById('def4pack').className != 'def'){
							poscurr = 'def4';
						}
						else if(document.getElementById('def5pack').className != 'def'){
							poscurr = 'def5';
						}
					}
					else if(pos=='mid'){
						if(currm==6){
							alert('you cannot have more than 5 mids');
							return;
						}
						if(document.getElementById('mid1pack').className != 'mid'){
							poscurr = 'mid1';
						}
						else if(document.getElementById('mid2pack').className != 'mid'){
							poscurr = 'mid2';
						}
						else if(document.getElementById('mid3pack').className != 'mid'){
							poscurr = 'mid3';
						}
						else if(document.getElementById('mid4pack').className != 'mid'){
							poscurr = 'mid4';
						}
						else if(document.getElementById('mid5pack').className != 'mid'){
							poscurr = 'mid5';
						}
					}
					else if(pos=='fwd'){
						if(currf==4){
							alert('you cannot have more than 3 fwds');
							return;
						}
						if(document.getElementById('fwd1pack').className != 'fwd'){
							poscurr = 'fwd1';
						}
						else if(document.getElementById('fwd2pack').className != 'fwd'){
							poscurr = 'fwd2';
						}
						else if(document.getElementById('fwd3pack').className != 'fwd'){
							poscurr = 'fwd3';
						}
					}
					//console.log(poscurr);
					
					var newhtml = 	'<div class = "' + pos + '" id = "' + poscurr + 'pack">';
					newhtml+=			'<div class = ' + id + '></div>';
					newhtml+=			'<img id = "' + poscurr + 'img" src = ' + club + '.webp />';
					newhtml+=			'<div class = "namepts1" id = "' + poscurr +'name">';
					newhtml+=				name;
					newhtml+=			'</div>';
					newhtml+=			'<div class = "namepts2" id = "' + poscurr + 'pts">';
					newhtml+=				club;
					newhtml+=			'</div>';
					newhtml+=		'</div>';
					
					clubs[club]++;
					//console.log(clubs);
					
					//console.log(newhtml);
					document.getElementById(poscurr).innerHTML = newhtml;
					this.style.background = '#00FF87';
					//console.log(document.getElementById(poscurr + 'pack').className);
					document.getElementById(poscurr).addEventListener('mouseover', 
					function(){
						this.style.cursor = 'hand';
					});
					document.getElementById(poscurr).addEventListener('click', 
					function(){
						if(this.firstChild.childNodes[3]){
							var releasedclub = this.firstChild.childNodes[3].innerHTML;
							clubs[releasedclub]--;
						}
						
						var newhtml2 = 	'<div class = "faka" id = "' + this.id + 'pack">';
						newhtml2+=			'<img id = "' + this.id + 'img" src = fakas.webp />';
						newhtml2+=			'<div class = "namepts1" id = "' + this.id +'name">';
						newhtml2+=				'Add Player';
						newhtml2+=			'</div>';
						newhtml2+=			'<div class = "namepts2" id = "' + this.id + 'pts">';
						newhtml2+=				'?';
						newhtml2+=			'</div>';
						newhtml2+=		'</div>';
						var tr = (this.firstChild.firstChild.className);
						
						//console.log(tr);
						if(tr){
							document.getElementById(tr).style.background = 'white';
						}
						this.innerHTML = newhtml2;
						
					});
					//console.log(document.getElementsByClassName('gk').length);
				});
			});
		}
	});
}	


function startDoingStuffs(){
	//draw shapes beside the navigation bar
	canvasStuffs();
	//disable the save team button initially
	disableSaveTeam();
	//Retrieve username from php session
	retrieveUsername();
	//select proper tabs (home/prizes/the scout...)
	doTheTabbing();
	//bring menus on hovering navigation tabs (premier league/fantasy/...)
	hoverNavTabs();
	//pitchview, listview
	pitchviewListview();
	
	//requesting ajax for initial player names
	
	viewPlayers(1, 'all');
	//fucking pagination
	
	
	//search by name
	view = document.getElementById('viewinput').value;
	
	var searchinput = document.getElementById('searchinput');
	searchinput.addEventListener('keyup', 
	function(){
		$.ajax({
			url: "loggedin.php",
			data: {
				SEARCH: searchinput.value
			},
			success: function(result){
				//console.log(result);
				document.getElementById('viewedplayers').innerHTML = result;
			}
		});
	});
	
}


