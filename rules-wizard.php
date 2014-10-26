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
	if(!isset($_SESSION['user']))
		error($PN.'10');
	
	header('Content-Type: text/html; charset=utf-8');
  
	include 'db.php';

	$response = array();
	$rows = array();

	if(isset($_GET['chapter_id']) && isset($_GET['assignment_id']) && isset($_GET['type']))
	{
		$assignment_id = (int) $_GET['assignment_id'];
		
		$chapter_id = (int) $_GET['chapter_id'];

		$type = (int) $_GET['type'];

		$result = mysql_query("SELECT user_id FROM assignment WHERE id = ".$assignment_id.";") or error($PN.'15');
		$array = mysql_fetch_array($result);

		if($array['user_id']!=$_SESSION['id'])
			error($PN.'16');

		if($chapter_id == 0)
		{
			if($type == 0)
				$result = mysql_query("SELECT A.id, A.chapter_id, A.rule_number, A.title, A.level, A.methodology, B.PassOrFail, B.comment FROM (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title, rules.level, rules.methodology FROM rules where rules.chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$assignment_id.") order by rules.id) AS A left join (SELECT assessment_rules.rule_id, assessment_rules.PassOrFail, assessment_rules.comment FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id;") or error($PN.'11');
			else
				$result = mysql_query("SELECT A.id, A.chapter_id, A.rule_number, A.title, A.level, A.methodology, B.PassOrFail, B.comment FROM (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title, rules.level, rules.methodology FROM rules where rules.chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$assignment_id." AND status=".$type.") order by rules.id) AS A left join (SELECT assessment_rules.rule_id, assessment_rules.PassOrFail, assessment_rules.comment FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id;") or error($PN.'12');
		}
		else
			$result = mysql_query("SELECT A.id, A.chapter_id, A.rule_number, A.title, A.level, A.methodology, B.PassOrFail, B.comment FROM (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title, rules.level, rules.methodology FROM rules where chapter_id=".$chapter_id." order by rules.id) AS A left join (SELECT assessment_rules.rule_id, assessment_rules.PassOrFail, assessment_rules.comment FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id;") or error($PN.'13');

		while ($row = mysql_fetch_array($result))
		{
		  $rows[] = $row;
		}
	}
	else
		error($PN.'14');

	$response['Result'] = 'OK';
	$response['Records'] = $rows;

	echo json_encode($response);
	
	@mysql_close();
  
?>