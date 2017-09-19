<?php
	function connect($mysql_dbname){
		$mysql_hostname = 'localhost';
		$mysql_user = 'root';
		$mysql_password = '';
		$mysql_error = 'Failed to connect!';
		$mysql_dbaseerror = 'Could not connect with database!';
		
		mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die($mysql_error);
		
		mysql_select_db($mysql_dbname) or die($mysql_dbaseerror);
	}
?>