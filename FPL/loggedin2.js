$(window).on('load', startDoingStuffs);

//draw on canvas (for the left side of the navigation)
function canvasStuffs(){
	var c = $('#navcanvas');	
	canvas = c[0].getContext('2d');
	canvas.fillStyle = "rgb(56, 0, 60)";	//choose navy blue color
	
	//draw first triangle
	canvas.beginPath();
	canvas.moveTo(300, 0);
	canvas.lineTo(120, 60);
	canvas.lineTo(300, 60);
	canvas.closePath();
	canvas.fill();
	
	//draw second triangle
	canvas.moveTo(300, 40);
	canvas.lineTo(0, 150);
	canvas.lineTo(300, 150);
	canvas.closePath();
	canvas.fill();
}

//disable the save team button while creating team for the first time
function disableSaveTeam(){
	document.getElementById('saveteam').disabled = true;
	document.getElementById('saveteam').style.background = 'gray';
}

//enable the save team button while creating team for the first time
function enableSaveTeam(){
	document.getElementById('saveteam').disabled = false;
	document.getElementById('saveteam').style.background = 'rgb(56, 0, 60)';
}

//disable the save changes button in my team
function disableSaveChanges(){
	document.getElementById('savechanges').disabled = true;
	document.getElementById('savechanges').style.background = 'gray';
}

//enable the save changes button in my team
function enableSaveChanges(){
	document.getElementById('savechanges').disabled = false;
	document.getElementById('savechanges').style.background = 'rgb(56, 0, 60)';
}

//disable the save transfers button in transfers
function disableSaveTransfers(){
	document.getElementById('savetransfers').disabled = true;
	document.getElementById('savetransfers').style.background = 'gray';
}

//enable the save transfers button in transfers
function enableSaveTransfers(){
	document.getElementById('savetransfers').disabled = false;
	document.getElementById('savetransfers').style.background = 'rgb(56, 0, 60)';
}

//hide team selection section if team already exists
function hideTeamSelection(){
	document.getElementById('content-1').style.display = "none";			//hide the team selection contents
	document.getElementById('content-2').style.display = "-webkit-box";		//show the my team contents
	document.getElementById('tab-1').style.display = "none";				//hide the team selection tab
	document.getElementById('tab-2').style.background = "white";			//make the my team tab white, as if it's selected
	document.getElementById('tab-2').style.color = "black";					//turn text in my team tab black
	
	//if the my team tab is selected, then don't change anything on hovering in or out
	document.getElementById('tab-2').addEventListener('mouseover', 
	function(){
		if(this.style.color=='black'){
			this.style.background = 'white';
			this.style.color = 'black';
		}
	});
	document.getElementById('tab-2').addEventListener('mouseout', 
	function(){
		if(this.style.color=='black'){
			this.style.background = 'white';
			this.style.color = 'black';
		}
	});
}

//swap same positioned players between field and bench (gk for gk, def for def etc.)
function samePosSubstitution(){
	var stfs = droppedstarter.firstChild;
	stfs.replaceChild(subimg, stfs.firstChild);					//change jersey-part 1
	var subfs = draggedsub.firstChild;
	subfs.replaceChild(starterimg, subfs.firstChild);			//change jersey-part 2
	droppedstarter.firstChild.children[1].innerHTML = subname;	//change name-part 1
	draggedsub.firstChild.children[1].innerHTML = startername;	//change name-part 2
	droppedstarter.firstChild.children[2].innerHTML = subpts;	//change points-part 1
	draggedsub.firstChild.children[2].innerHTML = starterpts;	//change points-part 2
	droppedstarter.firstChild.lastChild.id = subplid;			//change player id-part 1
	draggedsub.firstChild.lastChild.id = starterplid;			//change player id-part 2
}

//adding drag functions to substitutes
function makeSubsDraggable(){
	//setting event listeners to all the benched players; they have drag functions only
	for(var subi = 1; subi<=4; subi++){
		//as soon as a sub is dragged, important global variables are initialized so that everyone knows what was dragged
		document.getElementById('c2sub' + subi).addEventListener('dragstart', 
		function(){
			draggedsub = this;											//draggedsub now holds all the information
			subpos = draggedsub.className.substr(2);					//position of sub (gk, def, mid, fwd)
			subimg = draggedsub.firstChild.firstChild.cloneNode(true);	//jersey of sub (as image element)
			subname = draggedsub.firstChild.children[1].innerHTML;		//name of sub
			subpts = draggedsub.firstChild.children[2].innerHTML;		//points of sub
			subid = this.id;											//subs precise position (c2sub1, c2sub2, ...)
			subplid = this.firstChild.lastChild.id;						//player id of the sub (myteamid45, ...)
		});
		//just for the prevent default part
		document.getElementById('c2sub' + subi).addEventListener('dragenter', 
		function(event){
			event.preventDefault();
		});
		//just for the prevent default part
		document.getElementById('c2sub' + subi).addEventListener('dragover', 
		function(event){
			event.preventDefault();
		});
	}
}

//save changes to my team
function savingMe(){
	var onfield = [];		//holds the ids of on field players after change
	for(var saveteamh = 0; saveteamh<=3; saveteamh++){		//looping through all positions
		for(var saveteami = 1; saveteami<=5; saveteami++){	//from precise positions 1-5 in the formation
			if(document.getElementById('c2'+positions[saveteamh]+saveteami)){	//if a player is in this position (c2gk1, ...)
				//extract the id of this player from (myteamid45, ...)  and append this to onfield 
				onfield.push(document.getElementById('c2'+positions[saveteamh]+saveteami).firstChild.lastChild.id.substring(8));
			}
		}
	}
	//ajax call to save the changes
	$.ajax({
		url: "loggedin.php",
		data: {
			SAVECHANGES: onfield, CURRGW: currentgameweek, USN: usn	//send the onfield array, current gw and username
		},
		success: 
		function(result){
			//alert the user about changes being saved and reload the page
			alert('changes saved');
			location.reload();
		}
	});
}

