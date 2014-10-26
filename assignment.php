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

	include 'function.php';
	if(!isset($_SESSION['admin']))
		error($PN.'10');

	header('Content-Type: text/html; charset=utf-8');

	include 'db.php';

	if(!isset($_GET["action"]))
		error($PN.'11');

	if($_GET["action"] == "list")
	{
		if(isset($_GET["assessment_id"]))
		{
			$assessment_id = (int)$_GET['assessment_id'];
		}
		else
			error($PN.'12');	

		if(isset($_GET["jtSorting"]) && isset($_GET["jtStartIndex"]) && isset($_GET["jtPageSize"]))
		{
		
			$Sorting_array_multiple = explode(",", $_GET['jtSorting']);
			$sort = '';

			foreach($Sorting_array_multiple as $Sorting)
			{
				$Sorting = trim($Sorting);
				$Sorting_array = explode(" ", $Sorting);
				
				if($sort != '')
					$sort .= ', ';

				if(!isset($Sorting_array[0]) || !isset($Sorting_array[1]))
					error($PN.'13');

				switch ($Sorting_array[0]) {
					case "uname":
						$sort .= 'E.uname';
						break;
					case "chapter_id":
						$sort .= 'E.chapter_id';
						break;
					case "chapter_name":
						$sort .= 'F.chapter_name';
						break;
					/*case "complete":
						$sort .= 'E.complete';
						break;*/
					case "assignment_time":
						$sort .= 'E.assignment_time';
						break;
					default:
						error($PN.'14');
				}
					
				if($Sorting_array[1] == 'ASC')
					$sort .= ' ASC';
				else
					$sort .= ' DESC';
			}

			$Sorting = $sort;
		
			$StartIndex = (int)$_GET['jtStartIndex'];
		
			$PageSize = (int)$_GET['jtPageSize'];
		}
		else
			error($PN.'15');

		$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM (SELECT D.chapter_id FROM (SELECT A.id FROM (SELECT id, user_id FROM assignment where assessment_id=".$assessment_id.") AS A left join (SELECT id FROM users) AS B on A.user_id=B.id) AS C left join (SELECT chapter_id, assignment_id from assignment_chapter) AS D on C.id=D.assignment_id) AS E left join (SELECT id from chapters) AS F on E.chapter_id=F.id;") or error($PN.'16');
		$row = mysql_fetch_array($result);
		$recordCount = $row['RecordCount'];

		$result = mysql_query("SELECT E.id, E.user_id, E.uname, E.chapter_id, F.chapter_name, IF(E.status = 3, 1, 0) AS complete, E.assignment_time FROM (SELECT D.id, C.user_id, C.uname, D.chapter_id, D.status, D.assignment_time FROM (SELECT A.id, B.id AS user_id, B.uname FROM (SELECT id, user_id FROM assignment where assessment_id=".$assessment_id.") AS A left join (SELECT id, uname FROM users) AS B on A.user_id=B.id) AS C left join (SELECT id, chapter_id, assignment_id, status, assignment_time from assignment_chapter) AS D on C.id=D.assignment_id) AS E left join (SELECT id, chapter_name from chapters) AS F on E.chapter_id=F.id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'17');

		$rows = array();
		while($row = mysql_fetch_array($result))
		{
			$result_count1 = mysql_query("SELECT COUNT(*) FROM rules WHERE chapter_id = (SELECT chapter_id FROM assignment_chapter WHERE id=".$row['id'].");") or error($PN.'26');
			$result_count2 = mysql_query("SELECT COUNT(*) FROM assessment_rules WHERE assignment_id = (SELECT assignment_id FROM assignment_chapter WHERE id=".$row['id'].") AND rule_id IN (SELECT id FROM rules WHERE chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE id=".$row['id']."));") or error($PN.'27');

			$array_count1 = mysql_fetch_array($result_count1);		
			$array_count2 = mysql_fetch_array($result_count2);

			if($array_count1[0] != 0)
			{
				$percent = round(($array_count2[0]/$array_count1[0]), 4);
				$percent *= 100;
			}
			else
				$percent = 0;

			if($percent == 0)
				$row['completed'] = 'No';
			else if($percent == 100)
				$row['completed'] = 'Yes';
			else
				$row['completed'] = $percent."%";

			$rows[] = $row;
		}

		$response = array();
		$response['Result'] = "OK";
		$response['TotalRecordCount'] = $recordCount;
		$response['Records'] = $rows;
		print json_encode($response);
	}
	else if($_GET["action"] == "delete")
	{
		if(isset($_POST['id']))
		{
			$id = (int)$_POST['id'];
		}
		else
			error($PN.'18');

		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'19');

		$result = mysql_query("SELECT COUNT(*), assignment_id FROM assignment_chapter WHERE assignment_id = (SELECT assignment_id FROM assignment_chapter WHERE id=".$id.");") or error($PN.'20');
		$row = mysql_fetch_array($result);
		
		$result_chapter = mysql_query("SELECT chapter_id FROM assignment_chapter WHERE id = ".$id.";") or error($PN.'21');
		$row_chapter = mysql_fetch_array($result_chapter);

		if($row_chapter)
		{
			$result = mysql_query("SELECT id FROM report_rules WHERE assignment_id = ".$row[1]. " AND rule_id IN (SELECT id FROM rules WHERE chapter_id = ".$row_chapter['chapter_id'].");") or error($PN.'28');
			if(mysql_fetch_array($result))
				error($PN.'29');

			mysql_query("DELETE FROM assessment_rules WHERE assignment_id = ".$row[1]. " AND rule_id IN (SELECT id FROM rules WHERE chapter_id = ".$row_chapter['chapter_id'].");") or error($PN.'23');

			mysql_query("DELETE FROM assignment_chapter WHERE id = ".$id.";") or error($PN.'22');
			
			if($row[0] == 1)
				mysql_query("DELETE FROM assignment WHERE id = ".$row[1]. ";") or error($PN.'24');
		}
		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'25');

	@mysql_close();

?>