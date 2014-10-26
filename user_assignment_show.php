<?php 
/*
	OWASP ASVS Assessment Tool (OWAAT)

	Copyright (C) 2014 Mahmoud Ghorbanzadeh <mdgh (a) aut.ac.ir>
  
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

	include "settings.php";
	include "function.php";
	if(!isset($_SESSION['admin']))
		error($PN.'10');

	header('Content-Type: text/html; charset=utf-8');
	
	include 'db.php';

	$response = array();
	$rows = array();

	if(isset($_GET['action']) &&  $_GET['action'] == 'assessment')
	{
		if(!isset($_GET['user_id']))
			error($PN.'11');
	
		$user_id = (int) $_GET['user_id'];
	
		$result_user = mysql_query("SELECT id FROM users WHERE id = ".$user_id.";") or error($PN.'12');
		if(!mysql_fetch_row($result_user))
			error($PN.'13');
		
		$result = mysql_query("SELECT A.id AS assignment_id, B.assessment_name FROM (SELECT id, user_id, assessment_id FROM assignment WHERE user_id = ".$user_id." AND status != 3) AS A LEFT JOIN (SELECT id, assessment_name FROM assessment WHERE complete = 0) AS B ON A.assessment_id = B.id ORDER BY B.assessment_name;") or error($PN.'14');
	}
	else if(isset($_GET['action']) &&  $_GET['action'] == 'chapter')
	{
		$assignment_id = (int) $_GET['assignment_id'];
		$result_assignment = mysql_query("SELECT id FROM assignment WHERE id = ".$assignment_id.";") or error($PN.'15');
		if(!mysql_fetch_row($result_assignment))
			error($PN.'16');
		
		$result = mysql_query("SELECT A.chapter_id, B.chapter_name FROM (SELECT chapter_id FROM assignment_chapter WHERE assignment_id = ".$assignment_id." AND status != 3) AS A LEFT JOIN (SELECT id, chapter_name FROM chapters) AS B ON A.chapter_id = B.id ORDER BY A.chapter_id;") or error($PN.'17');
	}
	else
		error($PN.'18');

	while ($row = mysql_fetch_array($result))
	{
		if($_GET['action'] == 'chapter')
		{
			$result_count1 = mysql_query("SELECT COUNT(*) FROM rules where chapter_id =".$row['chapter_id'].";") or error($PN.'19');
			$array_count1 = mysql_fetch_array($result_count1);

			$result_count2 = mysql_query("SELECT COUNT(*) FROM assessment_rules where assignment_id=".$assignment_id." and rule_id IN (SELECT id FROM rules WHERE chapter_id =".$row['chapter_id'].");") or error($PN.'20');
			$array_count2 = mysql_fetch_array($result_count2);

			if($array_count1[0] != 0)
			{
				$percent = round(($array_count2[0]/$array_count1[0]), 4);
				$percent *= 100;
			}
			else
				$percent = 0;

			$row['chapter_name'] .= " (".$percent."%) ";
		}
		$rows[] = $row;
	}

	$response['Result'] = 'OK';
	$response['Records'] = $rows;

	echo json_encode($response);

	@mysql_close();

?>