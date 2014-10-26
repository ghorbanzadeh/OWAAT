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
		error($PN.'10');
	
	header('Content-Type: text/html; charset=utf-8');
	
	if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
		error($PN.'11');

	include 'db.php';

	if(isset($_GET["assignment_id"]))
	{
	  $assignment_id = (int)$_GET['assignment_id'];		
	}
	else
		error($PN.'12');

	if(isset($_POST["id"]))
	  $rule_id = (int)$_POST['id'];
	else if(isset($_GET["id"]))
	  $rule_id = (int)$_GET['id'];
	else
		error($PN.'13');
  
	if(isset($_POST['comment']))
	{
	  $comment_tmp = strip_tags($_POST['comment']);
	  $comment = mysql_real_escape_string($comment_tmp);
	  $comment = trim($comment);
	}
	else if(isset($_GET['comment']))
	{
	  $comment_tmp = strip_tags($_GET['comment']);
	  $comment = mysql_real_escape_string($comment_tmp); 
	  $comment = trim($comment);
	}
	else
		error($PN.'14');

	if(isset($_POST['PassOrFail']))
	  $PassOrFail = (int) $_POST['PassOrFail'];
	else if(isset($_GET['PassOrFail']))
	  $PassOrFail = (int) $_GET['PassOrFail'];
	else
	  $PassOrFail = 0;
	
	$result = mysql_query("SELECT id FROM rules WHERE id = ".$rule_id." AND chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id = ".$assignment_id.")") or error($PN.'15');
  
	$array = mysql_fetch_array($result);
 
	if($array)
	{

		$result2 = mysql_query("SELECT id FROM assessment_rules WHERE assignment_id=".$assignment_id." AND rule_id=".$rule_id.";") or error($PN.'16');
		 
		$array2 = mysql_fetch_array($result2);

		if($array2)
		{
			mysql_query("UPDATE assessment_rules SET PassOrFail='".$PassOrFail."', comment='".$comment."', last_modified= NOW() WHERE id = ".$array2["id"].";") or error($PN.'17');

			$result = mysql_query("SELECT rule_id, PassOrFail, comment, last_modified FROM assessment_rules WHERE id = ".$array2["id"].";") or error($PN.'18');
			$row = mysql_fetch_array($result);
			$row['change_status'] = 'No';
			$row['completed'] = 'No';
		}
		else
		{
			mysql_query("INSERT INTO assessment_rules (assignment_id, rule_id, PassOrFail, comment, last_modified) VALUES (".$assignment_id.",".$rule_id.",".$PassOrFail.",'".$comment."', NOW());") or error($PN.'19');

			$result = mysql_query("SELECT rule_id, PassOrFail, comment, last_modified FROM assessment_rules WHERE id = LAST_INSERT_ID();") or error($PN.'20');
			$row = mysql_fetch_array($result);

			$row['change_status'] = 'No';
			$row['completed'] = 'No';

			//Change New to Uncompleted in assignment Table
			mysql_query("UPDATE assignment SET status=2 WHERE id=".$assignment_id." AND status=1;") or error($PN.'21');

			$result_chapter = mysql_query("SELECT chapter_id FROM rules WHERE id =".$rule_id.";") or error($PN.'22');
			$array_chapter = mysql_fetch_array($result_chapter);
			
			//Change New to Uncompleted in assignment_chapter Table
			mysql_query("UPDATE assignment_chapter SET status=2 WHERE assignment_id=".$assignment_id." AND chapter_id=".$array_chapter['chapter_id']." AND status=1;") or error($PN.'32');			

			if(mysql_affected_rows() == 1)
			{
				$row['change_status'] = 'Yes';
			}
			
			$result_count1 = mysql_query("SELECT COUNT(*) FROM rules WHERE chapter_id =".$array_chapter['chapter_id'].";") or error($PN.'23');
			$array_count1 = mysql_fetch_array($result_count1);

			$result_count2 = mysql_query("SELECT COUNT(*) FROM assessment_rules WHERE assignment_id=".$assignment_id." AND rule_id IN (SELECT id FROM rules WHERE chapter_id =".$array_chapter['chapter_id'].");") or error($PN.'24');
			$array_count2 = mysql_fetch_array($result_count2);
			
			//Set status=3 in assignment_chapter Table
			if($array_count1[0] == $array_count2[0])
			{
				mysql_query("UPDATE assignment_chapter SET status=3 WHERE assignment_id=".$assignment_id." AND chapter_id=".$array_chapter['chapter_id'].";") or error($PN.'25');
				if(mysql_affected_rows() == 1)
				{
					$row['change_status'] = 'Yes';
				}
			}

			//Change Uncompleted to Completed in assignment Table
			$result3 = mysql_query("SELECT COUNT(*) FROM assignment_chapter WHERE assignment_id=".$assignment_id." and status!=3;") or error($PN.'26');
			$array3 = mysql_fetch_array($result3);
			if($array3[0] == 0)
			{
				mysql_query("UPDATE assignment SET status=3 WHERE id=".$assignment_id." AND status=2;") or error($PN.'27');
				$row['completed'] = 'Yes';
			}

			//Set Complete=1 in assessment Table
			$result4 = mysql_query("SELECT assessment_id FROM assignment WHERE id=".$assignment_id.";") or error($PN.'28');
			$array4 = mysql_fetch_array($result4);

			$result5 = mysql_query("SELECT COUNT(*) FROM assignment WHERE assessment_id=".$array4[0]." AND status!=3;") or error($PN.'29');
			$array5 = mysql_fetch_array($result5);
			if($array5[0] == 0)
				mysql_query("UPDATE assessment SET complete=1, complete_time= NOW() WHERE id=".$array4[0].";") or error($PN.'30');
		}
	}
	else
		error($PN.'31');

	$response = array();
	$response['Result'] = "OK";
	$response['Record'] = $row;
	print json_encode($response);

	@mysql_close();
?>