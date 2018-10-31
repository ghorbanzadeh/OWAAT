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

	if(($_GET["action"] == "update"))
	{
		if(isset($_POST["methodology"]))
		{
			$methodology_tmp = strip_tags($_POST['methodology']);
			$methodology = mysqli_real_escape_string($con, $methodology_tmp);
			$methodology = trim($methodology);
		}
		else
			error($PN.'12', $con);

		if(isset($_POST['id']))
		{
			$id = (int)$_POST['id'];
		}
		else
			error($PN.'13', $con);
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
					error($PN.'14', $con);

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
					case "methodology":
						$sort .= 'methodology';
						break;
					default:
						error($PN.'15', $con);
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
			error($PN.'16', $con);
			
		if(isset($_GET["select"]))
			$select = (int)$_GET['select'];
		else
			$select = 0;

		if($select == 0)
		{
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM rules;") or error($PN.'17', $con);
			$row = mysqli_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysqli_query($con, "SELECT id, chapter_id, rule_number, title, level, methodology FROM rules ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'18', $con);

			$rows = array();
			while($row = mysqli_fetch_array($result))
			{
				$rows[] = $row;
			}
		}
		else
		{
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM rules where chapter_id=".$select.";") or error($PN.'19', $con);
			$row = mysqli_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysqli_query($con, "SELECT id, chapter_id, rule_number, title, level, methodology FROM rules where chapter_id=".$select." ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'20', $con);

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
	else if($_GET["action"] == "update")
	{
	
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'21', $con);

		mysqli_query($con, "UPDATE rules SET methodology='".$methodology."' WHERE id = ".$id.";") or error($PN.'22', $con);

		$result = mysqli_query($con, "SELECT id, methodology FROM rules WHERE id = ".$id.";") or error($PN.'23', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);

	}
	else
		error($PN.'24', $con);

	@mysqli_close($con) ;
?>