//adding drop function to on field players in my team
function ifSomethingDropsOnMe(){
	//initialize information about the player that's having the drop
	droppedstarter = this;
	starterpos = droppedstarter.className.substr(2);						//position of starter (gk, def, mid, fwd)
	starterimg = droppedstarter.firstChild.firstChild.cloneNode(true);		//image element of starter
	startername = droppedstarter.firstChild.children[1].innerHTML;			//name of starter
	starterpts = droppedstarter.firstChild.children[2].innerHTML;			//points of starter
	starterid = this.id;													//precise position of starter (c2fwd1, c2fwd2, ...)
	starterplid = this.firstChild.lastChild.id;								//player id of the starter (myteamid45, ...)
	
	//if the sub and starter are of the same position then simply swap these two
	if(starterpos==subpos){
		samePosSubstitution();
	}
	else{
		//if the dragged player is the sub-keeper then don't allow substitution
		if(draggedsub.id=='c2sub1'){
			alert('cannot swap goalkeeper with an outfield player');
			return;
		}
		
		//check how many many players of the position that the about to be subbed player belongs to are there
		var stposcounter = 0;
		for(var stposi = 1; stposi<=5; stposi++){
			if(document.getElementById('c2' + starterpos + stposi)){
				stposcounter++;		//increment this if a player in this position exists in the starting 11
			}
		}
		
		//note the next vacant space of the position that the initial substitute belongs to
		for(var stposi = 1; stposi<=5; stposi++){
			if(document.getElementById('c2' + subpos + stposi)){
				
			}
			else{
				break;
			}
		}
		
		//set the minimum number of on-field players for different positions
		if(starterpos=='gk'){
			stposmin = 1;
		}
		else if(starterpos=='def'){
			stposmin = 3;
		}
		else if(starterpos=='mid'){
			stposmin = 3;
		}
		else if(starterpos=='fwd'){
			stposmin = 1;
		}
		
		//if the position currently has exactly the minimum allowed for that position then don't allow substitution
		if(stposcounter==stposmin){
			alert('you must have at least ' + stposmin + ' ' + starterpos + '(s)');
			return;
		}
		else{
			//substitution among different positions is happening
			var imgwrapperst = document.createElement('imgwrapperst');		//this was needed to handle the image element
			imgwrapperst.appendChild(starterimg.cloneNode(true));			//the newly subbed was the initial starter
			
			//html for the about to be substitute
			var subhtml = '<div class = "c2'+starterpos+'" id = "'+subid+'">';
			subhtml+= 			'<div id = "'+subid+'pack">';
			subhtml+= 				imgwrapperst.innerHTML;					//append the image element itself
			subhtml+= 				'<div class = "c2namepts1" id = "'+subid+'name">';
			subhtml+=					startername;						//the newly subbed was the initial starter
			subhtml+=				'</div>';
			subhtml+= 				'<div class = "c2namepts2" id = "'+subid+'pts">';
			subhtml+=					starterpts;							//the newly subbed was the initial starter
			subhtml+=				'</div>';
			subhtml+=				'<div id = "myteamid'+starterplid.substring(8)+'"></div>';
			subhtml+=			'</div>';
			subhtml+=	  '</div>';
			
			//remove the player from field
			$( droppedstarter ).replaceWith(''); 
			
			//replace the initial sub's information with the just substituted player
			$( draggedsub ).replaceWith(subhtml);
			
			//and make it do whatever subs are supposed to do when dragged
			makeSubsDraggable();
			
			//this is done to get the html of some fucking image element
			var imgwrappersub = document.createElement('imgwrappersub');
			imgwrappersub.appendChild(subimg.cloneNode(true));				//the new starter was initially a sub
			
			//html for the new on-field player
			var starterhtml = '<div class = "c2'+subpos+'" id = "c2'+subpos+stposi+'">';
			starterhtml+= 			'<div id = "c2'+subpos+stposi+'pack">';
			starterhtml+= 				imgwrappersub.innerHTML;			//append the image element itself
			starterhtml+= 				'<div class = "c2namepts1" id = c2"'+subpos+stposi+'name">';
			starterhtml+=					subname;						//the new starter was initially a sub
			starterhtml+=				'</div>';
			starterhtml+= 				'<div class = "c2namepts2" id = c2"'+subpos+stposi+'pts">';
			starterhtml+=					subpts;							//the new starter was initially a sub
			starterhtml+=				'</div>';
			starterhtml+=				'<div id = "myteamid'+subplid.substring(8)+'"></div>';
			starterhtml+=			'</div>';
			starterhtml+=	  '</div>';
			
			//insert into the appropriate position (c2mids, c2fwds...)
			var insertinto = document.getElementById('c2' + subpos +'s');
			insertinto.insertAdjacentHTML('beforeend', starterhtml);
			
			//as this is the last child of insertinto, prepare it for having drops
			//just for prevent default
			insertinto.lastChild.addEventListener('dragstart', 
			function(event){
				event.preventDefault();
			});
			//just for prevent default
			insertinto.lastChild.addEventListener('dragenter', 
			function(event){
				event.preventDefault();
			});
			//just for prevent default
			insertinto.lastChild.addEventListener('dragover', 
			function(event){
				event.preventDefault();
			});
			//this is it!
			insertinto.lastChild.addEventListener('drop', ifSomethingDropsOnMe);
		}
	}
	
	//if there's exactly one forward, then make him appear in the center (because justified looks shit)
	if(document.getElementById('c2fwds').children.length==1){
		$("#c2fwds").css("-webkit-box-pack", "center");
	}
	else{
		$("#c2fwds").css("-webkit-box-pack", "justify");
	}
	
	//enable the save changes button
	enableSaveChanges();
	
	//save team on click event
	document.getElementById('savechanges').addEventListener('click', savingMe);
}

//the starters cannot be dragged, but i don't know if it has anything to do with this function
function makeStartersDroppable(strpos){
	for(var fieldi = 1; fieldi<=5; fieldi++){
		//attach drop event listeners to all the on field player of position strpos
		if(document.getElementById(strpos + fieldi)){
			//just for the prevent default part
			document.getElementById(strpos + fieldi).addEventListener('dragstart', 
			function(event){
				event.preventDefault();
			});
			//just for the prevent default part
			document.getElementById(strpos + fieldi).addEventListener('dragover', 
			function(event){
				event.preventDefault();
			});
			//just for the prevent default part
			document.getElementById(strpos + fieldi).addEventListener('dragenter', 
			function(event){
				event.preventDefault();
			});
			//if something is dropped on an on field player
			document.getElementById(strpos + fieldi).addEventListener('drop', ifSomethingDropsOnMe);
		}
	}
}

