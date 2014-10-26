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
	
	if(($_GET["action"] == "update") || ($_GET["action"] == "delete"))
	{
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'11');

		if(isset($_POST['id']))
		{
			$id = (int)$_POST['id'];
		}
		else
			error($PN.'12');
	}

	if($_GET["action"] == "list")
	{
		if(isset($_GET["assessment_id"]))
		{
			$assessment_id = (int)$_GET['assessment_id'];
		}
		else
			error($PN.'13');

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
					error($PN.'14');

				switch ($Sorting_array[0]) {
					case "chapter_id":
						$sort .= 'chapter_id';
						break;
					case "rule_number":
						$sort .= 'rule_number';
						break;
					case "title":
						$sort .= 'title';
						break;
					case "PassOrFail":
						$sort .= 'PassOrFail';
						break;
					case "comment":
						$sort .= 'comment';
						break;
					case "last_modified":
						$sort .= 'last_modified';
						break;
					default:
						error($PN.'15');
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
			error($PN.'16');

		$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM (SELECT report_rules.assignment_id FROM report_rules WHERE report_rules.assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id.")) AS A left join (SELECT assignment.id FROM assignment WHERE assessment_id=".$assessment_id.") AS B on A.assignment_id=B.id") or error($PN.'17');
		$result2 = mysql_query("SELECT E.id, E.PassOrFail, E.comment, E.last_modified, E.uname, F.chapter_id, F.rule_number, F.title FROM (SELECT C.id, C.rule_id, C.PassOrFail, C.comment, C.last_modified, D.uname FROM (SELECT A.id, A.rule_id, A.PassOrFail, A.comment, A.last_modified, B.user_id FROM (SELECT report_rules.id, report_rules.assignment_id, report_rules.rule_id, report_rules.PassOrFail, report_rules.comment, report_rules.last_modified FROM report_rules WHERE report_rules.assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id.")) AS A left join (SELECT assignment.id, assignment.user_id FROM assignment WHERE assessment_id=".$assessment_id.") AS B on A.assignment_id=B.id) AS C left join (SELECT users.id, users.uname FROM users) AS D on C.user_id=D.id) AS E left join (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title FROM rules) AS F on E.rule_id=F.id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'18');
	
		$row = mysql_fetch_array($result);
		$recordCount = $row['RecordCount'];

		$rows = array();
		while($row = mysql_fetch_array($result2))
		{
			$rows[] = $row;
		}		

		$response = array();
		$response['Result'] = "OK";
		$response['TotalRecordCount'] = $recordCount;
		$response['Records'] = $rows;
		print json_encode($response);
	}
	else if($_GET["action"] == "update")
	{
		if(isset($_POST['PassOrFail']))
			$PassOrFail = (int) $_POST['PassOrFail'];
		else
			$PassOrFail = 0;
	  
		if(isset($_POST['comment']))
		{
			$comment_tmp = strip_tags($_POST['comment']);
			$comment = mysql_real_escape_string($comment_tmp); 		
			$comment = trim($comment);
		}
		else
			error($PN.'19');	

		mysql_query("update report_rules set PassOrFail='".$PassOrFail."', comment='".$comment."', last_modified= NOW() where id = ".$id.";") or error($PN.'20');

		$result = mysql_query("SELECT id, comment FROM report_rules WHERE id = ".$id.";") or error($PN.'21');
		$row = mysql_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	
	}
	else if($_GET["action"] == "delete")
	{
		mysql_query("DELETE FROM report_rules WHERE id = ".$id.";") or error($PN.'22');

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'23');

	@mysql_close();
?>