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

	if($_GET["action"] != "list")
	{
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'12', $con);
	}

	if(($_GET["action"] == "create") || ($_GET["action"] == "update"))
	{
		if(isset($_POST["assessment_name"]) && isset($_POST["description"]))
		{
			$assessment_name_tmp = strip_tags($_POST['assessment_name']);
			$assessment_name = mysqli_real_escape_string($con, $assessment_name_tmp);
			$assessment_name = trim($assessment_name);

			$description_tmp = strip_tags($_POST['description']);
			$description = mysqli_real_escape_string($con, $description_tmp);
			$description = trim($description);

			if(empty($assessment_name))
				error($PN.'13', $con);
		}
		else
			error($PN.'14', $con);
	}

	if(($_GET["action"] == "update") || ($_GET["action"] == "delete"))
	{
		if(isset($_POST['id']))
		{
			$id = (int)$_POST['id'];
		}
		else
			error($PN.'15', $con);
	}

	if($_GET["action"] == "list")
	{

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
					error($PN.'16', $con);

				switch ($Sorting_array[0]) {
					case "assessment_name":
						$sort .= 'A.assessment_name';
						break;
					case "description":
						$sort .= 'A.description';
						break;
					case "uname":
						$sort .= 'B.uname';
						break;
					case "create_time":
						$sort .= 'A.create_time';
						break;
					case "complete":
						$sort .= 'A.complete';
						break;
					case "complete_time":
						$sort .= 'A.complete_time';
						break;
					default:
						error($PN.'17', $con);
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
			error($PN.'18', $con);

		if(isset($_GET["select"]))
			$select = (int)$_GET['select'];
		else
			$select = 0;

		if($select == 0)
		{
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM assessment;") or error($PN.'19', $con);
			$row = mysqli_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysqli_query($con, "SELECT A.id, B.uname, A.assessment_name, A.description, A.complete, A.create_time, A.complete_time FROM (SELECT * FROM assessment) AS A left join (SELECT id, uname FROM users) AS B on A.user_id=B.id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'20', $con);
		}
		else
		{
			$recordCount = 1;
			$result = mysqli_query($con, "SELECT A.id, B.uname, A.assessment_name, A.description, A.complete, A.create_time, A.complete_time FROM (SELECT * FROM assessment WHERE id=".$select.") AS A left join (SELECT id, uname FROM users) AS B on A.user_id=B.id;") or error($PN.'21', $con);
		}
		
		$rows = array();
		while($row = mysqli_fetch_array($result))
		{
			$result_count1 = mysqli_query($con, "SELECT COUNT(B.chapter_id) FROM (SELECT id, chapter_id, assignment_id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$row['id'].")) AS A LEFT JOIN (SELECT id, chapter_id FROM rules) AS B ON A.chapter_id = B.chapter_id;") or error($PN.'32', $con);
			$array_count1 = mysqli_fetch_array($result_count1);

			$result_count2 = mysqli_query($con, "SELECT COUNT(*) FROM assessment_rules WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$row['id'].");") or error($PN.'33', $con);
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
	else if($_GET["action"] == "create")
	{
	
		$result1 = mysqli_query($con, "SELECT assessment_name FROM assessment WHERE assessment_name = '".$assessment_name."';") or error($PN.'22', $con);
		$row1 = mysqli_fetch_array($result1);
		
		if($row1)
			error($PN.'23', $con);
	
		mysqli_query($con, "INSERT INTO assessment(user_id, assessment_name, description, create_time) VALUES(".$_SESSION['id'].",'".$assessment_name."','".$description."', NOW());") or error($PN.'24', $con);

		$result = mysqli_query($con, "SELECT id, user_id, assessment_name, description, create_time FROM assessment WHERE id = LAST_INSERT_ID();") or error($PN.'25', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "update")
	{

		mysqli_query($con, "UPDATE assessment SET assessment_name='".$assessment_name."', description='".$description."' WHERE id = ".$id.";") or error($PN.'26', $con);

		$result = mysqli_query($con, "SELECT id, user_id, assessment_name, description, create_time FROM assessment WHERE id = ".$id.";") or error($PN.'27', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);

	}
	else if($_GET["action"] == "delete")
	{		
	
		$result = mysqli_query($con, "SELECT COUNT(*) FROM assignment WHERE assessment_id = ".$id.";") or error($PN.'28', $con);
		$row = mysqli_fetch_array($result);
		if($row[0] == 0)
		{
			mysqli_query($con, "DELETE FROM assessment WHERE id = ".$id. ";") or error($PN.'29', $con);
		}
		else
			error($PN.'30', $con);

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'31', $con);

	@mysqli_close($con) ;
?>