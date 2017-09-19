<?php

	require_once 'dbasehelper.inc.php';
	
	connect('fpl');

	if(isset($_GET['USERNAME'])){
		$username = $_GET['USERNAME'];
		$query = "SELECT * FROM `users`
				  WHERE username = '$username'";
		if($query_run = mysql_query($query)){
			$query_row = mysql_num_rows($query_run);
			echo $query_row;
		}
		else{
			echo 'Failed';
		}
	}
	
	if(isset($_GET['USERMAIL'])){
		$usermail = $_GET['USERMAIL'];
		$query = "SELECT * FROM `users`
				  WHERE email = '$usermail'";
		if($query_run = mysql_query($query)){
			$query_row = mysql_num_rows($query_run);
			echo $query_row;
		}
		else{
			echo 'Failed';
		}
	}

	if(isset($_GET['USERINFO'])){
		$userinfo = $_GET['USERINFO'];
		$name = $userinfo[0];
		$username = $userinfo[1];
		$email = $userinfo[2];
		$password = $userinfo[3];
		$country = $userinfo[4];
		$club = $userinfo[5];
		$query = "INSERT INTO `users` (name, username, email, password, country, club)
				  VALUES ('$name', '$username', '$email', '$password', '$country', '$club')";
		if($query_run = mysql_query($query)){
			echo 'suxxess';
		}
		else{
			echo 'fail';
		}
	}
	
?>