//showing the team in the my team section
function callMyTeam(gweek){
	console.log("calling my team with" + gweek);
	//first call for the keepers
	$.ajax({
		url: "loggedin.php",
		data: {
			GETGKS: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			document.getElementById('c2gks').innerHTML = result;	//add the html in c2gks div (c2 for content-2, my team)
			makeStartersDroppable('c2gk');							//making them droppable (adding drop events, ...)
		}
	});
	//then the defenders
	$.ajax({
		url: "loggedin.php",
		data: {
			GETDEFS: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			document.getElementById('c2defs').innerHTML = result;	//add the html in c2defs div (c2 for content-2, my team)
			makeStartersDroppable('c2def');							//making them droppable (adding drop events, ...)
		}
	});
	//the midfielders
	$.ajax({
		url: "loggedin.php",
		data: {
			GETMIDS: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			document.getElementById('c2mids').innerHTML = result;	//add the html in c2mids div (c2 for content-2, my team)
			makeStartersDroppable('c2mid');							//making them droppable (adding drop events, ...)
		}
	});
	//aand the strikers
	$.ajax({
		url: "loggedin.php",
		data: {
			GETFWDS: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			document.getElementById('c2fwds').innerHTML = result;	//add the html in c2fwds div (c2 for content-2, my team)
			makeStartersDroppable('c2fwd');							//making them droppable (adding drop events, ...)
			
			//if there's exactly one forward, then make him appear in the center (because justified looks shit)
			if(document.getElementById('c2fwds').children.length==1){
				$("#c2fwds").css("-webkit-box-pack", "center");
			}
			else{
				$("#c2fwds").css("-webkit-box-pack", "justify");
			}
		}
	});
	//don't forget the bench
	$.ajax({
		url: "loggedin.php",
		data: {
			GETSUBS: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			document.getElementById('c2benches').innerHTML = result;	//add the html in c2fwds div (c2 for content-2, my team)
			//add drag event listeners to these subs
			makeSubsDraggable();
		}
	});
	
	//disable the save changes button initially
	disableSaveChanges();
}

function callMyTeamForTransfers(){
	clubstransfers = {BOU: 0, ARS: 0, BUR: 0, CHE: 0, CRY: 0, 
			 EVE: 0, HUL: 0, LIV: 0, LEI: 0, MCI: 0,
			 MUN: 0, MID: 0, STO: 0, SOU: 0, SWA: 0,
			 SUN: 0, WAT: 0, WBA: 0, WHU: 0, TOT: 0};
	//get current team value
	$.ajax({
		url: "loggedin.php",
		data: {
			TOTALVAL: '?', CURRGW: currentgameweek, USN: usn
		},
		success: 
		function(result){
			squadvaluetransfers = parseFloat(result);
			inthebank = 100.0 - squadvaluetransfers;
			//show the in the bank value in money remaining section of transfers
			document.getElementById('c4mractual').innerHTML = (inthebank).toFixed(1) + '$';
			playersselected = 15;	//initially all players are in the team
		}
	});
	
	//first call for the keepers
	$.ajax({
		url: "loggedin.php",
		data: {
			GETGKS4: '?', CURRGW: currentgameweek, USN: usn
		},
		success: 
		function(result){
			document.getElementById('c4gks').innerHTML = result;
			
			viewPlayersForTransfers(1, 'all');
			
			//remove players from squad on click
			var c4gks = document.getElementById('c4gks').children;
			for(var c4posi = 0; c4posi<c4gks.length; c4posi++){
				c4gks[c4posi].addEventListener('mouseover', giveMeHand);
				c4gks[c4posi].addEventListener('click', removeFromTeamTransfers);
			}
			
			//note which clubs the players arrived from
			var f1 = (document.getElementById('c4gk1pack'));
			var f1directory = f1.firstChild.src;
			var f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4gk2pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
		}
	});
	//then the defenders
	$.ajax({
		url: "loggedin.php",
		data: {
			GETDEFS4: '?', CURRGW: currentgameweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c4defs').innerHTML = result;
			
			viewPlayersForTransfers(1, 'all');
			
			//remove players from squad on click
			var c4defs = document.getElementById('c4defs').children;
			for(var c4posi = 0; c4posi<c4defs.length; c4posi++){
				c4defs[c4posi].addEventListener('mouseover', giveMeHand);
				c4defs[c4posi].addEventListener('click', removeFromTeamTransfers);
			}
			
			//note which clubs the players arrived from
			var f1 = (document.getElementById('c4def1pack'));
			var f1directory = f1.firstChild.src;
			var f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4def2pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4def3pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4def4pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4def5pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
		}
	});
	//the midfielders
	$.ajax({
		url: "loggedin.php",
		data: {
			GETMIDS4: '?', CURRGW: currentgameweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c4mids').innerHTML = result;
			
			viewPlayersForTransfers(1, 'all');
			
			//remove players from squad on click
			var c4mids = document.getElementById('c4mids').children;
			for(var c4posi = 0; c4posi<c4mids.length; c4posi++){
				c4mids[c4posi].addEventListener('mouseover', giveMeHand);
				c4mids[c4posi].addEventListener('click', removeFromTeamTransfers);
			}
			
			//note which clubs the players arrived from
			var f1 = (document.getElementById('c4mid1pack'));
			var f1directory = f1.firstChild.src;
			var f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4mid2pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4mid3pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4mid4pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4mid5pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
		}
	});
	//aand the strikers
	$.ajax({
		url: "loggedin.php",
		data: {
			GETFWDS4: '?', CURRGW: currentgameweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c4fwds').innerHTML = result;
			
			viewPlayersForTransfers(1, 'all');
			
			//remove players from squad on click
			var c4fwds = document.getElementById('c4fwds').children;
			for(var c4posi = 0; c4posi<c4fwds.length; c4posi++){
				c4fwds[c4posi].addEventListener('mouseover', giveMeHand);
				c4fwds[c4posi].addEventListener('click', removeFromTeamTransfers);
			}
			
			//note which clubs the players arrived from
			var f1 = (document.getElementById('c4fwd1pack'));
			var f1directory = f1.firstChild.src;
			var f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4fwd2pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
			
			f1 = (document.getElementById('c4fwd3pack'));
			f1directory = f1.firstChild.src;
			f1club = "";
			f1club += f1directory[f1directory.length-8];
			f1club += f1directory[f1directory.length-7];
			f1club += f1directory[f1directory.length-6];
			clubstransfers[f1club]++;
		}
	});
	console.log(clubstransfers);
	//save changes when clicked on save transfers, but initially disable the button
	disableSaveTransfers();
	document.getElementById('savetransfers').addEventListener('click', saveTeamTransfers);
}

