<?php
	session_start();
	
	require_once 'dbasehelper.inc.php';
	connect('fpl');
	
	if(isset($_GET['USERNAME']) && isset($_GET['PASSWORD'])){
		$username = $_GET['USERNAME'];
		$password = $_GET['PASSWORD'];
		$query = "SELECT `Password` FROM `users` WHERE `Username` = '$username'";
		if($query_run = mysql_query($query)){
			$query_row = mysql_fetch_assoc($query_run);
			$pass = $query_row['Password'];
			if($pass==$password){
				$_SESSION['user'] = $username;
				echo 'Y';
			}
			else{
				echo 'N';
			}
		}
		else{
			echo 'Failed!';
		}
	}
?>