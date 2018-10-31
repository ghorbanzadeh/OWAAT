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

	if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
		error($PN.'11', $con);

	if(isset($_GET["user_id"]) && isset($_GET["assessment_id"]) && isset($_GET["chapters_id"]))
	{
		$user_id = (int)$_GET['user_id'];		
		$assessment_id = (int)$_GET['assessment_id'];
		$chapters_id_tmp = strip_tags($_GET['chapters_id']);
		$chapters_id = mysqli_real_escape_string($con, $chapters_id_tmp);
		$chapters_id = trim($chapters_id);

		$chapters_id_array = explode(",", $_GET['chapters_id']);
		$chapters_id = '';
		foreach($chapters_id_array as $chapterID)
		{
			if($chapters_id != '')
				$chapters_id .= ',';

			$chapterID = trim($chapterID);
			$chapters_id .= (int)$chapterID;
		}

		if((empty($chapters_id) && $chapters_id != 0) || (strpos($chapters_id, "null") !== false))
			error($PN.'12', $con);
	}
	else
		error($PN.'13', $con);
		
	$comment = false;
	if(isset($_GET["admin_comment"]))
	{
		$admin_comment_tmp = strip_tags($_GET['admin_comment']);
		$admin_comment = mysqli_real_escape_string($con, $admin_comment_tmp);
		$admin_comment = trim($admin_comment);
		$comment = true;
	}

	$result_user = mysqli_query($con, "SELECT id FROM assessment WHERE id = ".$assessment_id.";") or error($PN.'27', $con);
	$array_user = mysqli_fetch_array($result_user);
	if(!$array_user)
		error($PN.'28', $con);

	$result_user = mysqli_query($con, "SELECT id FROM users WHERE id = ".$user_id.";") or error($PN.'29', $con);
	$array_user = mysqli_fetch_array($result_user);
	if(!$array_user)
		error($PN.'30', $con);

	$result_user = mysqli_query($con, "SELECT id FROM chapters WHERE id IN (".$chapters_id.");") or error($PN.'31', $con);
	$array_user = mysqli_fetch_array($result_user);
	if(!$array_user)
		error($PN.'32', $con);

	$result = mysqli_query($con, "SELECT id FROM assignment WHERE user_id=".$user_id." and assessment_id=".$assessment_id.";") or error($PN.'14', $con);
  
	$array = mysqli_fetch_array($result);
 
	if(!$array)
	{
		if($comment == true)
			mysqli_query($con, "insert into assignment (user_id, assessment_id, status, admin_comment) values (".$user_id.",".$assessment_id.",1,'".$admin_comment."');") or error($PN.'15', $con);
		else
			mysqli_query($con, "insert into assignment (user_id, assessment_id, status) values (".$user_id.",".$assessment_id.",1);") or error($PN.'16', $con);
	
		$result = mysqli_query($con, "SELECT id FROM assignment WHERE id = LAST_INSERT_ID();") or error($PN.'17', $con);
		$array = mysqli_fetch_array($result);
		$assignment_id = $array['id'];
	}
	else
	{
		$assignment_id = $array['id'];

		if($comment == true)
			mysqli_query($con, "update assignment set admin_comment='".$admin_comment."' where id=".$assignment_id.";") or error($PN.'18', $con);
	}

	if($chapters_id == 0)
	{
		$result_chapter = mysqli_query($con, "SELECT id FROM chapters;") or error($PN.'21', $con);
		while($chapter_id = mysqli_fetch_array($result_chapter))
		{
			$result = mysqli_query($con, "SELECT id FROM assignment_chapter WHERE assignment_id = ".$assignment_id." and chapter_id = ".$chapter_id['id'].";") or error($PN.'22', $con);
			$array = mysqli_fetch_array($result);
			if(!$array)
				mysqli_query($con, "insert into assignment_chapter (chapter_id, assignment_id, status, assignment_time) values (".$chapter_id['id'].",".$assignment_id.",1 , NOW());") or error($PN.'23', $con);
		}
	}
	else
	{
		$uncompleted_run = false;
		$chapters_id_array = explode(",", $chapters_id);
		foreach ($chapters_id_array as $chapterID) {
			$chapter_id = (int)$chapterID;
			$result_chapter = mysqli_query($con, "SELECT COUNT(*) FROM chapters WHERE id = ".$chapter_id.";") or error($PN.'24', $con);
			$array_chapter = mysqli_fetch_row($con, $result_chapter);
			if($array_chapter[0] == 1)
			{
				$result = mysqli_query($con, "SELECT id FROM assignment_chapter WHERE assignment_id = ".$assignment_id." and chapter_id = ".$chapter_id.";") or error($PN.'25', $con);
				$array = mysqli_fetch_array($result);
				if(!$array)
				{
					mysqli_query($con, "insert into assignment_chapter (chapter_id, assignment_id, status, assignment_time) values (".$chapter_id.",".$assignment_id.",1 , NOW());") or error($PN.'26', $con);
					if($uncompleted_run == false)
					{
						uncompleted($assignment_id, $assessment_id);
						$uncompleted_run = true;
					}
				}
			}
		}
	}
	
	function uncompleted($assignment_id, $assessment_id)
	{
		global $PN;
		//Change Completed to Uncompleted in assignment Table
		mysqli_query($con, "update assignment set status=2 where id=".$assignment_id." and status=3;") or error($PN.'19', $con);

		//Set Complete=0 in assessment Table
		mysqli_query($con, "update assessment set complete=0, complete_time='0' where id=".$assessment_id." and complete=1;") or error($PN.'20', $con);
	}
	
	$response = array();
	$response['Result'] = "OK";
	print json_encode($response);

	@mysqli_close($con) ;
?>