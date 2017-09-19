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

function registerButtonWorks(){
	$('#registerbutton').css('background', 'rgb(56, 0, 60)');
	$('#registerbutton').mouseover(function(){
		$(this).css('cursor', 'hand');
		$(this).css('background', 'black');
	}).mouseout(function(){
		$(this).css('background', 'rgb(56, 0, 60)');
	});
}

function disableRegisterButton(){
	$('#registerbutton').attr('disabled', true);
	$('#registerbutton').css('background', 'gray');
}

function inputCheck(){
	if(input1==1 && input2==1 && input3==1 && input4==1 && input6==1 && input7==1){
		$('#registerbutton').attr('disabled', false);
		registerButtonWorks();
	}
	else{
		disableRegisterButton();
	}
}

function checkUserInputs(){
	$('#input1').keyup(function(){
		$('#status1').css('visibility', 'visible');
		var name = $(this).val();
		if(name==""){
			$('#status1').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
			input1 = 0;
			inputCheck();
		}
		else{
			$('#status1').css('background', 'green').html('<img class = "wbox wboxflexible" src = "correct.svg" />');
			input1 = 1;
			inputCheck();
		}
	});
	$('#input2').keyup(function(){
		var username = $(this).val();
		//ajax call to check if username exists
		if(username!=""){
			$.ajax({
				url: "signup.php",
				data: {
					USERNAME: username
				},
				success: 
				function(result){
					$('#status2').css('visibility', 'visible');
					if(result=='1'){
						$('#status2').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
						input2 = 0;
						inputCheck();
					}
					else{
						$('#status2').css('background', 'green').html('<img class = "wbox wboxflexible" src = "correct.svg" />');
						input2 = 1;
						inputCheck();
					}
				}
			});
		}
		else{
			$('#status2').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
			input2 = 0;
			inputCheck();
		}
	});
	$('#input3').keyup(function(){
		var usermail = $(this).val();
		//ajax call to check if username exists
		if(usermail!=""){
			$.ajax({
				url: "signup.php",
				data: {
					USERMAIL: usermail
				},
				success: 
				function(result){
					$('#status3').css('visibility', 'visible');
					if(result=='1'){
						$('#status3').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
						input3 = 0;
						inputCheck();
					}
					else{
						$('#status3').css('background', 'green').html('<img class = "wbox wboxflexible" src = "correct.svg" />');
						input3 = 1;
						inputCheck();
					}
				}
			});
		}
		else{
			$('#status3').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
			input3 = 0;
			inputCheck();
		}
	});
	$('#input4').keyup(function(){
		$('#status4').css('visibility', 'visible');
		var name = $(this).val();
		if(name==""){
			$('#status4').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
			input4 = 0;
			inputCheck();
		}
		else{
			$('#status4').css('background', 'green').html('<img class = "wbox wboxflexible" src = "correct.svg" />');
			input4 = 1;
			inputCheck();
		}
	});
		
	$('#input6').keyup(function(){
		$('#status6').css('visibility', 'visible');
		var name = $(this).val();
		if(name==""){
			$('#status6').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
			input6 = 0;
			inputCheck();
		}
		else{
			$('#status6').css('background', 'green').html('<img class = "wbox wboxflexible" src = "correct.svg" />');
			input6 = 1;
			inputCheck();
		}
	});
	$('#input7').keyup(function(){
		$('#status7').css('visibility', 'visible');
		var name = $(this).val();
		if(name==""){
			$('#status7').css('background', '#E90052').html('<img class = "wbox wboxflexible" src = "wrong.svg" />');
			input7 = 0;
			inputCheck();
		}
		else{
			$('#status7').css('background', 'green').html('<img class = "wbox wboxflexible" src = "correct.svg" />');
			input7 = 1;
			inputCheck();
		}
	});
}

function initInputs(){
	input1 = 0;	input2 = 0;	input3 = 0;	input4 = 0;	input6 = 0;	input7 = 0;
}

function registerUser(){
	$('#registerbutton').click(function(){
		info = [];
		info.push($('#input1').val());
		info.push($('#input2').val());
		info.push($('#input3').val());
		info.push($('#input4').val());
		info.push($('#input6').val());
		info.push($('#input7').val());
		$.ajax({
			url: "signup.php",
			data: {
				USERINFO: info
			},
			success: 
			function(result){
				if(result=='suxxess'){
					alert('Registration successful');
					window.location.href = "flexbox.html";
				}
			}
		});
	});
}

$(window).on('load', function(){
	canvasStuffs();
	disableRegisterButton();
	initInputs();
	checkUserInputs();
	registerUser();
});