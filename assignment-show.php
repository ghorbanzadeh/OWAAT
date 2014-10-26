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

	if(!isset($_GET['type']))
		error($PN.'11');

	$type = (int)$_GET['type'];

	if($type == 0)
		$result = mysql_query("SELECT A.id, A.assessment_id, B.assessment_name FROM (SELECT id, assessment_id FROM assignment WHERE user_id = ".$_SESSION['id'].") AS A left join (SELECT id, assessment_name FROM assessment) AS B on A.assessment_id=B.id ORDER BY B.assessment_name") or error($PN.'12');
	else
		$result = mysql_query("SELECT A.id, A.assessment_id, B.assessment_name FROM (SELECT id, assessment_id FROM assignment WHERE user_id = ".$_SESSION['id']." AND id IN (SELECT assignment_id FROM assignment_chapter WHERE status=".$type.")) AS A left join (SELECT id, assessment_name FROM assessment) AS B on A.assessment_id=B.id ORDER BY B.assessment_name") or error($PN.'13');

    while ($row = mysql_fetch_array($result))
    {
		$assessment_id = $row['assessment_id'];
		$result_count1 = mysql_query("SELECT COUNT(B.chapter_id) FROM (SELECT id, chapter_id, assignment_id FROM assignment_chapter WHERE assignment_id = (SELECT id FROM assignment WHERE assessment_id=".$assessment_id." AND user_id=".$_SESSION['id'].")) AS A LEFT JOIN (SELECT id, chapter_id FROM rules) AS B ON A.chapter_id = B.chapter_id;") or error($PN.'14');
		$array_count1 = mysql_fetch_array($result_count1);

		$result_count2 = mysql_query("SELECT COUNT(*) FROM assessment_rules WHERE assignment_id = (SELECT id FROM assignment WHERE assessment_id=".$assessment_id." AND user_id=".$_SESSION['id'].");") or error($PN.'15');
		$array_count2 = mysql_fetch_array($result_count2);

		if($array_count1[0] != 0)
		{
			$percent = round(($array_count2[0]/$array_count1[0]), 4);
			$percent *= 100;
		}
		else
			$percent = 0;

		$row['assessment_name'] .= " (".$percent."%) ";

		$rows[] = $row;
    }

	$response['Result'] = 'OK';
	$response['Records'] = $rows;

	$result_new_assignment = mysql_query("SELECT id FROM assignment WHERE user_id = ".$_SESSION['id']." AND id IN (SELECT assignment_id FROM assignment_chapter WHERE status=1)") or error($PN.'16');
	if(mysql_fetch_array($result_new_assignment))
		$response['new_assignment'] = 'Yes';
	else
		$response['new_assignment'] = 'No';

	echo json_encode($response);

	@mysql_close();

?>