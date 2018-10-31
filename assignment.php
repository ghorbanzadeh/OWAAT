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
		error($PN.'10', $con);

	header('Content-Type: text/html; charset=utf-8');

	include 'db.php';

	if(!isset($_GET["action"]))
		error($PN.'11', $con);

	if($_GET["action"] == "list")
	{
		if(isset($_GET["assessment_id"]))
		{
			$assessment_id = (int)$_GET['assessment_id'];
		}
		else
			error($PN.'12', $con);

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
					error($PN.'13', $con);

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
						error($PN.'14', $con);
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
			error($PN.'15', $con);

		$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM (SELECT D.chapter_id FROM (SELECT A.id FROM (SELECT id, user_id FROM assignment where assessment_id=".$assessment_id.") AS A left join (SELECT id FROM users) AS B on A.user_id=B.id) AS C left join (SELECT chapter_id, assignment_id from assignment_chapter) AS D on C.id=D.assignment_id) AS E left join (SELECT id from chapters) AS F on E.chapter_id=F.id;") or error($PN.'16', $con);
		$row = mysqli_fetch_array($result);
		$recordCount = $row['RecordCount'];

		$result = mysqli_query($con, "SELECT E.id, E.user_id, E.uname, E.chapter_id, F.chapter_name, IF(E.status = 3, 1, 0) AS complete, E.assignment_time FROM (SELECT D.id, C.user_id, C.uname, D.chapter_id, D.status, D.assignment_time FROM (SELECT A.id, B.id AS user_id, B.uname FROM (SELECT id, user_id FROM assignment where assessment_id=".$assessment_id.") AS A left join (SELECT id, uname FROM users) AS B on A.user_id=B.id) AS C left join (SELECT id, chapter_id, assignment_id, status, assignment_time from assignment_chapter) AS D on C.id=D.assignment_id) AS E left join (SELECT id, chapter_name from chapters) AS F on E.chapter_id=F.id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'17', $con);

		$rows = array();
		while($row = mysqli_fetch_array($result))
		{
			$result_count1 = mysqli_query($con, "SELECT COUNT(*) FROM rules WHERE chapter_id = (SELECT chapter_id FROM assignment_chapter WHERE id=".$row['id'].");") or error($PN.'26', $con);
			$result_count2 = mysqli_query($con, "SELECT COUNT(*) FROM assessment_rules WHERE assignment_id = (SELECT assignment_id FROM assignment_chapter WHERE id=".$row['id'].") AND rule_id IN (SELECT id FROM rules WHERE chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE id=".$row['id']."));") or error($PN.'27', $con);

			$array_count1 = mysqli_fetch_array($result_count1);
			$array_count2 = mysqli_fetch_array($result_count2);

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
			error($PN.'18', $con);

		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'19', $con);

		$result = mysqli_query($con, "SELECT COUNT(*), assignment_id FROM assignment_chapter WHERE assignment_id = (SELECT assignment_id FROM assignment_chapter WHERE id=".$id.");") or error($PN.'20', $con);
		$row = mysqli_fetch_array($result);
		
		$result_chapter = mysqli_query($con, "SELECT chapter_id FROM assignment_chapter WHERE id = ".$id.";") or error($PN.'21', $con);
		$row_chapter = mysqli_fetch_array($result_chapter);

		if($row_chapter)
		{
			$result = mysqli_query($con, "SELECT id FROM report_rules WHERE assignment_id = ".$row[1]. " AND rule_id IN (SELECT id FROM rules WHERE chapter_id = ".$row_chapter['chapter_id'].");") or error($PN.'28', $con);
			if(mysqli_fetch_array($result))
				error($PN.'29', $con);

			mysqli_query($con, "DELETE FROM assessment_rules WHERE assignment_id = ".$row[1]. " AND rule_id IN (SELECT id FROM rules WHERE chapter_id = ".$row_chapter['chapter_id'].");") or error($PN.'23', $con);

			mysqli_query($con, "DELETE FROM assignment_chapter WHERE id = ".$id.";") or error($PN.'22', $con);
			
			if($row[0] == 1)
				mysqli_query($con, "DELETE FROM assignment WHERE id = ".$row[1]. ";") or error($PN.'24', $con);
		}
		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'25', $con);

	@mysqli_close($con) ;

?>