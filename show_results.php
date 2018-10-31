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

	if(isset($_GET["assignment_chapters_id"]))
	{	  
		$assignment_chapters_id_array = explode(",", $_GET['assignment_chapters_id']);
		$assignment_chapters_id = '';
		foreach($assignment_chapters_id_array as $chapterID)
		{
			if($assignment_chapters_id != '')
				$assignment_chapters_id .= ',';

			$chapterID = trim($chapterID);
			$assignment_chapters_id .= (int)$chapterID;
		}
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
					$sort .= 'G.chapter_id';
					break;
				case "rule_number":
					$sort .= 'G.rule_number';
					break;
				case "title":
					$sort .= 'G.title';
					break;
				case "uname":
					$sort .= 'G.uname';
					break;
				case "PassOrFail":
					$sort .= 'H.PassOrFail';
					break;
				case "comment":
					$sort .= 'H.comment';
					break;
				case "last_modified":
					$sort .= 'H.last_modified';
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

	$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM (SELECT E.assignment_id, E.id FROM (SELECT A.assignment_id, B.id, B.chapter_id FROM (SELECT chapter_id, assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id.")) AS A LEFT JOIN (SELECT rules.id, rules.chapter_id FROM rules) AS B ON A.chapter_id = B.chapter_id) AS E LEFT JOIN (SELECT C.id, D.uname FROM (SELECT id, user_id FROM assignment WHERE id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS C LEFT JOIN (SELECT id, uname FROM users) AS D ON C.user_id = D.id) AS F ON E.assignment_id =F.id) AS G INNER JOIN (SELECT assignment_id, rule_id FROM assessment_rules WHERE assignment_id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS H ON G.id = H.rule_id and H.assignment_id=G.assignment_id") or error($PN.'15', $con);
	$result2 = mysqli_query($con, "SELECT G.chapter_id, G.rule_number, G.title, G.uname, H.id, H.PassOrFail, H.comment, H.last_modified FROM (SELECT E.assignment_id, E.id, E.chapter_id, E.rule_number, E.title, F.uname FROM (SELECT A.assignment_id, B.id, B.chapter_id, B.rule_number, B.title FROM (SELECT chapter_id, assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id.")) AS A LEFT JOIN (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title FROM rules) AS B ON A.chapter_id = B.chapter_id) AS E LEFT JOIN (SELECT C.id, D.uname FROM (SELECT id, user_id FROM assignment WHERE id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS C LEFT JOIN (SELECT id, uname FROM users) AS D ON C.user_id = D.id) AS F ON E.assignment_id =F.id) AS G INNER JOIN (SELECT id, assignment_id, rule_id, PassOrFail, comment, last_modified FROM assessment_rules WHERE assignment_id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS H ON G.id = H.rule_id and H.assignment_id=G.assignment_id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'16', $con);

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