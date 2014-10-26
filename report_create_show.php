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

	include 'settings.php';
	include 'function.php';

	if(!isset($_SESSION['admin']))
		error($PN.'10');
	
	header('Content-Type: text/html; charset=utf-8');
	
	include 'db.php';

	if(!isset($_GET['action']))
		error($PN.'11');

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
	
	$assessment_id = '';
	$chapters_id = '';
	$users_id = '';
	if($_GET['action'] == "assessment" || $_GET['action'] == "user" || $_GET['action'] == "chapter")
	{
		if(isset($_GET["assessment_id"]))
		{
			$assessment_id = (int) $_GET["assessment_id"];
		}
		else
			error($PN.'15');	
	}
	else
		error($PN.'16');

	if($_GET['action'] == "user" || $_GET['action'] == "chapter")
	{
		if(isset($_GET["users_id"]))
		{			
			$users_id_array = explode(",", $_GET['users_id']);
			$users_id = '';
			foreach($users_id_array as $userID)
			{
				if($users_id != '')
					$users_id .= ',';

				$userID = trim($userID);
				$users_id .= (int)$userID;
			}
		}
		else
			error($PN.'17');
	}

	if($_GET['action'] == "chapter")
	{
		if(isset($_GET["chapters_id"]))
		{
			$chapters_id_array = explode(",", $_GET['chapters_id']);
			$chapters_id = '';
			foreach($chapters_id_array as $chapterID)
			{
				if($chapters_id != '')
					$chapters_id .= ',';

				$chapterID = trim($chapterID);
				$chapters_id .= (int)$chapterID;
			}

		}
		else
			error($PN.'18');
	}
	
	if($_GET['action'] == "assessment")
	{
		$result_assignment_chapters_id = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id.");") or error($PN.'19');
	}
	else if($_GET['action'] == "user")
	{
		if($users_id != 0)
			$result_assignment_chapters_id = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id IN (".$users_id."));") or error($PN.'20');
		else
			$result_assignment_chapters_id = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id IN (SELECT user_id FROM (SELECT user_id from assignment WHERE assessment_id=".$assessment_id.") AS A LEFT JOIN (SELECT id FROM users) AS B on A.user_id = B.id ORDER BY id));") or error($PN.'21');

	}
	else
	{
		if($users_id != 0 && $chapters_id != 0)
			$result_assignment_chapters_id = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id IN (".$users_id.")) AND chapter_id IN (".$chapters_id.")") or error($PN.'22');
		else if($users_id != 0 && $chapters_id == 0)
			$result_assignment_chapters_id = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id IN (".$users_id.")) AND chapter_id IN (SELECT id FROM chapters WHERE id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." and user_id IN (SELECT user_id FROM (SELECT user_id from assignment WHERE assessment_id=".$assessment_id.") AS A LEFT JOIN (SELECT id FROM users) AS B on A.user_id = B.id)) ORDER BY id))") or error($PN.'23');
		else if($users_id == 0 && $chapters_id != 0)
			$result_assignment_chapters_id = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id IN (SELECT user_id FROM (SELECT user_id from assignment WHERE assessment_id=".$assessment_id.") AS A LEFT JOIN (SELECT id FROM users) AS B on A.user_id = B.id ORDER BY id)) AND chapter_id IN (".$chapters_id.")") or error($PN.'24');
		else
			$result_assignment_chapters_id = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id IN (SELECT user_id FROM (SELECT user_id from assignment WHERE assessment_id=".$assessment_id.") AS A LEFT JOIN (SELECT id FROM users) AS B on A.user_id = B.id ORDER BY id)) AND chapter_id IN (SELECT id FROM chapters WHERE id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id = ".$assessment_id." and user_id IN (SELECT user_id FROM (SELECT user_id from assignment WHERE assessment_id=".$assessment_id.") AS A LEFT JOIN (SELECT id FROM users) AS B on A.user_id = B.id)) ORDER BY id))") or error($PN.'25');
	}

	$assignment_chapters_id = '';
	while($row_assignment_chapters_id = mysql_fetch_array($result_assignment_chapters_id))
	{
		if($row_assignment_chapters_id['id'] > 0)//It has value
		{
			if($assignment_chapters_id == '')
				$assignment_chapters_id = $row_assignment_chapters_id['id'];
			else
				$assignment_chapters_id .= ','.$row_assignment_chapters_id['id'];
		}
	}
		
	if($assignment_chapters_id == '')
		error($PN.'26');

	$response = array();
	
	$result_report_id = mysql_query("SELECT A.id AS report_rules_id, B.id AS assessment_rules_id FROM (SELECT id, assignment_id, rule_id FROM report_rules WHERE assignment_id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS A LEFT JOIN (SELECT id, assignment_id, rule_id FROM assessment_rules WHERE assignment_id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS B ON A.assignment_id = B.assignment_id AND A.rule_id = B.rule_id;") or error($PN.'27');
	$rows_report_id = array();
	while($row_report_id = mysql_fetch_array($result_report_id))
	{
		$rows_report_id[] = $row_report_id;
	}

	$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM (SELECT E.assignment_id, E.id FROM (SELECT A.assignment_id, B.id, B.chapter_id FROM (SELECT chapter_id, assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id.")) AS A LEFT JOIN (SELECT rules.id, rules.chapter_id FROM rules) AS B ON A.chapter_id = B.chapter_id) AS E LEFT JOIN (SELECT C.id, D.uname FROM (SELECT id, user_id FROM assignment WHERE id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS C LEFT JOIN (SELECT id, uname FROM users) AS D ON C.user_id = D.id) AS F ON E.assignment_id =F.id) AS G INNER JOIN (SELECT assignment_id, rule_id FROM assessment_rules WHERE assignment_id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS H ON G.id = H.rule_id and H.assignment_id=G.assignment_id") or error($PN.'28');
	$result2 = mysql_query("SELECT G.chapter_id, G.rule_number, G.title, G.uname, H.id, H.PassOrFail, H.comment, H.last_modified FROM (SELECT E.assignment_id, E.id, E.chapter_id, E.rule_number, E.title, F.uname FROM (SELECT A.assignment_id, B.id, B.chapter_id, B.rule_number, B.title FROM (SELECT chapter_id, assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id.")) AS A LEFT JOIN (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title FROM rules) AS B ON A.chapter_id = B.chapter_id) AS E LEFT JOIN (SELECT C.id, D.uname FROM (SELECT id, user_id FROM assignment WHERE id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS C LEFT JOIN (SELECT id, uname FROM users) AS D ON C.user_id = D.id) AS F ON E.assignment_id =F.id) AS G INNER JOIN (SELECT id, assignment_id, rule_id, PassOrFail, comment, last_modified FROM assessment_rules WHERE assignment_id IN (SELECT assignment_id FROM assignment_chapter WHERE id IN (".$assignment_chapters_id."))) AS H ON G.id = H.rule_id and H.assignment_id=G.assignment_id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'29');

	$row = mysql_fetch_array($result);
	$recordCount = $row['RecordCount'];

	$rows = array();
	while($row = mysql_fetch_array($result2))
	{
		$row['selected'] = '0';
		$row['report_rules_id'] = '0';

		foreach ($rows_report_id as $report_id)
		{
			if($row['id'] == $report_id['assessment_rules_id'])
			{
				$row['selected'] = '1';
				$row['report_rules_id'] = $report_id['report_rules_id'];
				break;
			}
		}

		$rows[] = $row;
	}	

	$response['Result'] = "OK";
	$response['TotalRecordCount'] = $recordCount;
	$response['Records'] = $rows;

	print json_encode($response);

	@mysql_close();
?>