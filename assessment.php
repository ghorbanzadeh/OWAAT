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

	if($_GET["action"] != "list")
	{
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'12');
	}

	if(($_GET["action"] == "create") || ($_GET["action"] == "update"))
	{
		if(isset($_POST["assessment_name"]) && isset($_POST["description"]))
		{
			$assessment_name_tmp = strip_tags($_POST['assessment_name']);
			$assessment_name = mysql_real_escape_string($assessment_name_tmp);
			$assessment_name = trim($assessment_name);

			$description_tmp = strip_tags($_POST['description']);
			$description = mysql_real_escape_string($description_tmp);
			$description = trim($description);

			if(empty($assessment_name))
				error($PN.'13');
		}
		else
			error($PN.'14');	
	}

	if(($_GET["action"] == "update") || ($_GET["action"] == "delete"))
	{
		if(isset($_POST['id']))
		{
			$id = (int)$_POST['id'];
		}
		else
			error($PN.'15');
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
					error($PN.'16');

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
						error($PN.'17');
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
			error($PN.'18');

		if(isset($_GET["select"]))
			$select = (int)$_GET['select'];
		else
			$select = 0;

		if($select == 0)
		{
			$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM assessment;") or error($PN.'19');
			$row = mysql_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysql_query("SELECT A.id, B.uname, A.assessment_name, A.description, A.complete, A.create_time, A.complete_time FROM (SELECT * FROM assessment) AS A left join (SELECT id, uname FROM users) AS B on A.user_id=B.id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'20');
		}
		else
		{
			$recordCount = 1;
			$result = mysql_query("SELECT A.id, B.uname, A.assessment_name, A.description, A.complete, A.create_time, A.complete_time FROM (SELECT * FROM assessment WHERE id=".$select.") AS A left join (SELECT id, uname FROM users) AS B on A.user_id=B.id;") or error($PN.'21');
		}
		
		$rows = array();
		while($row = mysql_fetch_array($result))
		{
			$result_count1 = mysql_query("SELECT COUNT(B.chapter_id) FROM (SELECT id, chapter_id, assignment_id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$row['id'].")) AS A LEFT JOIN (SELECT id, chapter_id FROM rules) AS B ON A.chapter_id = B.chapter_id;") or error($PN.'32');
			$array_count1 = mysql_fetch_array($result_count1);

			$result_count2 = mysql_query("SELECT COUNT(*) FROM assessment_rules WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$row['id'].");") or error($PN.'33');
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
	else if($_GET["action"] == "create")
	{
	
		$result1 = mysql_query("SELECT assessment_name FROM assessment WHERE assessment_name = '".$assessment_name."';") or error($PN.'22');
		$row1 = mysql_fetch_array($result1);
		
		if($row1)
			error($PN.'23');
	
		mysql_query("INSERT INTO assessment(user_id, assessment_name, description, create_time) VALUES(".$_SESSION['id'].",'".$assessment_name."','".$description."', NOW());") or error($PN.'24');

		$result = mysql_query("SELECT id, user_id, assessment_name, description, create_time FROM assessment WHERE id = LAST_INSERT_ID();") or error($PN.'25');
		$row = mysql_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "update")
	{

		mysql_query("UPDATE assessment SET assessment_name='".$assessment_name."', description='".$description."' WHERE id = ".$id.";") or error($PN.'26');

		$result = mysql_query("SELECT id, user_id, assessment_name, description, create_time FROM assessment WHERE id = ".$id.";") or error($PN.'27');
		$row = mysql_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);

	}
	else if($_GET["action"] == "delete")
	{		
	
		$result = mysql_query("SELECT COUNT(*) FROM assignment WHERE assessment_id = ".$id.";") or error($PN.'28');
		$row = mysql_fetch_array($result);
		if($row[0] == 0)
		{
			mysql_query("DELETE FROM assessment WHERE id = ".$id. ";") or error($PN.'29');
		}
		else
			error($PN.'30');

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'31');

	@mysql_close();
?>