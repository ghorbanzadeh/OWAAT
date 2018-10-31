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
	
	if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
		error($PN.'11', $con);

	include 'db.php';

	if(isset($_GET["assignment_id"]))
	{
	  $assignment_id = (int)$_GET['assignment_id'];		
	}
	else
		error($PN.'12', $con);

	if(isset($_POST["id"]))
	  $rule_id = (int)$_POST['id'];
	else if(isset($_GET["id"]))
	  $rule_id = (int)$_GET['id'];
	else
		error($PN.'13', $con);
  
	if(isset($_POST['comment']))
	{
	  $comment_tmp = strip_tags($_POST['comment']);
	  $comment = mysqli_real_escape_string($con, $comment_tmp);
	  $comment = trim($comment);
	}
	else if(isset($_GET['comment']))
	{
	  $comment_tmp = strip_tags($_GET['comment']);
	  $comment = mysqli_real_escape_string($con, $comment_tmp);
	  $comment = trim($comment);
	}
	else
		error($PN.'14', $con);

	if(isset($_POST['PassOrFail']))
	  $PassOrFail = (int) $_POST['PassOrFail'];
	else if(isset($_GET['PassOrFail']))
	  $PassOrFail = (int) $_GET['PassOrFail'];
	else
	  $PassOrFail = 0;
	
	$result = mysqli_query($con, "SELECT id FROM rules WHERE id = ".$rule_id." AND chapter_id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id = ".$assignment_id.")") or error($PN.'15', $con);
  
	$array = mysqli_fetch_array($result);
 
	if($array)
	{

		$result2 = mysqli_query($con, "SELECT id FROM assessment_rules WHERE assignment_id=".$assignment_id." AND rule_id=".$rule_id.";") or error($PN.'16', $con);
		 
		$array2 = mysqli_fetch_array($result2);

		if($array2)
		{
			mysqli_query($con, "UPDATE assessment_rules SET PassOrFail='".$PassOrFail."', comment='".$comment."', last_modified= NOW() WHERE id = ".$array2["id"].";") or error($PN.'17', $con);

			$result = mysqli_query($con, "SELECT rule_id, PassOrFail, comment, last_modified FROM assessment_rules WHERE id = ".$array2["id"].";") or error($PN.'18', $con);
			$row = mysqli_fetch_array($result);
			$row['change_status'] = 'No';
			$row['completed'] = 'No';
		}
		else
		{
			mysqli_query($con, "INSERT INTO assessment_rules (assignment_id, rule_id, PassOrFail, comment, last_modified) VALUES (".$assignment_id.",".$rule_id.",".$PassOrFail.",'".$comment."', NOW());") or error($PN.'19', $con);

			$result = mysqli_query($con, "SELECT rule_id, PassOrFail, comment, last_modified FROM assessment_rules WHERE id = LAST_INSERT_ID();") or error($PN.'20', $con);
			$row = mysqli_fetch_array($result);

			$row['change_status'] = 'No';
			$row['completed'] = 'No';

			//Change New to Uncompleted in assignment Table
			mysqli_query($con, "UPDATE assignment SET status=2 WHERE id=".$assignment_id." AND status=1;") or error($PN.'21', $con);

			$result_chapter = mysqli_query($con, "SELECT chapter_id FROM rules WHERE id =".$rule_id.";") or error($PN.'22', $con);
			$array_chapter = mysqli_fetch_array($result_chapter);
			
			//Change New to Uncompleted in assignment_chapter Table
			mysqli_query($con, "UPDATE assignment_chapter SET status=2 WHERE assignment_id=".$assignment_id." AND chapter_id=".$array_chapter['chapter_id']." AND status=1;") or error($PN.'32', $con);

			if(mysqli_affected_rows($con)  == 1)
			{
				$row['change_status'] = 'Yes';
			}
			
			$result_count1 = mysqli_query($con, "SELECT COUNT(*) FROM rules WHERE chapter_id =".$array_chapter['chapter_id'].";") or error($PN.'23', $con);
			$array_count1 = mysqli_fetch_array($result_count1);

			$result_count2 = mysqli_query($con, "SELECT COUNT(*) FROM assessment_rules WHERE assignment_id=".$assignment_id." AND rule_id IN (SELECT id FROM rules WHERE chapter_id =".$array_chapter['chapter_id'].");") or error($PN.'24', $con);
			$array_count2 = mysqli_fetch_array($result_count2);
			
			//Set status=3 in assignment_chapter Table
			if($array_count1[0] == $array_count2[0])
			{
				mysqli_query($con, "UPDATE assignment_chapter SET status=3 WHERE assignment_id=".$assignment_id." AND chapter_id=".$array_chapter['chapter_id'].";") or error($PN.'25', $con);
				if(mysqli_affected_rows($con)  == 1)
				{
					$row['change_status'] = 'Yes';
				}
			}

			//Change Uncompleted to Completed in assignment Table
			$result3 = mysqli_query($con, "SELECT COUNT(*) FROM assignment_chapter WHERE assignment_id=".$assignment_id." and status!=3;") or error($PN.'26', $con);
			$array3 = mysqli_fetch_array($result3);
			if($array3[0] == 0)
			{
				mysqli_query($con, "UPDATE assignment SET status=3 WHERE id=".$assignment_id." AND status=2;") or error($PN.'27', $con);
				$row['completed'] = 'Yes';
			}

			//Set Complete=1 in assessment Table
			$result4 = mysqli_query($con, "SELECT assessment_id FROM assignment WHERE id=".$assignment_id.";") or error($PN.'28', $con);
			$array4 = mysqli_fetch_array($result4);

			$result5 = mysqli_query($con, "SELECT COUNT(*) FROM assignment WHERE assessment_id=".$array4[0]." AND status!=3;") or error($PN.'29', $con);
			$array5 = mysqli_fetch_array($result5);
			if($array5[0] == 0)
				mysqli_query($con, "UPDATE assessment SET complete=1, complete_time= NOW() WHERE id=".$array4[0].";") or error($PN.'30', $con);
		}
	}
	else
		error($PN.'31', $con);

	$response = array();
	$response['Result'] = "OK";
	$response['Record'] = $row;
	print json_encode($response);

	@mysqli_close($con) ;
?>