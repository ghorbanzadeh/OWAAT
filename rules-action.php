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
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'12', $con);

	if(($_GET["action"] == "create") || ($_GET["action"] == "update"))
	{
		if(isset($_POST["chapter_id"]) && isset($_POST["rule_number"]) && isset($_POST["title"]) && isset($_POST["level"]))
		{
			$chapter_id = (int)$_POST['chapter_id'];

			$rule_number = (int)$_POST['rule_number'];

			$title_tmp = strip_tags($_POST['title']);
			$title = mysqli_real_escape_string($con, $title_tmp);
			$title = trim($title);

			$level = (int)$_POST['level'];
			
			if(!($level == 1 || $level == 2 || $level == 3))
				error($PN.'59', $con);

			if(empty($chapter_id) || empty($rule_number) || empty($title) || empty($level))
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
					case "chapter_id":
						$sort .= 'chapter_id';
						break;
					case "rule_number":
						$sort .= 'rule_number';
						break;
					case "level":
						$sort .= 'level';
						break;
					case "title":
						$sort .= 'title';
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
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM rules;") or error($PN.'19', $con);
			$row = mysqli_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysqli_query($con, "SELECT id, chapter_id, rule_number, title, level FROM rules ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'20', $con);

			$rows = array();
			while($row = mysqli_fetch_array($result))
			{
				$rows[] = $row;
			}
		}
		else
		{
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM rules where chapter_id=".$select.";") or error($PN.'21', $con);
			$row = mysqli_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysqli_query($con, "SELECT id, chapter_id, rule_number, title, level FROM rules where chapter_id=".$select." ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'22', $con);

			$rows = array();
			while($row = mysqli_fetch_array($result))
			{
				$rows[] = $row;
			}		
		}

		$response = array();
		$response['Result'] = "OK";
		$response['TotalRecordCount'] = $recordCount;
		$response['Records'] = $rows;
		print json_encode($response);
	}
	else if($_GET["action"] == "create")
	{
		$result1 = mysqli_query($con, "SELECT * FROM chapters WHERE id = ".$chapter_id.";") or error($PN.'37', $con);
		$row1 = mysqli_fetch_array($result1);
		
		if(!$row1)
		  error($PN.'38', $con);

		$result2 = mysqli_query($con, "SELECT id FROM rules WHERE chapter_id = ".$chapter_id." and rule_number = ".$rule_number.";") or error($PN.'23', $con);
		$row2 = mysqli_fetch_array($result2);
		
		if($row2)
		  error($PN.'24', $con);
		
		mysqli_query($con, "INSERT INTO rules(chapter_id, rule_number, title, level) VALUES(".$chapter_id.",".$rule_number.",'".$title."',".$level.");") or error($PN.'25', $con);

		uncompleted($chapter_id);

		$result = mysqli_query($con, "SELECT id, chapter_id, rule_number, title, level FROM rules WHERE id = LAST_INSERT_ID();") or error($PN.'26', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "update")
	{
	
		$result_chapter = mysqli_query($con, "SELECT * FROM chapters WHERE id = ".$chapter_id.";") or error($PN.'44', $con);
		$row_chapter = mysqli_fetch_array($result_chapter);
		
		if(!$row_chapter)
		  error($PN.'45', $con);

		$result1 = mysqli_query($con, "SELECT id FROM rules WHERE chapter_id = ".$chapter_id." AND rule_number = ".$rule_number." AND id != ".$id.";") or error($PN.'27', $con);
		$row1 = mysqli_fetch_array($result1);
		
		if($row1)
		  error($PN.'28', $con);

		$result = mysqli_query($con, "SELECT chapter_id FROM rules WHERE id = ".$id.";") or error($PN.'39', $con);
		$row = mysqli_fetch_array($result);
		
		$chapter_id_changed = false;
		if($row['chapter_id'] != $chapter_id)
		{
			$result = mysqli_query($con, "SELECT rule_id FROM assessment_rules WHERE rule_id = ".$id.";") or error($PN.'40', $con);
			if(mysqli_fetch_array($result))
				error($PN.'41', $con);
			else
			{
				$result = mysqli_query($con, "SELECT rule_id FROM report_rules WHERE rule_id = ".$id.";") or error($PN.'42', $con);
				if(mysqli_fetch_array($result))
					error($PN.'43', $con);
			}
			
			$chapter_id_changed = true;
		}

		mysqli_query($con, "UPDATE rules SET chapter_id=".$chapter_id.", rule_number=".$rule_number.", title='".$title."', level=".$level." WHERE id = ".$id.";") or error($PN.'29', $con);

		if(($chapter_id_changed == true) && (mysqli_affected_rows($con)  == 1))
		{
			uncompleted($chapter_id);
			iscompleted($row['chapter_id']);
		}

		$result = mysqli_query($con, "SELECT id, chapter_id, rule_number, title, level FROM rules WHERE id = ".$id.";") or error($PN.'30', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "delete")
	{

		$result = mysqli_query($con, "SELECT rule_id FROM assessment_rules WHERE rule_id = ".$id.";") or error($PN.'31', $con);
		if(mysqli_fetch_array($result))
			error($PN.'32', $con);
		else
		{
			$result = mysqli_query($con, "SELECT rule_id FROM report_rules WHERE rule_id = ".$id.";") or error($PN.'33', $con);
			if(mysqli_fetch_array($result))
				error($PN.'34', $con);
		}

		$result = mysqli_query($con, "SELECT chapter_id FROM rules WHERE id = ".$id.";") or error($PN.'49', $con);
		$row = mysqli_fetch_array($result);
		if($row['chapter_id'] == '')
			error($PN.'60', $con);

		mysqli_query($con, "DELETE FROM rules WHERE id = ".$id. ";") or error($PN.'35', $con);

		iscompleted($row['chapter_id']);

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'36', $con);

	function uncompleted($chapter_id)
	{
		global $PN;
		//Change Completed to Uncompleted in assignment_chapter Table
		mysqli_query($con, "UPDATE assignment_chapter SET status=2 WHERE chapter_id=".$chapter_id.";") or error($PN.'46', $con);

		//Change Completed to Uncompleted in assignment Table
		mysqli_query($con, "UPDATE assignment SET status=2 WHERE id IN (SELECT assignment_id FROM assignment_chapter WHERE chapter_id=".$chapter_id.") AND status=3;") or error($PN.'47', $con);

		//Set Complete=0 in assessment Table
		mysqli_query($con, "UPDATE assessment SET complete=0, complete_time='0' WHERE id IN (SELECT assessment_id FROM assignment WHERE id IN (SELECT assignment_id FROM assignment_chapter WHERE chapter_id=".$chapter_id.")) AND complete=1;") or error($PN.'48', $con);
	}
	
	function iscompleted($chapter_id)
	{
		global $PN;
		$rows = array();
		$result = mysqli_query($con, "SELECT assignment_id FROM assignment_chapter WHERE chapter_id=".$chapter_id.";") or error($PN.'50', $con);
		while($row = mysqli_fetch_array($result))
		{
			$assignment_id = $row['assignment_id'];	
		
			$result_count1 = mysqli_query($con, "SELECT COUNT(*) FROM rules WHERE chapter_id =".$chapter_id.";") or error($PN.'51', $con);
			$array_count1 = mysqli_fetch_array($result_count1);

			$result_count2 = mysqli_query($con, "SELECT COUNT(*) FROM assessment_rules WHERE assignment_id=".$assignment_id." AND rule_id IN (SELECT id FROM rules WHERE chapter_id =".$chapter_id.");") or error($PN.'52', $con);
			$array_count2 = mysqli_fetch_array($result_count2);
			
			//Set status=3 in assignment_chapter Table
			if($array_count1[0] == $array_count2[0])
			{
				mysqli_query($con, "UPDATE assignment_chapter SET status=3 WHERE assignment_id=".$assignment_id." AND chapter_id=".$chapter_id.";") or error($PN.'53', $con);
			}

			//Change Uncompleted to Completed in assignment Table
			$result3 = mysqli_query($con, "SELECT COUNT(*) FROM assignment_chapter WHERE assignment_id=".$assignment_id." and status!=3;") or error($PN.'54', $con);
			$array3 = mysqli_fetch_array($result3);
			if($array3[0] == 0)
			{
				mysqli_query($con, "UPDATE assignment SET status=3 WHERE id=".$assignment_id." AND status=2;") or error($PN.'55', $con);
				$row['completed'] = 'Yes';
			}

			//Set Complete=1 in assessment Table
			$result4 = mysqli_query($con, "SELECT assessment_id FROM assignment WHERE id=".$assignment_id.";") or error($PN.'56', $con);
			$array4 = mysqli_fetch_array($result4);

			$result5 = mysqli_query($con, "SELECT COUNT(*) FROM assignment WHERE assessment_id=".$array4[0]." AND status!=3;") or error($PN.'57', $con);
			$array5 = mysqli_fetch_array($result5);
			if($array5[0] == 0)
				mysqli_query($con, "UPDATE assessment SET complete=1, complete_time= NOW() WHERE id=".$array4[0].";") or error($PN.'58', $con);
		}
	}

	@mysqli_close($con) ;
?>