function callPoints(gweek){
	//first call for the keepers
	$.ajax({
		url: "loggedin.php",
		data: {
			GETGKS3: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c3gks').innerHTML = result;
			//makeStartersDroppable('c3gk');
		}
	});
	//then the defenders
	$.ajax({
		url: "loggedin.php",
		data: {
			GETDEFS3: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c3defs').innerHTML = result;
			//makeStartersDroppable('c3def');
		}
	});
	//the midfielders
	$.ajax({
		url: "loggedin.php",
		data: {
			GETMIDS3: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c3mids').innerHTML = result;
			//makeStartersDroppable('c3mid');
		}
	});
	//aand the strikers
	$.ajax({
		url: "loggedin.php",
		data: {
			GETFWDS3: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c3fwds').innerHTML = result;
			//makeStartersDroppable('c3fwd');
			
			//if there's exactly one forward, then make him appear in the center (because justified looks shit)
			if(document.getElementById('c3fwds').children.length==1){
				$("#c3fwds").css("-webkit-box-pack", "center");
			}
			else{
				$("#c3fwds").css("-webkit-box-pack", "justify");
			}
		}
	});
	//don't forget the bench
	$.ajax({
		url: "loggedin.php",
		data: {
			GETSUBS3: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			//console.log(result);
			document.getElementById('c3benches').innerHTML = result;
			//add drag event listeners to these subs
			//makeSubsDraggable();
		}
	});
	//show total points
	$.ajax({
		url: "loggedin.php",
		data: {
			TOTPTS: '?', CURRGW: gweek, USN: usn
		},
		success: 
		function(result){
			document.getElementById('c3actualpts').innerHTML = result;
		}
	});
	//disable the save changes button initially
	//disableSaveChanges();
}

function nextWeekClickKorle(){
	document.getElementById('c3next').addEventListener('click', 
	function(){
		weektoshow++;
		document.getElementById('c3gwlekha').innerHTML = 'Gameweek ' + weektoshow;
		document.getElementById('c3prev').style.visibility = 'visible';
		callPoints(weektoshow);
		//console.log(weektoshow, currentgameweek);
		if(weektoshow==currentgameweek-1){
			$('#c3next').css('visibility', 'hidden');
		}
	});
}

function prevWeekClickKorle(){
	document.getElementById('c3prev').addEventListener('click', 
	function(){
		$('#c3next').css('visibility', 'visible');
		weektoshow--;
		document.getElementById('c3gwlekha').innerHTML = 'Gameweek ' + weektoshow;
		if(weektoshow==1){
			document.getElementById('c3prev').style.visibility = 'hidden';
		}
		callPoints(weektoshow);
	});
}

