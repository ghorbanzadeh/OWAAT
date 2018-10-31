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
	if(!isset($_SESSION['user']))
		error($PN.'10', $con);

	header('Content-Type: text/html; charset=utf-8');

	include 'db.php';

	if(isset($_GET["chapter_id"]) && isset($_GET["assignment_id"]) && isset($_GET['type']))
	{
		$chapter_id = (int)$_GET['chapter_id'];
		$assignment_id = (int)$_GET['assignment_id'];	
		$type = (int) $_GET['type'];		
	}
	else
		error($PN.'11', $con);

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
				error($PN.'12', $con);

			switch ($Sorting_array[0]) {
				case "chapter_id":
					$sort .= 'A.chapter_id';
					break;
				case "rule_number":
					$sort .= 'A.rule_number';
					break;
				case "title":
					$sort .= 'A.title';
					break;
				case "methodology":
					$sort .= 'A.methodology';
					break;
				case "level":
					$sort .= 'A.level';
					break;
				case "PassOrFail":
					$sort .= 'B.PassOrFail';
					break;
				case "comment":
					$sort .= 'B.comment';
					break;
				default:
					error($PN.'13', $con);
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
		error($PN.'14', $con);

	if($chapter_id == 0)
	{
		if($type == 0)
		{
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM (SELECT rules.id FROM rules where rules.chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$assignment_id.") order by rules.id) AS A left join (SELECT assessment_rules.rule_id FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id;") or error($PN.'15', $con);
			$result2 = mysqli_query($con, "SELECT A.id, A.chapter_id, A.rule_number, A.title, A.level, A.methodology, B.PassOrFail, B.comment FROM (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title, rules.level, rules.methodology FROM rules where rules.chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$assignment_id.") order by rules.id) AS A left join (SELECT assessment_rules.rule_id, assessment_rules.PassOrFail, assessment_rules.comment FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'16', $con);
		}
		else
		{
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM (SELECT rules.id FROM rules where rules.chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$assignment_id." AND status=".$type.") order by rules.id) AS A left join (SELECT assessment_rules.rule_id FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id;") or error($PN.'17', $con);
			$result2 = mysqli_query($con, "SELECT A.id, A.chapter_id, A.rule_number, A.title, A.level, A.methodology, B.PassOrFail, B.comment FROM (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title, rules.level, rules.methodology FROM rules where rules.chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$assignment_id." AND status=".$type.") order by rules.id) AS A left join (SELECT assessment_rules.rule_id, assessment_rules.PassOrFail, assessment_rules.comment FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'18', $con);
		}
	}
	else
	{
		$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM (SELECT rules.id FROM rules where rules.chapter_id=".$chapter_id." order by rules.id) AS A left join (SELECT assessment_rules.rule_id FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id;") or error($PN.'19', $con);
	    $result2 = mysqli_query($con, "SELECT A.id, A.chapter_id, A.rule_number, A.title, A.level, A.methodology, B.PassOrFail, B.comment FROM (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title, rules.level, rules.methodology FROM rules where chapter_id=".$chapter_id." order by rules.id) AS A left join (SELECT assessment_rules.rule_id, assessment_rules.PassOrFail, assessment_rules.comment FROM assessment_rules where assessment_rules.assignment_id=".$assignment_id.") AS B on A.id=B.rule_id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'20', $con);
	}
	
	$row = mysqli_fetch_array($result);
	$recordCount = $row['RecordCount'];

	$rows = array();
	while($row = mysqli_fetch_array($result2))
	{
		$rows[] = $row;
	}		

	$response = array();
	$response['Result'] = "OK";
	$response['TotalRecordCount'] = $recordCount;
	$response['Records'] = $rows;
	print json_encode($response);

	@mysqli_close($con) ;
?>