<?php
	session_start();						//needed for using sessions 
	require_once 'dbasehelper.inc.php';		//needed to initialize stuffs about database
	connect('fpl');							//connect to the fpl's database
	
	//send username to javascript or 'No session' if user is not logged in
	if(isset($_GET['USER'])){
		if(isset($_SESSION['user'])){
			echo $_SESSION['user'];
		}
		else{
			echo 'No session';
		}
	}
	
	//showing user's full name in my team's rhs
	if(isset($_GET['FULLNAME'])){
		$username = $_GET['FULLNAME'];
		$query = "SELECT name FROM users WHERE username = '$username'";
		if($query_run = mysql_query($query)){
			$response = '';
			while($query_row = mysql_fetch_assoc($query_run)){
				$response.=$query_row['name'];
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}
	
	//sign a user out
	if(isset($_GET['SIGNOUT'])){
		unset($_SESSION['user']);
	}
	
	//show player list on the right for initial squad selection
	if(isset($_GET['PAGENO']) && isset($_GET['POS'])){
		$pageno = $_GET['PAGENO'];	//get the page number selected
		$pos = $_GET['POS'];		//get the position the user wants to view
		$pagenogk = ($pageno-1)*3;	//viewing to be started from this keeper (0, 3, 6, ...)
		
		//if the user didn't specify any position, the view id, name, position, club, price and total points 
		//for all (first gks(3), then defs(10), then mids(12), then fwds(6)) 
		//all these details are required in the process later
		if($pos=='all'){
			$querygk = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'GK') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_gk`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenogk, 3";
			if($query_run = mysql_query($querygk)){
				$response = '';
				$response .= '<div id = "viewedgks"><table id = "gkstable">';	//table to be inserted into html
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){		//if the query returned any rows, then create the heading (Goalkeepers, ...)
					$response .= '<th></th><th id = "gkslekha">Goalkeepers</th><th>$</th><th>Pts</th>';
				}
				//create separate rows in the table for each viewed player
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';		//end the table tag
				echo $response;						//send this entire response to javascript
			}
			else{
				echo 'Failed';
			}
			$pagenodef = ($pageno-1)*10;			//viewing to be started from this defender (0, 10, 20, ...)
			$querydef = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'DEF') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_def`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenodef, 10";
			if($query_run = mysql_query($querydef)){
				$response = '';
				$response .= '<div id = "vieweddefs"><table id = "defstable">';		//table to be inserted into html
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){		//if the query returned any rows, then create the heading (Goalkeepers, ...)
					$response .= '<th></th><th id = "defslekha">Defenders</th><th>$</th><th>Pts</th>';
				}
				//create separate rows in the table for each viewed player
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';		//end the table tag
				echo $response;						//send this entire response to javascript
			}
			else{
				echo 'Failed';
			}
			$pagenomid = ($pageno-1)*12;			//viewing to be started from this midfielder (0, 12, 24, ...)
			$querymid = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*5) + (`ai`*3) + (`cs`*1) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'MID') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_mid`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenomid, 10";
			if($query_run = mysql_query($querymid)){
				$response = '';
				$response .= '<div id = "viewedmids"><table id = "midstable">';		//table to be inserted into html
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){		//if the query returned any rows, then create the heading (Goalkeepers, ...)
					$response .= '<th></th><th id = "midslekha">Midfielders</th><th>$</th><th>Pts</th>';
				}
				//create separate rows in the table for each viewed player
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';		//end the table tag
				echo $response;						//send this entire response to javascript
			}
			else{
				echo 'Failed';
			}
			$pagenofwd = ($pageno-1)*6;		//viewing to be started from this forward (0, 6, 12, ...)
			$queryfwd = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*4) + (`ai`*3) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'FWD') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_fwd`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenofwd, 10";
			if($query_run = mysql_query($queryfwd)){
				$response = '';
				$response .= '<div id = "viewedfwds"><table id = "fwdstable">';		//table to be inserted into html
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){		//if the query returned any rows, then create the heading (Goalkeepers, ...)
					$response .= '<th></th><th id = "fwdslekha">Forwards</th><th>$</th><th>Pts</th>';
				}
				//create separate rows in the table for each viewed player
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';		//end the table tag
				echo $response;						//send this entire response to javascript
			}
			else{
				echo 'Failed';
			}
		}
		//if user wanted to see specific positioned players 
		else{
			$pagenopos = ($pageno-1)*30;	//viewing to be started from this player (0, 30, 60, ...)
			//create query upon which position was chosen
			if($pos=='GK'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'GK') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_gk`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			else if($pos=='DEF'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'DEF') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_def`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			else if($pos=='MID'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*5) + (`ai`*3) + (`cs`*1) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'MID') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_mid`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			else if($pos=='FWD'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*4) + (`ai`*3) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'FWD') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_fwd`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			if($query_run = mysql_query($querypos)){
				$response = '';
				
				//create divs and tables for html depending on the position chosen
				if($pos=='GK'){
					$response .= '<div id = "viewedgks"><table id = "gkstable">';
				}
				else if($pos=='DEF'){
					$response .= '<div id = "vieweddefs"><table id = "defstable">';
				}
				else if($pos=='MID'){
					$response .= '<div id = "viewedmids"><table id = "midstable">';
				}
				else if($pos=='FWD'){
					$response .= '<div id = "viewedfwds"><table id = "fwdstable">';
				}
				$returnedrows = mysql_num_rows($query_run);
				
				//if any row was returned then create the heading (Goalkeepers, ...) depending on the position chosen
				if($returnedrows!=0){
					if($pos=='GK'){
						$response .= '<th></th><th id = "gkslekha">Goalkeepers</th><th>$</th><th>Pts</th>';
					}
					else if($pos=='DEF'){
						$response .= '<th></th><th id = "defslekha">Defenders</th><th>$</th><th>Pts</th>';
					}
					else if($pos=='MID'){
						$response .= '<th></th><th id = "midslekha">Midfielders</th><th>$</th><th>Pts</th>';
					}
					else if($pos=='FWD'){
						$response .= '<th></th><th id = "fwdslekha">Forwards</th><th>$</th><th>Pts</th>';
					}
				}
				
				//create separate rows for each player
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response = $response.('</table></div>');		//end the table tag
				echo $response;									//send the entire response to javascript
			}
			else{
				echo 'Failed';
			}
		}
	}
	
	//show player list on the right for transfers. works similarly as the one for initial squad selection,
	//but has c4 in many fields in the html (c4 for content-4, transfers)
	if(isset($_GET['PAGENO4']) && isset($_GET['POS'])){
		$pageno = $_GET['PAGENO4'];
		$pos = $_GET['POS'];
		$pagenogk = ($pageno-1)*3;
		if($pos=='all'){
			$querygk = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'GK') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_gk`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenogk, 3";
			if($query_run = mysql_query($querygk)){
				$response = '';
				$response .= '<div id = "c4viewedgks"><table id = "c4gkstable">';
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){
					$response .= '<th></th><th id = "c4gkslekha">Goalkeepers</th><th>$</th><th>Pts</th>';
				}
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';
				echo $response;
			}
			else{
				echo 'Failed';
			}
			$pagenodef = ($pageno-1)*10;
			$querydef = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'DEF') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_def`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenodef, 10";
			if($query_run = mysql_query($querydef)){
				$response = '';
				$response .= '<div id = "c4vieweddefs"><table id = "c4defstable">';
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){
					$response .= '<th></th><th id = "c4defslekha">Defenders</th><th>$</th><th>Pts</th>';
				}
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';
				echo $response;
			}
			else{
				echo 'Failed';
			}
			$pagenomid = ($pageno-1)*12;
			$querymid = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*5) + (`ai`*3) + (`cs`*1) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'MID') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_mid`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenomid, 12";
			if($query_run = mysql_query($querymid)){
				$response = '';
				$response .= '<div id = "c4viewedmids"><table id = "c4midstable">';
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){
					$response .= '<th></th><th id = "c4midslekha">Midfielders</th><th>$</th><th>Pts</th>';
				}
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';
				echo $response;
			}
			else{
				echo 'Failed';
			}
			$pagenofwd = ($pageno-1)*6;
			$queryfwd = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
			(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
			(ceil(`mp`/59) + (`gs`*4) + (`ai`*3) - 
			(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
			FROM `weekly_stat_players`, `players` 
			WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'FWD') 
			ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_fwd`
			GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenofwd, 10";
			if($query_run = mysql_query($queryfwd)){
				$response = '';
				$response .= '<div id = "c4viewedfwds"><table id = "c4fwdstable">';
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){
					$response .= '<th></th><th id = "c4fwdslekha">Forwards</th><th>$</th><th>Pts</th>';
				}
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response .= '</table></div>';
				echo $response;
			}
			else{
				echo 'Failed';
			}
		}
		else{
			$pagenopos = ($pageno-1)*30;
			if($pos=='GK'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'GK') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_gk`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			else if($pos=='DEF'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'DEF') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_def`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			else if($pos=='MID'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*5) + (`ai`*3) + (`cs`*1) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'MID') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_mid`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			else if($pos=='FWD'){
				$querypos = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
				(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
				(ceil(`mp`/59) + (`gs`*4) + (`ai`*3) - 
				(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
				FROM `weekly_stat_players`, `players` 
				WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'FWD') 
				ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_fwd`
				GROUP BY `name` ORDER BY `total_points` DESC LIMIT $pagenopos, 30";
			}
			if($query_run = mysql_query($querypos)){
				$response = '';
				if($pos=='GK'){
					$response .= '<div id = "c4viewedgks"><table id = "c4gkstable">';
				}
				else if($pos=='DEF'){
					$response .= '<div id = "c4vieweddefs"><table id = "c4defstable">';
				}
				else if($pos=='MID'){
					$response .= '<div id = "c4viewedmids"><table id = "c4midstable">';
				}
				else if($pos=='FWD'){
					$response .= '<div id = "c4viewedfwds"><table id = "c4fwdstable">';
				}
				$returnedrows = mysql_num_rows($query_run);
				if($returnedrows!=0){
					if($pos=='GK'){
						$response .= '<th></th><th id = "c4gkslekha">Goalkeepers</th><th>$</th><th>Pts</th>';
					}
					else if($pos=='DEF'){
						$response .= '<th></th><th id = "c4defslekha">Defenders</th><th>$</th><th>Pts</th>';
					}
					else if($pos=='MID'){
						$response .= '<th></th><th id = "c4midslekha">Midfielders</th><th>$</th><th>Pts</th>';
					}
					else if($pos=='FWD'){
						$response .= '<th></th><th id = "c4fwdslekha">Forwards</th><th>$</th><th>Pts</th>';
					}
				}
				while($query_row = mysql_fetch_assoc($query_run)){
					$pts = $query_row['total_points'];
					$pr = $query_row['pr'];
					$fullname = $query_row['name'];
					$separatenames = explode(" ", $fullname);
					$lastname = $separatenames[sizeof($separatenames)-1];
					
					$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
					$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
					$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
					$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
					$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
					$response .=	'<td>'.$pts.'</td>';
					$response .= '</tr>';
				}
				$response = $response.('</table></div>');
				echo $response;
			}
			else{
				echo 'Failed';
			}
		}
	}
	
	//searching players by name in initial team selection
	if(isset($_GET['SEARCH'])){
		$search = $_GET['SEARCH'];		//things user typed in the search box
		
		//finding keepers with a match. this works just like the ones before
		$querygk = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'GK') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_gk`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($querygk)){
			$response = '';
			$response .= '<div id = "viewedgks"><table id = "gkstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "gkslekha">Goalkeepers</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
		
		//finding defenders with a match. this works just like the ones before
		$querydef = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'DEF') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_def`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($querydef)){
			$response = '';
			$response .= '<div id = "vieweddefs"><table id = "defstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "defslekha">Defenders</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
		
		//finding midfielders with a match. this works just like the ones before
		$querymid = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*5) + (`ai`*3) + (`cs`*1) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'MID') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_mid`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($querymid)){
			$response = '';
			$response .= '<div id = "viewedmids"><table id = "midstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "midslekha">Midfielders</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
		
		//finding forwards with a match. this works just like the ones before
		$queryfwd = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*4) + (`ai`*3) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'FWD') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_fwd`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($queryfwd)){
			$response = '';
			$response .= '<div id = "viewedfwds"><table id = "fwdstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "fwdslekha">Forwards</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}
	
	//searching players by name in transfers. works similarly as the one for initial squad selection,
	//but has c4 in many fields in the html (c4 for content-4, transfers)
	if(isset($_GET['SEARCH4'])){
		$search = $_GET['SEARCH4'];
		$querygk = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'GK') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_gk`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($querygk)){
			$response = '';
			$response .= '<div id = "c4viewedgks"><table id = "c4gkstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "c4gkslekha">Goalkeepers</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
		
		$querydef = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'DEF') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_def`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($querydef)){
			$response = '';
			$response .= '<div id = "c4vieweddefs"><table id = "c4defstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "c4defslekha">Defenders</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
		
		$querymid = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*5) + (`ai`*3) + (`cs`*1) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'MID') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_mid`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($querymid)){
			$response = '';
			$response .= '<div id = "c4viewedmids"><table id = "c4midstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "c4midslekha">Midfielders</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
		
		$queryfwd = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*4) + (`ai`*3) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'FWD') AND (`players`.`name` LIKE '%$search%')
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_fwd`
		GROUP BY `name` ORDER BY `total_points` DESC";
		if($query_run = mysql_query($queryfwd)){
			$response = '';
			$response .= '<div id = "c4viewedfwds"><table id = "c4fwdstable">';
			$returnedrows = mysql_num_rows($query_run);
			if($returnedrows!=0){
				$response .= '<th></th><th id = "c4fwdslekha">Forwards</th><th>$</th><th>Pts</th>';
			}
			while($query_row = mysql_fetch_assoc($query_run)){
				$pts = $query_row['total_points'];
				$pr = $query_row['pr'];
				$fullname = $query_row['name'];
				$separatenames = explode(" ", $fullname);
				$lastname = $separatenames[sizeof($separatenames)-1];
				
				$response .= '<tr class = "c4viewedp" id = "c4id'.$query_row['id'].'">';
				$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
				$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
				$response .=	'<td style = "display: none">'.$query_row['position'].'</td>';
				$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
				$response .=	'<td>'.$pts.'</td>';
				$response .= '</tr>';
			}
			$response .= '</table></div>';
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}

	//creating and saving a user's team for the first time
	if(isset($_GET['CREATETEAM']) && isset($_GET['GK1']) && isset($_GET['GK2']) && 
	   isset($_GET['DEF1']) && isset($_GET['DEF2']) && isset($_GET['DEF3']) && isset($_GET['DEF4']) && isset($_GET['DEF5']) &&
	   isset($_GET['MID1']) && isset($_GET['MID2']) && isset($_GET['MID3']) && isset($_GET['MID4']) && isset($_GET['MID5']) &&
	   isset($_GET['FWD1']) && isset($_GET['FWD2']) && isset($_GET['FWD3']) && 
	   isset($_GET['GWEEK']) && isset($_GET['USN'])){
	   
		//getting all these id's into variables. the gw, and the user's username is retrieved too
		$gk1 = $_GET['GK1']; $gk2 = $_GET['GK2'];
		$def1 = $_GET['DEF1']; $def2 = $_GET['DEF2']; $def3 = $_GET['DEF3']; $def4 = $_GET['DEF4']; $def5 = $_GET['DEF5']; 
		$mid1 = $_GET['MID1']; $mid2 = $_GET['MID2']; $mid3 = $_GET['MID3']; $mid4 = $_GET['MID4']; $mid5 = $_GET['MID5'];
		$fwd1 = $_GET['FWD1']; $fwd2 = $_GET['FWD2']; $fwd3 = $_GET['FWD3'];
		$gweek = $_GET['GWEEK']; $usn = $_GET['USN'];
		
		//create new table with the user's username
		$createtablequery = "CREATE TABLE $usn(
								gw int,
								plid varchar(255),
								pos varchar(255),
								forb varchar(255) 
							)";
		if($query_run = mysql_query($createtablequery)){
			//insert team for the current gameweek with an initial 4-4-2 formation
			$insertquery = "INSERT INTO $usn(`gw`, `plid`, `pos`, `forb`) VALUES
							('$gweek', '$gk1', 'gk', 'field'),
							('$gweek', '$gk2', 'gk', 'bench'),
							
							('$gweek', '$def1', 'def', 'field'),
							('$gweek', '$def2', 'def', 'field'),
							('$gweek', '$def3', 'def', 'field'),
							('$gweek', '$def4', 'def', 'field'),
							('$gweek', '$def5', 'def', 'bench'),
							
							('$gweek', '$mid1', 'mid', 'field'),
							('$gweek', '$mid2', 'mid', 'field'),
							('$gweek', '$mid3', 'mid', 'field'),
							('$gweek', '$mid4', 'mid', 'field'),
							('$gweek', '$mid5', 'mid', 'bench'),
							
							('$gweek', '$fwd1', 'fwd', 'field'),
							('$gweek', '$fwd2', 'fwd', 'field'),
							('$gweek', '$fwd3', 'fwd', 'bench')";
			if($query_run = mysql_query($insertquery)){
				echo 'your team has been successfully saved';
			}
			else{
				echo 'superfailure';
			}
		}
		else{
			echo 'Failed';
		}
	}
	 
	//saving team in my team   
	if(isset($_GET['SAVECHANGES']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		//getting the id's of all the players in the user's squad, as well as the gw and user'r username
		$savechanges = $_GET['SAVECHANGES'];
		$currgw = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		
		//first, bench all the players.
		$querymakeallb = "UPDATE $usn
						  SET forb = 'bench'
						  WHERE gw = '$currgw'";
		if($query_run = mysql_query($querymakeallb)){
			echo 'first query run';
			for($i=0; $i<11; $i++){		//take all on-field players one by one
				$currplid = $savechanges[$i];
				
				//and save this as an on-field player in the database
				$querymakefieldf = "UPDATE $usn
								    SET forb = 'field'
								    WHERE gw = '$currgw' AND plid = '$currplid'";
				if($query_run_2 = mysql_query($querymakefieldf)){
					echo 'supersuxexes';
				}
				else{
					echo 'second one failed';
				}
			}
		}
		else{
			echo 'first query failed';
		}
	}   
 	
	//saving team in transfers
	if(isset($_GET['SAVETR']) && isset($_GET['GK1']) && isset($_GET['GK2']) && 
	   isset($_GET['DEF1']) && isset($_GET['DEF2']) && isset($_GET['DEF3']) && isset($_GET['DEF4']) && isset($_GET['DEF5']) &&
	   isset($_GET['MID1']) && isset($_GET['MID2']) && isset($_GET['MID3']) && isset($_GET['MID4']) && isset($_GET['MID5']) &&
	   isset($_GET['FWD1']) && isset($_GET['FWD2']) && isset($_GET['FWD3']) && 
	   isset($_GET['GWEEK']) && isset($_GET['USN'])){
	   
		//getting the id's of all the players in the user's squad, as well as the gw and user'r username
		$gk1 = $_GET['GK1']; $gk2 = $_GET['GK2'];
		$def1 = $_GET['DEF1']; $def2 = $_GET['DEF2']; $def3 = $_GET['DEF3']; $def4 = $_GET['DEF4']; $def5 = $_GET['DEF5']; 
		$mid1 = $_GET['MID1']; $mid2 = $_GET['MID2']; $mid3 = $_GET['MID3']; $mid4 = $_GET['MID4']; $mid5 = $_GET['MID5'];
		$fwd1 = $_GET['FWD1']; $fwd2 = $_GET['FWD2']; $fwd3 = $_GET['FWD3'];
		$gweek = $_GET['GWEEK']; $usn = $_GET['USN'];
		
		//first delete the entire team for the current week
		$updatetablequery = "DELETE FROM $usn
							WHERE gw = '$gweek'";
		if($query_run = mysql_query($updatetablequery)){
			//then insert the new team with an initial 4-4-2 formation
			$insertquery = "INSERT INTO $usn(`gw`, `plid`, `pos`, `forb`) VALUES
							('$gweek', '$gk1', 'gk', 'field'),
							('$gweek', '$gk2', 'gk', 'bench'),
							
							('$gweek', '$def1', 'def', 'field'),
							('$gweek', '$def2', 'def', 'field'),
							('$gweek', '$def3', 'def', 'field'),
							('$gweek', '$def4', 'def', 'field'),
							('$gweek', '$def5', 'def', 'bench'),
							
							('$gweek', '$mid1', 'mid', 'field'),
							('$gweek', '$mid2', 'mid', 'field'),
							('$gweek', '$mid3', 'mid', 'field'),
							('$gweek', '$mid4', 'mid', 'field'),
							('$gweek', '$mid5', 'mid', 'bench'),
							
							('$gweek', '$fwd1', 'fwd', 'field'),
							('$gweek', '$fwd2', 'fwd', 'field'),
							('$gweek', '$fwd3', 'fwd', 'bench')";
			if($query_run = mysql_query($insertquery)){
				echo 'your team has been successfully saved';
			}
			else{
				echo 'superfailure';
			}
		}
		else{
			echo 'Failed to delete';
		}
	}

	//let javascript know if this user already has a team 
	if(isset($_GET['TEXISTS'])){
		$texists = $_GET['TEXISTS'];
		$querytableexists = "SHOW TABLES LIKE '$texists'";
		if($query_run = mysql_query($querytableexists)){
			echo mysql_num_rows($query_run);	//either 1 or 0
		}
		else{
			echo 'failed';
		}
	}   
	
	//draw for my team. the javascript calls separate ajaxes for each position, including the bench
    //first, draw the goalkeepers	
	if(isset($_GET['GETGKS']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettinggks = "SELECT `plid` FROM $usn WHERE `pos` = 'gk' AND `forb` = 'field'";	//first retrieve the ids
		$response = '';
		if($query_run = mysql_query($querygettinggks)){
			$counter = 1;	//first player to have specific position of 1 (c2gk1, c2def1, c2def2, ...) and so on
			while($query_row = mysql_fetch_assoc($query_run)){	//do the following for each player retrieved...
				$plid = $query_row['plid'];		
				//getting name, fixture and club for this player
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'GK')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){	//using the information gained to create the html 
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$gkname = $separatenames[sizeof($separatenames)-1];
						
						$gkclub = $query_row_2['club'];
						$gkpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						//note that the html has c2 in many fields (c2 for content-2, my team)
						$response.= '<div class = "c2gk" id = "c2gk'.$counter.'">';
						$response.=	 	'<div id = "c2gk'.$counter.'pack">';
						$response.=			'<img id = "c2gk'.$counter.'img" src = "'.$gkclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2gk'.$counter.'name">';
						$response.=				$gkname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2gk'.$counter.'pts">';
						$response.=				$gkpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;		//increment counter so that next player goes into the next position
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   
	
	//then draw the defenders, just like the previous one
	if(isset($_GET['GETDEFS']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingdefs = "SELECT `plid` FROM $usn WHERE `pos` = 'def' AND `forb` = 'field'";
		$response = '';
		if($query_run = mysql_query($querygettingdefs)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'DEF')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$defname = $separatenames[sizeof($separatenames)-1];
						
						$defclub = $query_row_2['club'];
						$defpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						//note that the html has c2 in many fields (c2 for content-2, my team)
						$response.= '<div class = "c2def" id = "c2def'.$counter.'">';
						$response.=	 	'<div id = "c2def'.$counter.'pack">';
						$response.=			'<img id = "c2def'.$counter.'img" src = "'.$defclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2def'.$counter.'name">';
						$response.=				$defname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2def'.$counter.'pts">';
						$response.=				$defpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}      
	
	//draw the midfielders, just like the previous one
	if(isset($_GET['GETMIDS']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingmids = "SELECT `plid` FROM $usn WHERE `pos` = 'mid' AND `forb` = 'field'";
		$response = '';
		if($query_run = mysql_query($querygettingmids)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'MID')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$midname = $separatenames[sizeof($separatenames)-1];
						
						$midclub = $query_row_2['club'];
						$midpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						//note that the html has c2 in many fields (c2 for content-2, my team)
						$response.= '<div class = "c2mid" id = "c2mid'.$counter.'">';
						$response.=	 	'<div id = "c2mid'.$counter.'pack">';
						$response.=			'<img id = "c2mid'.$counter.'img" src = "'.$midclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2mid'.$counter.'name">';
						$response.=				$midname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2mid'.$counter.'pts">';
						$response.=				$midpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   
	
	//draw the forwards
	if(isset($_GET['GETFWDS']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingfwds = "SELECT `plid` FROM $usn WHERE `pos` = 'fwd' AND `forb` = 'field'";
		$response = '';
		if($query_run = mysql_query($querygettingfwds)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'FWD')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$fwdname = $separatenames[sizeof($separatenames)-1];
						
						$fwdclub = $query_row_2['club'];
						$fwdpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						//note that the html has c2 in many fields (c2 for content-2, my team)
						$response.= '<div class = "c2fwd" id = "c2fwd'.$counter.'">';
						$response.=	 	'<div id = "c2fwd'.$counter.'pack">';
						$response.=			'<img id = "c2fwd'.$counter.'img" src = "'.$fwdclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2fwd'.$counter.'name">';
						$response.=				$fwdname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2fwd'.$counter.'pts">';
						$response.=				$fwdpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   

	//finally, draw the subs
	if(isset($_GET['GETSUBS']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$counter = 1;
		$querygettinggks = "SELECT `plid` FROM $usn WHERE `pos` = 'gk' AND `forb` = 'bench'";
		$response = '';
		if($query_run = mysql_query($querygettinggks)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'GK')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$gkname = $separatenames[sizeof($separatenames)-1];
						
						$gkclub = $query_row_2['club'];
						$gkpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						//note that the html has c2 in many fields (c2 for content-2, my team)
						$response.= '<div class = "c2gk" id = "c2sub'.$counter.'">';
						$response.=	 	'<div id = "c2sub'.$counter.'pack">';
						$response.=			'<img id = "c2sub'.$counter.'img" src = "'.$gkclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2sub'.$counter.'name">';
						$response.=				$gkname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2sub'.$counter.'pts">';
						$response.=				$gkpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		
		$querygettingdefs = "SELECT `plid` FROM $usn WHERE `pos` = 'def' AND `forb` = 'bench'";
		if($query_run = mysql_query($querygettingdefs)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'DEF')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$defname = $separatenames[sizeof($separatenames)-1];
						
						$defclub = $query_row_2['club'];
						$defpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						$response.= '<div class = "c2def" id = "c2sub'.$counter.'">';
						$response.=	 	'<div id = "c2sub'.$counter.'pack">';
						$response.=			'<img id = "c2sub'.$counter.'img" src = "'.$defclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2sub'.$counter.'name">';
						$response.=				$defname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2sub'.$counter.'pts">';
						$response.=				$defpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		
		$querygettingmids = "SELECT `plid` FROM $usn WHERE `pos` = 'mid' AND `forb` = 'bench'";
		if($query_run = mysql_query($querygettingmids)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'MID')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$midname = $separatenames[sizeof($separatenames)-1];
						
						$midclub = $query_row_2['club'];
						$midpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						$response.= '<div class = "c2mid" id = "c2sub'.$counter.'">';
						$response.=	 	'<div id = "c2sub'.$counter.'pack">';
						$response.=			'<img id = "c2sub'.$counter.'img" src = "'.$midclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2sub'.$counter.'name">';
						$response.=				$midname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2sub'.$counter.'pts">';
						$response.=				$midpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		
		$querygettingfwds = "SELECT `plid` FROM $usn WHERE `pos` = 'fwd' AND `forb` = 'bench'";
		if($query_run = mysql_query($querygettingfwds)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name AS name, players.club AS club, 
				fixtures_and_results.home AS home, fixtures_and_results.away AS away
				FROM weekly_stat_players, players, fixtures_and_results
				WHERE (weekly_stat_players.id = players.id) AND (weekly_stat_players.gw = '$gwmt') AND (players.position = 'FWD')
				AND (fixtures_and_results.gw = '$gwmt') AND (players.id = '$plid') AND
				(fixtures_and_results.home = players.club OR fixtures_and_results.away = players.club)";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$fwdname = $separatenames[sizeof($separatenames)-1];
						
						$fwdclub = $query_row_2['club'];
						$fwdpts = $query_row_2['home'].' vs '.$query_row_2['away'];
						
						$response.= '<div class = "c2fwd" id = "c2sub'.$counter.'">';
						$response.=	 	'<div id = "c2sub'.$counter.'pack">';
						$response.=			'<img id = "c2sub'.$counter.'img" src = "'.$fwdclub.'.webp" />';
						$response.=			'<div class = "c2namepts1" id = "c2sub'.$counter.'name">';
						$response.=				$fwdname;
						$response.=			'</div>';
						$response.=			'<div class = "c2namepts2" id = "c2sub'.$counter.'pts">';
						$response.=				$fwdpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		echo $response;
	}   
	
	//draw for points. this time too, separate ajax calls are made by javascript. 
	//the functionalities are similar to showing for my team.
	//first, draw the goalkeepers (for points)	
	if(isset($_GET['GETGKS3']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettinggks = "SELECT `plid` FROM $usn WHERE `pos` = 'gk' AND `forb` = 'field'";
		$response = '';
		if($query_run = mysql_query($querygettinggks)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*6) + (ai*3) + (cs*4) - floor(gc/2) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'GK') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$gkname = $separatenames[sizeof($separatenames)-1];
						
						$gkclub = $query_row_2['club'];
						$gkpts = $query_row_2['points'];
						
						//note that the html has c3 in many fields (c3 for content-3, points)
						$response.= '<div class = "c3gk" id = "c3gk'.$counter.'">';
						$response.=	 	'<div id = "c3gk'.$counter.'pack">';
						$response.=			'<img id = "c3gk'.$counter.'img" src = "'.$gkclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3gk'.$counter.'name">';
						$response.=				$gkname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3gk'.$counter.'pts">';
						$response.=				$gkpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   
	
	//draw the defenders (for points)
	if(isset($_GET['GETDEFS3']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingdefs = "SELECT `plid` FROM $usn WHERE `pos` = 'def' AND `forb` = 'field'";
		$response = '';
		if($query_run = mysql_query($querygettingdefs)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*6) + (ai*3) + (cs*4) - floor(gc/2) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'DEF') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$defname = $separatenames[sizeof($separatenames)-1];
						
						$defclub = $query_row_2['club'];
						$defpts = $query_row_2['points'];
						
						//note that the html has c3 in many fields (c3 for content-3, points)
						$response.= '<div class = "c3def" id = "c3def'.$counter.'">';
						$response.=	 	'<div id = "c3def'.$counter.'pack">';
						$response.=			'<img id = "c3def'.$counter.'img" src = "'.$defclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3def'.$counter.'name">';
						$response.=				$defname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3def'.$counter.'pts">';
						$response.=				$defpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}      
	
	//draw the midfielders (for points)
	if(isset($_GET['GETMIDS3']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingmids = "SELECT `plid` FROM $usn WHERE `pos` = 'mid' AND `forb` = 'field'";
		$response = '';
		if($query_run = mysql_query($querygettingmids)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*5) + (ai*3) + (cs*1) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'MID') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$midname = $separatenames[sizeof($separatenames)-1];
						
						$midclub = $query_row_2['club'];
						$midpts = $query_row_2['points'];
						
						//note that the html has c3 in many fields (c3 for content-3, points)
						$response.= '<div class = "c3mid" id = "c3mid'.$counter.'">';
						$response.=	 	'<div id = "c3mid'.$counter.'pack">';
						$response.=			'<img id = "c3mid'.$counter.'img" src = "'.$midclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3mid'.$counter.'name">';
						$response.=				$midname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3mid'.$counter.'pts">';
						$response.=				$midpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   
	
	//draw the forwards (for points)
	if(isset($_GET['GETFWDS3']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingfwds = "SELECT `plid` FROM $usn WHERE `pos` = 'fwd' AND `forb` = 'field'";
		$response = '';
		if($query_run = mysql_query($querygettingfwds)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*4) + (ai*3) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'FWD') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$fwdname = $separatenames[sizeof($separatenames)-1];
						
						$fwdclub = $query_row_2['club'];
						$fwdpts = $query_row_2['points'];
						
						//note that the html has c3 in many fields (c3 for content-3, points)
						$response.= '<div class = "c3fwd" id = "c3fwd'.$counter.'">';
						$response.=	 	'<div id = "c3fwd'.$counter.'pack">';
						$response.=			'<img id = "c3fwd'.$counter.'img" src = "'.$fwdclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3fwd'.$counter.'name">';
						$response.=				$fwdname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3fwd'.$counter.'pts">';
						$response.=				$fwdpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   

	//draw the subs (for points)
	if(isset($_GET['GETSUBS3']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$counter = 1;
		$querygettinggks = "SELECT `plid` FROM $usn WHERE `pos` = 'gk' AND `forb` = 'bench'";
		$response = '';
		if($query_run = mysql_query($querygettinggks)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*6) + (ai*3) + (cs*4) - floor(gc/2) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'GK') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$gkname = $separatenames[sizeof($separatenames)-1];
						
						$gkclub = $query_row_2['club'];
						$gkpts = $query_row_2['points'];
						
						//note that the html has c3 in many fields (c3 for content-3, points)
						$response.= '<div class = "c3gk" id = "c3sub'.$counter.'">';
						$response.=	 	'<div id = "c3sub'.$counter.'pack">';
						$response.=			'<img id = "c3sub'.$counter.'img" src = "'.$gkclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3sub'.$counter.'name">';
						$response.=				$gkname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3sub'.$counter.'pts">';
						$response.=				$gkpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		
		$querygettingdefs = "SELECT `plid` FROM $usn WHERE `pos` = 'def' AND `forb` = 'bench'";
		if($query_run = mysql_query($querygettingdefs)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*6) + (ai*3) + (cs*4) - floor(gc/2) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'DEF') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$defname = $separatenames[sizeof($separatenames)-1];
						
						$defclub = $query_row_2['club'];
						$defpts = $query_row_2['points'];
						
						$response.= '<div class = "c3def" id = "c3sub'.$counter.'">';
						$response.=	 	'<div id = "c3sub'.$counter.'pack">';
						$response.=			'<img id = "c3sub'.$counter.'img" src = "'.$defclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3sub'.$counter.'name">';
						$response.=				$defname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3sub'.$counter.'pts">';
						$response.=				$defpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		
		$querygettingmids = "SELECT `plid` FROM $usn WHERE `pos` = 'mid' AND `forb` = 'bench'";
		if($query_run = mysql_query($querygettingmids)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*5) + (ai*3) + (cs*1) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'MID') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$midname = $separatenames[sizeof($separatenames)-1];
						
						$midclub = $query_row_2['club'];
						$midpts = $query_row_2['points'];
						
						$response.= '<div class = "c3mid" id = "c3sub'.$counter.'">';
						$response.=	 	'<div id = "c3sub'.$counter.'pack">';
						$response.=			'<img id = "c3sub'.$counter.'img" src = "'.$midclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3sub'.$counter.'name">';
						$response.=				$midname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3sub'.$counter.'pts">';
						$response.=				$midpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		
		$querygettingfwds = "SELECT `plid` FROM $usn WHERE `pos` = 'fwd' AND `forb` = 'bench'";
		if($query_run = mysql_query($querygettingfwds)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*4) + (ai*3) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'FWD') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$fwdname = $separatenames[sizeof($separatenames)-1];
						
						$fwdclub = $query_row_2['club'];
						$fwdpts = $query_row_2['points'];
						
						$response.= '<div class = "c3fwd" id = "c3sub'.$counter.'">';
						$response.=	 	'<div id = "c3sub'.$counter.'pack">';
						$response.=			'<img id = "c3sub'.$counter.'img" src = "'.$fwdclub.'.webp" />';
						$response.=			'<div class = "c3namepts1" id = "c3sub'.$counter.'name">';
						$response.=				$fwdname;
						$response.=			'</div>';
						$response.=			'<div class = "c3namepts2" id = "c3sub'.$counter.'pts">';
						$response.=				$fwdpts;
						$response.=			'</div>';
						$response.=			'<div id = "myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
		}
		else{
			echo 'Failed';
		}
		echo $response;
	}   
	
	//return total points of currgw (to show in total points section)
	if(isset($_GET['TOTPTS']) && isset($_GET['USN']) && $_GET['CURRGW']){
		$totpts = 0;				//total initially 0
		$gwmt = $_GET['CURRGW'];	//gameweek no
		$usn = $_GET['USN'];		//username of user
		
		//counting point of the goalkeeper 
		$querygettinggks = "SELECT `plid` FROM $usn WHERE `pos` = 'gk' AND `forb` = 'field'";
		if($query_run = mysql_query($querygettinggks)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*6) + (ai*3) + (cs*4) - floor(gc/2) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'GK') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$pts = $query_row_2['points'];
						$totpts+= $pts;		//adding this to total points
					}
				}
				else{
					echo 'faillail';
				}
			}
		}
		else{
			echo 'Failed';
		}
		
		//counting point of the defenders
		$querygettingdefs = "SELECT `plid` FROM $usn WHERE `pos` = 'def' AND `forb` = 'field'";
		if($query_run = mysql_query($querygettingdefs)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*6) + (ai*3) + (cs*4) - floor(gc/2) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'DEF') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$pts = $query_row_2['points'];
						$totpts+= $pts;		//adding this to total points
					}
				}
				else{
					echo 'faillail';
				}
			}
		}
		else{
			echo 'Failed';
		}
		
		//counting point of the midfielders
		$querygettingmids = "SELECT `plid` FROM $usn WHERE `pos` = 'mid' AND `forb` = 'field'";
		if($query_run = mysql_query($querygettingmids)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*5) + (ai*3) + (cs*1) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'MID') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$pts = $query_row_2['points'];
						$totpts+= $pts;		//adding this to total points
					}
				}
				else{
					echo 'faillail';
				}
			}
		}
		else{
			echo 'Failed';
		}
		
		//counting point of the forwards
		$querygettingfwds = "SELECT `plid` FROM $usn WHERE `pos` = 'fwd' AND `forb` = 'field'";
		if($query_run = mysql_query($querygettingfwds)){
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				$querygetnamepts = "SELECT players.name, players.club, 
				(ceil(mp/60) + (gs*4) + (ai*3) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) 
				AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'FWD') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$pts = $query_row_2['points'];
						$totpts+= $pts;		//adding this to total points
					}
				}
				else{
					echo 'faillail';
				}
			}
		}
		else{
			echo 'Failed';
		}
		echo $totpts;		//sending the total points to javascript. note that subs are not counted upto this point
	}   
		
	//draw for transfers. this time too, separate ajax calls are made by javascript. 
	//the functionalities are similar to showing for my team and points, this time just showing all players (including subs)
	//first, draw the goalkeepers	
	if(isset($_GET['GETGKS4']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettinggks = "SELECT `plid` FROM $usn WHERE `pos` = 'gk'";
		$response = '';
		if($query_run = mysql_query($querygettinggks)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				weekly_stat_players.pr AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'GK') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$gkname = $separatenames[sizeof($separatenames)-1];
						
						$gkclub = $query_row_2['club'];
						$gkpts = $query_row_2['points'];
						
						//note that the html has c4 in some fields (c4 for content-4, Transfers)
						$response.= '<div class = "c4gk" id = "c4gk'.$counter.'">';
						$response.=	 	'<div id = "c4gk'.$counter.'pack">';
						$response.=			'<img id = "c4gk'.$counter.'img" src = "'.$gkclub.'.webp" />';
						$response.=			'<div class = "c4namepts1" id = "c4gk'.$counter.'name">';
						$response.=				$gkname;
						$response.=			'</div>';
						$response.=			'<div class = "c4namepts2" id = "c4gk'.$counter.'pts">';
						$response.=				sprintf("%.1f", $gkpts);
						$response.=			'</div>';
						$response.=			'<div id = "c4myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   
	
	//draw the defenders
	if(isset($_GET['GETDEFS4']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingdefs = "SELECT `plid` FROM $usn WHERE `pos` = 'def'";
		$response = '';
		if($query_run = mysql_query($querygettingdefs)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				weekly_stat_players.pr AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'DEF') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$defname = $separatenames[sizeof($separatenames)-1];
						
						$defclub = $query_row_2['club'];
						$defpts = $query_row_2['points'];
						
						//note that the html has c4 in some fields (c4 for content-4, Transfers)
						$response.= '<div class = "c4def" id = "c4def'.$counter.'">';
						$response.=	 	'<div id = "c4def'.$counter.'pack">';
						$response.=			'<img id = "c4def'.$counter.'img" src = "'.$defclub.'.webp" />';
						$response.=			'<div class = "c4namepts1" id = "c4def'.$counter.'name">';
						$response.=				$defname;
						$response.=			'</div>';
						$response.=			'<div class = "c4namepts2" id = "c4def'.$counter.'pts">';
						$response.=				sprintf("%.1f", $defpts);
						$response.=			'</div>';
						$response.=			'<div id = "c4myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}      
	
	//draw the midfielders
	if(isset($_GET['GETMIDS4']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingmids = "SELECT `plid` FROM $usn WHERE `pos` = 'mid'";
		$response = '';
		if($query_run = mysql_query($querygettingmids)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				weekly_stat_players.pr AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'MID') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$midname = $separatenames[sizeof($separatenames)-1];
						
						$midclub = $query_row_2['club'];
						$midpts = $query_row_2['points'];
						
						//note that the html has c4 in some fields (c4 for content-4, Transfers)
						$response.= '<div class = "c4mid" id = "c4mid'.$counter.'">';
						$response.=	 	'<div id = "c4mid'.$counter.'pack">';
						$response.=			'<img id = "c4mid'.$counter.'img" src = "'.$midclub.'.webp" />';
						$response.=			'<div class = "c4namepts1" id = "c4mid'.$counter.'name">';
						$response.=				$midname;
						$response.=			'</div>';
						$response.=			'<div class = "c4namepts2" id = "c4mid'.$counter.'pts">';
						$response.=				sprintf("%.1f", $midpts);
						$response.=			'</div>';
						$response.=			'<div id = "c4myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   
	
	//draw the forwards
	if(isset($_GET['GETFWDS4']) && isset($_GET['CURRGW']) && isset($_GET['USN'])){
		$gwmt = $_GET['CURRGW'];
		$usn = $_GET['USN'];
		$querygettingfwds = "SELECT `plid` FROM $usn WHERE `pos` = 'fwd'";
		$response = '';
		if($query_run = mysql_query($querygettingfwds)){
			$counter = 1;
			while($query_row = mysql_fetch_assoc($query_run)){
				$plid = $query_row['plid'];
				
				$querygetnamepts = "SELECT players.name, players.club, 
				weekly_stat_players.pr AS points 
				FROM weekly_stat_players, players 
				WHERE (weekly_stat_players.id = players.id) AND (players.position = 'FWD') AND 
				(players.id = '$plid') AND (weekly_stat_players.gw = '$gwmt')";
				
				if($query_run_2 = mysql_query($querygetnamepts)){
					while($query_row_2 = mysql_fetch_assoc($query_run_2)){
						$fullname = $query_row_2['name'];
						$separatenames = explode(" ", $fullname);
						$fwdname = $separatenames[sizeof($separatenames)-1];
						
						$fwdclub = $query_row_2['club'];
						$fwdpts = $query_row_2['points'];
						
						//note that the html has c4 in some fields (c4 for content-4, Transfers)
						$response.= '<div class = "c4fwd" id = "c4fwd'.$counter.'">';
						$response.=	 	'<div id = "c4fwd'.$counter.'pack">';
						$response.=			'<img id = "c4fwd'.$counter.'img" src = "'.$fwdclub.'.webp" />';
						$response.=			'<div class = "c4namepts1" id = "c4fwd'.$counter.'name">';
						$response.=				$fwdname;
						$response.=			'</div>';
						$response.=			'<div class = "c4namepts2" id = "c4fwd'.$counter.'pts">';
						$response.=				sprintf("%.1f", $fwdpts);
						$response.=			'</div>';
						$response.=			'<div id = "c4myteamid'.$plid.'"></div>';
						$response.=	 	'</div>';
						$response.= '</div>';
					}
				}
				else{
					echo 'faillail';
				}
				$counter++;
			}
			echo $response;
		}
		else{
			echo 'Failed';
		}
	}   

	//send total team value to javascript. this is required to determine team value and money remaining in transfers
	if(isset($_GET['TOTALVAL']) && isset($_GET['USN']) && isset($_GET['CURRGW'])){
		$usn = $_GET['USN'];
		$currgw = $_GET['CURRGW'];
		$querytotalprice = "SELECT SUM(pr) AS total FROM $usn, weekly_stat_players 
		WHERE ($usn.plid = weekly_stat_players.id) AND (weekly_stat_players.gw = '$currgw')";
		if($query_run = mysql_query($querytotalprice)){
			while($query_row = mysql_fetch_assoc($query_run)){
				echo $query_row['total'];
			}
		}
		else{
			echo 'Failed';
		}
	}
	
	//return fixtures and results for a specific gw to javascript
	if(isset($_GET['FIXNRES'])){
		$fixnres = $_GET['FIXNRES'];
		$fixturesquery = "SELECT * FROM `fixtures_and_results`
						  WHERE gw = '$fixnres'";
		if($query_run = mysql_query($fixturesquery)){
			$response = '';
			while($query_row = mysql_fetch_assoc($query_run)){
				$home = $query_row['home'];
				$away = $query_row['away'];
				$hogo = $query_row['home_goals'];
				$awgo = $query_row['away_goals'];
				
				$response .= '<div class = "wbox wboxflexible wboxcenter fixturerow">';
				$response .= 	'<div class = "wbox wboxflexible home">';
				$response .= 		'<div class = "wbox wboxflexible wboxvertical wboxcenter" id = "homelekha">';
				$response .= 			$home;
				$response .= 		'</div>';
				$response .= 		'<img src = "Pictures/'.$home.'.png" />';
				$response .= 	'</div>';
				$response .= 	'<div class = "wbox score">';
				$response .= 		($hogo.' - '.$awgo);
				$response .= 	'</div>';
				$response .= 	'<div class = "wbox wboxflexible away">';
				$response .= 		'<img src = "Pictures/'.$away.'.png" />';
				$response .= 		'<div class = "wbox wboxflexible wboxvertical wboxcenter" id = "awaylekha">';
				$response .= 			$away;
				$response .= 		'</div>';
				$response .= 	'</div>';
				$response .= '</div>';
			}
			echo $response;
		}
		else{
			echo 'fail';
		}
	}
	
	
?>