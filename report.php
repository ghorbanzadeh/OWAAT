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
					error($PN.'12');

				switch ($Sorting_array[0])
				{
					case "assessment_name":
						$sort .= 'assessment_name';
						break;
					case "description":
						$sort .= 'description';
						break;
					default:
						error($PN.'13');
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
			error($PN.'14');

		$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM assessment WHERE id IN (SELECT assessment_id FROM assignment WHERE id IN (SELECT assignment_id FROM report_rules)) ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'15');
		$result2 = mysql_query("SELECT id, assessment_name, description FROM assessment WHERE id IN (SELECT assessment_id FROM assignment WHERE id IN (SELECT assignment_id FROM report_rules)) ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'16');

		$row = mysql_fetch_array($result);
		$recordCount = $row['RecordCount'];

		$rows = array();
		while($row = mysql_fetch_array($result2))
		{

			$assessment_id = $row['id'];
			$result_count1 = mysql_query("SELECT COUNT(*) FROM rules WHERE chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id."));") or error($PN.'17');
			$array_count1 = mysql_fetch_array($result_count1);

			$result_count2 = mysql_query("SELECT COUNT(DISTINCT rule_id) FROM report_rules WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id.");") or error($PN.'18');
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

		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'19');

		if(isset($_POST['id']))
		{
			$id = (int)$_POST['id'];
		}
		else
			error($PN.'20');

		mysql_query("DELETE FROM report_rules WHERE assignment_id IN (select id FROM assignment WHERE assessment_id = ".$id. ");") or error($PN.'21');

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'22');

	@mysql_close();
?>