function retrieveUsername(){
	//get the username from the session variable and store it in var usn
	$.ajax({
		url: "loggedin.php",
		data: {
			USER: '?'
		},
		success: function(result){
			usn = result;
			console.log(usn);
			//check if user already has a team; if so, hide the team selection section
			$.ajax({
				url: "loggedin.php",
				data: {
					TEXISTS: usn
				},
				success: 
				function(result){
					if(result=='1'){
						//team exists, so hide the team selection section
						hideTeamSelection();
						
						//call ajax to put players in their places
						callMyTeam(currentgameweek);
						
						//show points when clicked on points
						document.getElementById('tab-3').addEventListener('click', 
						function(){
							callPoints(currentgameweek-1);
							
							//viewing the gameweek number correctly
							weektoshow = currentgameweek-1;
							document.getElementById('c3gwlekha').innerHTML = 'Gameweek ' + weektoshow;
							
							//if first gw, then hide previous button
							if(weektoshow==1){
								document.getElementById('c3prev').style.visibility = 'hidden';
							}
							//if week to show is the last gameweek hide next button
							if(weektoshow==currentgameweek-1){
								$('#c3next').css('visibility', 'hidden');
							}
							
							//show team for next week and bring back previous button if hidden
							nextWeekClickKorle();
							
							//show team for previous week and hide previous button if gameweek 1 team is shown
							prevWeekClickKorle();
						});
						
						//show transfers tab
						document.getElementById('tab-4').addEventListener('click', 
						function(){
							//view entire team
							callMyTeamForTransfers();
							
							//setup for pagination
							paginationSetupForTransfers();
							
							//show players on the right hand side
							viewPlayersForTransfers(1, 'all');
							
							//search by name setup
							searchByNameSetupForTransfers();
							
						});
					}
					else{
						$('#tab-2').hide();
						$('#tab-3').hide();
						$('#tab-4').hide();
						$('#tab-5').hide();
					}
				}
			});
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

function giveMeHand(){
	this.style.cursor = 'hand';
}

function removeFromTeam(){
	//reduce the squad value by the removed player's price
	squadvalue-=parseFloat(this.firstChild.childNodes[4].className);
	
	//retrieve the club of the player removed and decrement the number of players selected from this club
	var directory = this.firstChild.children[1].src;
	var actualdirectory = "";
	actualdirectory += directory[directory.length-8];
	actualdirectory += directory[directory.length-7];
	actualdirectory += directory[directory.length-6];
	clubs[actualdirectory]--;
	
	//make the row at the rhs white from paste so that the player can be selected again
	var tr = (this.firstChild.firstChild.className);
	if(tr){
		if(document.getElementById(tr)){
			document.getElementById(tr).style.background = 'white';
		}
	}
	//create the new html with blank jerseys 
	var newhtml2 = 		'<div class = "faka" id = "' + this.id + 'pack">';
		newhtml2+=			'<img id = "' + this.id + 'img" src = fakas.webp />';
		newhtml2+=			'<div class = "namepts1" id = "' + this.id +'name">';
		newhtml2+=				'Add Player';
		newhtml2+=			'</div>';
		newhtml2+=			'<div class = "namepts2" id = "' + this.id + 'pts">';
		newhtml2+=				'?';
		newhtml2+=			'</div>';
		newhtml2+=		'</div>';
	
	//and replace this with the old one
	this.innerHTML = newhtml2;
	//disable the save team button as team cannot be saved with player removed
	disableSaveTeam();
	
	//show total players
	totalplayers--;
	$('#psactual').html(totalplayers + '/15')
	
	//show money remaining
	$('#mractual').html((100.0 - squadvalue).toFixed(1) + '$')
}

function removeFromTeamTransfers(){
	console.log(clubstransfers);
	//increase the squad value by the removed player's price and show it in "In the bank"
	inthebank+=parseFloat(this.firstChild.children[2].innerHTML);
	//squadvaluetransfers-=parseFloat(this.firstChild.children[2].innerHTML);
	document.getElementById('c4mractual').innerHTML = inthebank.toFixed(1) + '$';
	//console.log(parseFloat(this.firstChild.children[2].innerHTML));
	
	//retrieve the club of the player removed and decrement the number of players selected from this club
	var directory = this.firstChild.firstChild.src;
	var actualdirectory = "";
	actualdirectory += directory[directory.length-8];
	actualdirectory += directory[directory.length-7];
	actualdirectory += directory[directory.length-6];
	console.log(actualdirectory);
	clubstransfers[actualdirectory]--;
	
	//make the row at the rhs white from paste so that the player can be selected again
	var tr = this.firstChild.children[3].id.substring(8);
	//console.log('whatever tr is:' + tr);
	if(tr){
		//console.log('tr not null:'+tr);
		if(document.getElementById('c4'+tr)){
			//console.log('yoyoyo' + document.getElementById('c4' + tr));
			document.getElementById('c4'+tr).style.background = 'white';
		}
	}
	
	//create the new html with blank jerseys 
		var newhtml2= '<div class = "fakawrapper" id = "' + this.id + '" >';
		newhtml2+= 		'<div class = "faka" id = "' + this.id + 'pack">';
		newhtml2+=			'<img id = "' + this.id + 'img" src = fakas.webp />';
		newhtml2+=			'<div class = "namepts1" id = "' + this.id +'name">';
		newhtml2+=				'Add Player';
		newhtml2+=			'</div>';
		newhtml2+=			'<div class = "namepts2" id = "' + this.id + 'pts">';
		newhtml2+=				'?';
		newhtml2+=			'</div>';
		newhtml2+=		'</div>';
		newhtml2+=	 '</div>';
	
	//and replace this with the old one
	$( this ).replaceWith(newhtml2);
	
	//disable the save transfers button as team cannot be saved with a player removed
	disableSaveTransfers();
	
	//decrement players selected by 1
	playersselected--;
	document.getElementById('c4psactual').innerHTML = playersselected + '/15';
}

function addToTeam(){
	//initializing important variables
	var currg = document.getElementsByClassName('gk').length;
	var currd = document.getElementsByClassName('def').length;
	var currm = document.getElementsByClassName('mid').length;
	var currf = document.getElementsByClassName('fwd').length;
	var name = this.children[1].firstChild.firstChild.data;
	var club = this.children[1].children[2].firstChild.data;
	var pos = this.children[2].firstChild.data.toLowerCase();
	var id = this.id;
	var pr = parseFloat(this.children[3].innerHTML);

	//check if player is already in team
	if(this.style.background=='rgb(0, 255, 135)'){
		alert('already in your team');
		return;
	}
	
	//check that at most 3 players are selected from a club
	if(clubs[club]==3 ){
		   alert('Cannot have more than 3 players from one team');
		   return;
	}
	
	//retrieve the position of the selected player
	for(positioni = 0; positioni<4; positioni++){
		if(pos==positions[positioni]){
			break;
		}
	}
		
	//set maximum limit and current number of players for the position selected 	
	if(positioni==0){
		positionmax = 2;
		positioncurr = currg;
	}
	else if(positioni==1){
		positionmax = 5;
		positioncurr = currd;
	}
	else if(positioni==2){
		positionmax = 5;
		positioncurr = currm;
	}
	else{
		positionmax = 3;
		positioncurr = currf;
	}
	
	var actualpositioni = positions[positioni];
	
	//if the current number of players exceed the maximum limit for that position then don't add player
	if(positioncurr==(positionmax)){
		alert('you cannot have more than ' + positionmax + ' ' + actualpositioni + 's');
		return;
	}
	
	//check that total value of squad is within 100m
	if((squadvalue + pr)>100.0){
		alert('squad value must be within 100.0m');
		return;
	}
	
	//search for vacancies in the position and set poscurr as the first vacant position
	for(var scanningvacancies = 1; scanningvacancies<=positionmax; scanningvacancies++){
		if(document.getElementById(actualpositioni + scanningvacancies + 'pack').className != actualpositioni){
			poscurr = actualpositioni + scanningvacancies;
			break;
		}
	}
	
	//creating the html to insert at position poscurr
	var newhtml = 		'<div class = "' + pos + '" id = "' + poscurr + 'pack">';
		newhtml+=			'<div class = ' + id + '></div>';
		newhtml+=			'<img id = "' + poscurr + 'img" src = ' + club + '.webp />';
		newhtml+=			'<div class = "namepts1" id = "' + poscurr +'name">';
		newhtml+=				name;
		newhtml+=			'</div>';
		newhtml+=			'<div class = "namepts2" id = "' + poscurr + 'pts">';
		newhtml+=				pr.toFixed(1);
		newhtml+=			'</div>';
		newhtml+=			'<div class = "' + pr +'">' + '</div>';
		newhtml+=		'</div>';
	
	clubs[club]++;
	
	//insert the new html into div id = poscurr; e.g. <div id = "gk1"><div id = "gk1pack">...</div></div>
	//and highlight the row of the player that was selected
	document.getElementById(poscurr).innerHTML = newhtml;	
	this.style.background = '#00FF87';
	
	//set event listeners to this new player. click to remove from squad
	document.getElementById(poscurr).addEventListener('mouseover', giveMeHand);
	document.getElementById(poscurr).addEventListener('click', removeFromTeam);
	document.getElementById(poscurr).cl = club;
	
	//increase the value of the squad
	squadvalue+=pr;	
	//enable save your team button if 15 players are chosen
	if(currg+currd+currm+currf==14){
		enableSaveTeam();
	}
	//show players selected
	totalplayers++;
	$('#psactual').html(totalplayers + '/15');
	//show money remaining
	$('#mractual').html((100 - squadvalue).toFixed(1) + '$');
}

function addToTeamTransfers(){
	console.log(clubstransfers);
	//initializing important variables
	var currg = document.getElementsByClassName('c4gk').length;
	var currd = document.getElementsByClassName('c4def').length;
	var currm = document.getElementsByClassName('c4mid').length;
	var currf = document.getElementsByClassName('c4fwd').length;
	var name = this.children[1].firstChild.firstChild.data;
	var club = this.children[1].children[2].firstChild.data;
	var pos = this.children[2].firstChild.data.toLowerCase();
	var id = this.id;
	var pr = parseFloat(this.children[3].innerHTML);

	//check if player is already in team
	if(this.style.background=='rgb(0, 255, 135)'){
		alert('already in your team');
		return;
	}
	
	//check that at most 3 players are selected from a club
	if(clubstransfers[club]==3 ){
		   alert('Cannot have more than 3 players from one team');
		   return;
	}
	
	//retrieve the position of the selected player
	for(positioni = 0; positioni<4; positioni++){
		if(pos==positions[positioni]){
			break;
		}
	}
		
	//set maximum limit and current number of players for the position selected 	
	if(positioni==0){
		positionmax = 2;
		positioncurr = currg;
	}
	else if(positioni==1){
		positionmax = 5;
		positioncurr = currd;
	}
	else if(positioni==2){
		positionmax = 5;
		positioncurr = currm;
	}
	else{
		positionmax = 3;
		positioncurr = currf;
	}
	
	var actualpositioni = positions[positioni];
	
	//if the current number of players exceed the maximum limit for that position then don't add player
	if(positioncurr==(positionmax)){
		alert('you cannot have more than ' + positionmax + ' ' + actualpositioni + 's');
		return;
	}
	
	//check that total value of squad is within 100m
	if((pr)>inthebank){
		alert('not enough money in the bank');
		return;
	}
	
	//search for vacancies in the position and set poscurr as the first vacant position
	for(var scanningvacancies = 1; scanningvacancies<=positionmax; scanningvacancies++){
		//console.log(document.getElementById('c4' + actualpositioni + scanningvacancies).className, 'c4' + actualpositioni);
		if(document.getElementById('c4' + actualpositioni + scanningvacancies).className != ('c4' + actualpositioni)){
			poscurr = 'c4' + actualpositioni + scanningvacancies;
			break;
		}
	}
	
	//console.log(document.getElementById('c4fwd3pack').parentNode.className);
	//console.log(name, club, pos, pr, id);
	//console.log(poscurr);
	
	//creating the htmlto insert at position poscurr
	var newhtml =   '<div class = "c4' + actualpositioni + '" id = "' + poscurr + '" >';
	    newhtml+= 		'<div id = "' + poscurr + 'pack">';
		newhtml+=			'<img id = "' + poscurr + 'img" src = ' + club + '.webp />';
		newhtml+=			'<div class = "c4namepts1" id = "' + poscurr +'name">';
		newhtml+=				name;
		newhtml+=			'</div>';
		newhtml+=			'<div class = "c4namepts2" id = "' + poscurr + 'pts">';
		newhtml+=				pr.toFixed(1);
		newhtml+=			'</div>';
		newhtml+=			'<div id = "c4myteamid' + id.substring(4) +'">' + '</div>';
		newhtml+=		'</div>';
		newhtml+=	'</div>';
	
	clubstransfers[club]++;
	//console.log(newhtml);
	//insert the new html into div id = poscurr; e.g. <div id = "gk1"><div id = "gk1pack">...</div></div>
	//and highlight the row of the player that was selected
	//document.getElementById(poscurr).innerHTML = newhtml;
	var fakanode = document.getElementById(poscurr + 'pack').parentNode;
	$(fakanode).replaceWith(newhtml);
	//console.log(document.getElementById('c4fwds'));
	this.style.background = '#00FF87';
	
	//set event listeners to this new player. click to remove from squad
	document.getElementById(poscurr).addEventListener('mouseover', giveMeHand);
	document.getElementById(poscurr).addEventListener('click', removeFromTeamTransfers);
	
	//increase the value of the squad and update "In the bank"
	inthebank-=pr;
	document.getElementById('c4mractual').innerHTML = (inthebank).toFixed(1) + '$';
	
	//increment players selected by 1 and show in "Players selected"
	playersselected++;
	document.getElementById('c4psactual').innerHTML = playersselected + '/15';
	
	//enable save your transfers button if 15 players are chosen
	if(currg+currd+currm+currf==14){
		enableSaveTransfers();
	}
}


function forEachRowViewed(element){
	//turn background to paste if player is already in team
	var contains = document.getElementsByClassName(element.id).length;
	if(contains==1){
		element.style.background = 'rgb(0, 255, 135)';
	}
	//bringing hand cursor when hovered over rows
	element.addEventListener('mouseover', giveMeHand);
	//adding player to team
	element.addEventListener('click', addToTeam);
}

function clickHeadingToViewFull(){
	for(var chi = 0; chi<4; chi++){
		var pi = positions[chi];
		var pislekha = document.getElementById(pi + 'slekha');
		if(pislekha){
			pislekha.addEventListener('click', 
			function(){
				positionforpagination = this.id.slice(0, -6);
				viewPlayers(1, positionforpagination.toUpperCase());
			});
		}
	}
}

function viewPlayers(pageNo, pos){
	positionforpagination = pos;
	$.ajax({
		url: "loggedin.php",
		data: {
			PAGENO: pageNo, POS: pos
		},
		success: 
		function(result){
			//showing results with categories
			document.getElementById('viewedplayers').innerHTML = result;
			//showing players from specific categories upon clicking on the heading
			clickHeadingToViewFull();
			
			//for each row viewed on the rhs
			var viewedp = document.getElementsByClassName('viewedp');
			Array.from(viewedp).forEach(forEachRowViewed);
		}
	});
}	

function forEachRowViewedTransfers(element){
	//turn background to paste if player is already in team
	if(document.getElementById('c4myteam' + element.id.substring(2))){
		element.style.background = 'rgb(0, 255, 135)';
	}
	//bringing hand cursor when hovered over rows
	element.addEventListener('mouseover', giveMeHand);
	//adding player to team
	element.addEventListener('click', addToTeamTransfers);
}

function clickHeadingToViewFullTransfers(){
	for(var chi = 0; chi<4; chi++){
		var pi = positions[chi];
		var pislekha = document.getElementById('c4' + pi + 'slekha');
		if(pislekha){
			pislekha.addEventListener('click', 
			function(){
				positionforpagination = (this.id.slice(0, -6)).substring(2);
				viewPlayersForTransfers(1, positionforpagination.toUpperCase());
			});
		}
	}
}

function viewPlayersForTransfers(pageNo, pos){
	positionforpagination = pos;
	$.ajax({
		url: "loggedin.php",
		data: {
			PAGENO4: pageNo, POS: pos
		},
		success: 
		function(result){
			//showing results with categories
			document.getElementById('c4viewedplayers').innerHTML = result;
			//showing players from specific categories upon clicking on the heading
			clickHeadingToViewFullTransfers();
			
			//for each row viewed on the rhs
			var c4viewedp = document.getElementsByClassName('c4viewedp');
			Array.from(c4viewedp).forEach(forEachRowViewedTransfers);
		}
	});
}	

function paginationSetup(){
	positionforpagination = 'all'
	for(var pagingi = 1; pagingi<=10; pagingi++){
		document.getElementById('paging' + pagingi).addEventListener('click', 
		function(){
			viewPlayers(parseInt(this.innerHTML), positionforpagination);
		});
	}
}

function paginationSetupForTransfers(){
	positionforpagination = 'all'
	for(var pagingi = 1; pagingi<=10; pagingi++){
		document.getElementById('c4paging' + pagingi).addEventListener('click', 
		function(){
			viewPlayersForTransfers(parseInt(this.innerHTML), positionforpagination);
		});
	}
}

function searchByNameSetup(){
	var view = document.getElementById('viewinput').value;
	var searchinput = document.getElementById('searchinput');
	searchinput.addEventListener('keyup', 
	function(){
		$.ajax({
			url: "loggedin.php",
			data: {
				SEARCH: searchinput.value
			},
			success: function(result){
				document.getElementById('viewedplayers').innerHTML = result;
				clickHeadingToViewFull();
				var viewedp = document.getElementsByClassName('viewedp');
				Array.from(viewedp).forEach(forEachRowViewed);
			}
		});
	});
}

function searchByNameSetupForTransfers(){
	var view = document.getElementById('c4viewinput').value;
	var searchinput = document.getElementById('c4searchinput');
	searchinput.addEventListener('keyup', 
	function(){
		$.ajax({
			url: "loggedin.php",
			data: {
				SEARCH4: searchinput.value
			},
			success: function(result){
				document.getElementById('c4viewedplayers').innerHTML = result;
				clickHeadingToViewFullTransfers();
				var c4viewedp = document.getElementsByClassName('c4viewedp');
				Array.from(c4viewedp).forEach(forEachRowViewedTransfers);
			}
		});
	});
}

function initVars(){
	positions = ['gk', 'def', 'mid', 'fwd'];
	clubs = {BOU: 0, ARS: 0, BUR: 0, CHE: 0, CRY: 0, 
			 EVE: 0, HUL: 0, LIV: 0, LEI: 0, MCI: 0,
			 MUN: 0, MID: 0, STO: 0, SOU: 0, SWA: 0,
			 SUN: 0, WAT: 0, WBA: 0, WHU: 0, TOT: 0};
	clubstransfers = {BOU: 0, ARS: 0, BUR: 0, CHE: 0, CRY: 0, 
			 EVE: 0, HUL: 0, LIV: 0, LEI: 0, MCI: 0,
			 MUN: 0, MID: 0, STO: 0, SOU: 0, SWA: 0,
			 SUN: 0, WAT: 0, WBA: 0, WHU: 0, TOT: 0};
	squadvalue = 0.0;
	currentgameweek = 1;
	totalplayers = 0;
	fixturetoshow = currentgameweek;
}

function createTeam(){
	document.getElementById('saveteam').addEventListener('click', 
	function(){
		var gk1id = document.getElementById('gk1pack').firstChild.className.substr(2);
		var gk2id = document.getElementById('gk2pack').firstChild.className.substr(2);
		
		var def1id = document.getElementById('def1pack').firstChild.className.substr(2);
		var def2id = document.getElementById('def2pack').firstChild.className.substr(2);
		var def3id = document.getElementById('def3pack').firstChild.className.substr(2);
		var def4id = document.getElementById('def4pack').firstChild.className.substr(2);
		var def5id = document.getElementById('def5pack').firstChild.className.substr(2);
		
		var mid1id = document.getElementById('mid1pack').firstChild.className.substr(2);
		var mid2id = document.getElementById('mid2pack').firstChild.className.substr(2);
		var mid3id = document.getElementById('mid3pack').firstChild.className.substr(2);
		var mid4id = document.getElementById('mid4pack').firstChild.className.substr(2);
		var mid5id = document.getElementById('mid5pack').firstChild.className.substr(2);
		
		var fwd1id = document.getElementById('fwd1pack').firstChild.className.substr(2);
		var fwd2id = document.getElementById('fwd2pack').firstChild.className.substr(2);
		var fwd3id = document.getElementById('fwd3pack').firstChild.className.substr(2);
		
		$.ajax({
			url: "loggedin.php",
			data: {
				CREATETEAM: '?', GK1: gk1id, GK2: gk2id, DEF1: def1id, DEF2: def2id, DEF3: def3id, DEF4: def4id, DEF5: def5id,
				MID1: mid1id, MID2: mid2id, MID3: mid3id, MID4: mid4id, MID5: mid5id, 
				FWD1: fwd1id, FWD2: fwd2id, FWD3: fwd3id,
				GWEEK: currentgameweek, USN: usn
			},
			success: 
			function(result){
				alert(result);
				location.reload();
			}
		});
	});
}

function saveTeamTransfers(){
	//console.log(document.getElementById('c4gk1pack').children[3].id.substr(10));
	var gk1id = document.getElementById('c4gk1pack').children[3].id.substr(10);
	var gk2id = document.getElementById('c4gk2pack').children[3].id.substr(10);
	
	var def1id = document.getElementById('c4def1pack').children[3].id.substr(10);
	var def2id = document.getElementById('c4def2pack').children[3].id.substr(10);
	var def3id = document.getElementById('c4def3pack').children[3].id.substr(10);
	var def4id = document.getElementById('c4def4pack').children[3].id.substr(10);
	var def5id = document.getElementById('c4def5pack').children[3].id.substr(10);
	
	var mid1id = document.getElementById('c4mid1pack').children[3].id.substr(10);
	var mid2id = document.getElementById('c4mid2pack').children[3].id.substr(10);
	var mid3id = document.getElementById('c4mid3pack').children[3].id.substr(10);
	var mid4id = document.getElementById('c4mid4pack').children[3].id.substr(10);
	var mid5id = document.getElementById('c4mid5pack').children[3].id.substr(10);
	
	var fwd1id = document.getElementById('c4fwd1pack').children[3].id.substr(10);
	var fwd2id = document.getElementById('c4fwd2pack').children[3].id.substr(10);
	var fwd3id = document.getElementById('c4fwd3pack').children[3].id.substr(10);
	
	
	$.ajax({
		url: "loggedin.php",
		data: {
			SAVETR: '?', GK1: gk1id, GK2: gk2id, DEF1: def1id, DEF2: def2id, DEF3: def3id, DEF4: def4id, DEF5: def5id,
			MID1: mid1id, MID2: mid2id, MID3: mid3id, MID4: mid4id, MID5: mid5id, 
			FWD1: fwd1id, FWD2: fwd2id, FWD3: fwd3id,
			GWEEK: currentgameweek, USN: usn
		},
		success: 
		function(result){
			alert(result);
			location.reload();
		}
	});
}

function onClickSignout(){
	$('#tab-6').click(function(event){
		$.ajax({
			url: "loggedin.php",
			data: {
				SIGNOUT: '?'
			},
			success: function(result){
				window.location="flexbox.html";
				
			}
		});
		//console.log(usn);
	});
}

function fixturesButtonsReady(){
	$('#prevbutton').click(function(){
		fixturetoshow--;
		viewFixtures(fixturetoshow);
	});
	$('#nextbutton').click(function(){
		fixturetoshow++;
		viewFixtures(fixturetoshow);
	});
}

function viewFixtures(gw){
	$('#majherlekha').html('Gameweek ' + gw + '<br /><br /><img src = "pl-long.png" />');
	if(gw==38){
		$('#nextbutton').css('visibility', 'hidden');
	}
	else if(gw==1){
		$('#prevbutton').css('visibility', 'hidden');
	}
	else{
		$('#prevbutton').css('visibility', 'visible');
		$('#nextbutton').css('visibility', 'visible');
	}
	$.ajax({
		url: "loggedin.php",
		data: {
			FIXNRES: gw
		},
		success: function(result){
			$('#actualfixtures').html(result);
			$('.fixturerow').mouseover(function(){
				$(this).css('background', '#E90052');
				$(this).css('color', 'white');
				$(this).css('transition', 'background 0.3s');
				$(this).children('.score').css('background', 'white');
				$(this).children('.score').css('color', '#E90052');
			}).mouseout(function(){
				$(this).css('background', 'white');
				$(this).css('color', 'black');
				$(this).css('transition', 'background 0.3s');
				$(this).children('.score').css('background', 'rgb(56, 0, 60)');
				$(this).children('.score').css('color', 'white');
				
			});
		}
	});
}

function rhsuserfullname(){
	console.log('called');
	$.ajax({
		url: "loggedin.php",
		data: {
			USER: '?'
		},
		success: function(result){
			$.ajax({
				url: "loggedin.php",
				data: {
					FULLNAME: result
				},
				success: function(result2){
					//console.log(result2);
					$('#usersfullname').html(result2);
					$('#pointsusersfullname').html(result2);
				}
			});
		}
	});
	//ajax call to get full name
	
}

function rhsmyteam(){
	//show user's full name at the top with country flag
	rhsuserfullname();
	//show user's last gameweek points
}

function hidepointsingw1()
{
	if(currentgameweek==1){
		$('#content-3').hide();
		$('#tab-3').hide();
		return;
	}
}

function startDoingStuffs(){
	//draw shapes beside the navigation bar
	canvasStuffs();
	//set required variables about clubs and positions
	initVars();
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
	//fucking pagination
	paginationSetup();
	//requesting ajax for initial player names
	viewPlayers(1, 'all');
	//search by name
	searchByNameSetup();
	//create team if no team has been registered yet
	createTeam();
	//adding event listeners to previous and next buttons
	fixturesButtonsReady();
	//initializing the right hand side of my team
	rhsmyteam();
	//viewing fixtures
	viewFixtures(currentgameweek);
	//signing user out
	onClickSignout();
	//if current gameweek is 1, then hide points section
	hidepointsingw1();
}