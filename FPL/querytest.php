<?php
require_once 'dbasehelper.inc.php';
	
connect('fpl');

/*$query = "SELECT `id`, `name`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
(SELECT `players`.`id`, `players`.`name`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - (`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
FROM `weekly_stat_players`, `players` 
WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'DEF') 
ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_def`
GROUP BY `name` ORDER BY `total_points` DESC";

if($query_run = mysql_query($query)){
	while($query_row = mysql_fetch_assoc($query_run)){
		echo $query_row['id'].', '.$query_row['name'].': '.$query_row['club'].', '.$query_row['pr'].', '.$query_row['total_points'].'<br>';
	}
}
else{
	echo 'thik kor!';
}


$querygk = "SELECT `id`, `name`, `position`, `club`, `pr`, SUM(`points`) AS `total_points` FROM
		(SELECT `players`.`id`, `players`.`name`, `players`.`position`, `players`.`club`, `weekly_stat_players`.`pr`, `weekly_stat_players`.`gw`, 
		(ceil(`mp`/59) + (`gs`*6) + (`ai`*3) + (`cs`*4) - floor(`gc`/2) - 
		(`og`*2) + (`ps`*5) - (`pm`*2) - (`yc`*1) - (`rc`*3) + `bo` ) AS `points` 
		FROM `weekly_stat_players`, `players` 
		WHERE (`weekly_stat_players`.`id` = `players`.`id`) AND (`players`.`position` = 'GK') 
		ORDER BY `weekly_stat_players`.`id`, `weekly_stat_players`.`gw`) AS `table_gk`
		GROUP BY `name` ORDER BY `total_points` DESC";
if($query_run = mysql_query($querygk)){
	$response = '';
	$response .= '<div id = "viewedgks"><table id = "gkstable">';
	$returnedrows = mysql_num_rows($query_run);
	if($returnedrows!=0){
		$response .= '<th></th><th id = "gkslekha">Goalkeepers<th></th></th><th>$</th><th>Pts</th>';
	}
	while($query_row = mysql_fetch_assoc($query_run)){
		$id = $query_row['id'];
		$pts = $query_row['total_points'];
		$pr = $query_row['pr'];
		$fullname = $query_row['name'];
		$separatenames = explode(" ", $fullname);
		$lastname = $separatenames[sizeof($separatenames)-1];
		
		$response .= '<tr class = "viewedp" id = "id'.$query_row['id'].'">';
		$response .= 	'<td><img src = "'.$query_row['club'].'.webp" /></td>';
		$response .=	'<td><span>'.$lastname.'</span><br/><span>'.$query_row['club'].'</span></td>';
		$response .=	'<td class = "'.$query_row['position'].'">'.'</div>';
		$response .=	'<td>'.sprintf("%.1f", $pr).'</td>';
		$response .=	'<td>'.$pts.'</td>';
		$response .= '</tr>';
	}
	$response .= '</table></div>';
	echo $response;
}
else{
	echo 'Failed';
}*/
/*$tablename = 'ss 9';
$gk1 = 'Cech';
$gk2 = 'Courtois';
$gw = 1;

$createtablequery = "CREATE TABLE $tablename (
						gw int,
						plid varchar(255),
						pos varchar(255),
						forb varchar(255) 
					)";
if($query_run = mysql_query($createtablequery)){
	$insertquery = "INSERT INTO $tablename(`gw`, `plid`, `pos`, `forb`) VALUES
					('$gw', '$gk1', 'gk', 'field'),
					('$gw', '$gk2', 'gk', 'bench')";
	if($query_run = mysql_query($insertquery)){
		echo 'supersuccess';
	}
	else{
		echo 'superfailure';
	}
}
else{
	echo 'Failed';
}*/
/*$tablename = 'players';
$querytableexists = "SHOW TABLES LIKE '$tablename'";
if($query_run = mysql_query($querytableexists)){
	echo mysql_num_rows($query_run);
}
else{
	echo 'failed';
}*/
/*$querygettinggks = "SELECT `plid` FROM `shanta` WHERE `pos` = 'def' AND `forb` = 'field'";

$response = '';

if($query_run = mysql_query($querygettinggks)){
	$counter = 1;
	while($query_row = mysql_fetch_assoc($query_run)){
		$plid = $query_row['plid'];
		
		$querygetnamepts = "SELECT players.name, players.club, 
		(ceil(mp/60) + (gs*6) + (ai*3) + (cs*4) - floor(gc/2) - (og*2) + (ps*5) - (pm*2) - (yc*1) - (rc*3) + bo ) AS points 
		FROM weekly_stat_players, players 
		WHERE (weekly_stat_players.id = players.id) AND (players.position = 'DEF') AND 
		(players.id = '$plid') AND (weekly_stat_players.gw = '1')";
		
		if($query_run_2 = mysql_query($querygetnamepts)){
			while($query_row_2 = mysql_fetch_assoc($query_run_2)){
				$gkname = $query_row_2['name'];
				$gkclub = $query_row_2['club'];
				$gkpts = $query_row_2['points'];
				
				//echo $gkname.'<br>'.$gkclub.'<br>'.$gkpts.'<br>';
				$response.= '<div id = "gk'.$counter.'">'
				$response.=	 	'<div id = "gk'.$counter.'pack">'
				$response.=			'<img id = "gk'.$counter.'img" src = "'.$gkclub.'.webp" />'
				$response.=			'<div class = "namepts1" id = "gk'.$counter.'name">'
				$response.=				$gkname
				$response.=			'</div>'
				$response.=			'<div class = "namepts2" id = "gk'.$counter.'pts">'
				$response.=				$gkpts
				$response.=			'</div>'
				$response.=	 	'</div>'
				$response.= '</div>'
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
}*/


?>