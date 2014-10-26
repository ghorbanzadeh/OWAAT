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

	if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
		error($PN.'11');

	if(isset($_GET["user_id"]) && isset($_GET["assessment_id"]) && isset($_GET["chapters_id"]))
	{
		$user_id = (int)$_GET['user_id'];		
		$assessment_id = (int)$_GET['assessment_id'];
		$chapters_id_tmp = strip_tags($_GET['chapters_id']);
		$chapters_id = mysql_real_escape_string($chapters_id_tmp); 
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
			error($PN.'12');
	}
	else
		error($PN.'13');
		
	$comment = false;
	if(isset($_GET["admin_comment"]))
	{
		$admin_comment_tmp = strip_tags($_GET['admin_comment']);
		$admin_comment = mysql_real_escape_string($admin_comment_tmp); 
		$admin_comment = trim($admin_comment);
		$comment = true;
	}

	$result_user = mysql_query("SELECT id FROM assessment WHERE id = ".$assessment_id.";") or error($PN.'27');
	$array_user = mysql_fetch_array($result_user);
	if(!$array_user)
		error($PN.'28');

	$result_user = mysql_query("SELECT id FROM users WHERE id = ".$user_id.";") or error($PN.'29');
	$array_user = mysql_fetch_array($result_user);
	if(!$array_user)
		error($PN.'30');

	$result_user = mysql_query("SELECT id FROM chapters WHERE id IN (".$chapters_id.");") or error($PN.'31');
	$array_user = mysql_fetch_array($result_user);
	if(!$array_user)
		error($PN.'32');

	$result = mysql_query("SELECT id FROM assignment WHERE user_id=".$user_id." and assessment_id=".$assessment_id.";") or error($PN.'14');
  
	$array = mysql_fetch_array($result);
 
	if(!$array)
	{
		if($comment == true)
			mysql_query("insert into assignment (user_id, assessment_id, status, admin_comment) values (".$user_id.",".$assessment_id.",1,'".$admin_comment."');") or error($PN.'15');	  
		else
			mysql_query("insert into assignment (user_id, assessment_id, status) values (".$user_id.",".$assessment_id.",1);") or error($PN.'16');	  
	
		$result = mysql_query("SELECT id FROM assignment WHERE id = LAST_INSERT_ID();") or error($PN.'17');
		$array = mysql_fetch_array($result);
		$assignment_id = $array['id'];
	}
	else
	{
		$assignment_id = $array['id'];

		if($comment == true)
			mysql_query("update assignment set admin_comment='".$admin_comment."' where id=".$assignment_id.";") or error($PN.'18');
	}

	if($chapters_id == 0)
	{
		$result_chapter = mysql_query("SELECT id FROM chapters;") or error($PN.'21');			
		while($chapter_id = mysql_fetch_array($result_chapter))
		{
			$result = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id = ".$assignment_id." and chapter_id = ".$chapter_id['id'].";") or error($PN.'22');
			$array = mysql_fetch_array($result);
			if(!$array)
				mysql_query("insert into assignment_chapter (chapter_id, assignment_id, status, assignment_time) values (".$chapter_id['id'].",".$assignment_id.",1 , NOW());") or error($PN.'23');	  
		}
	}
	else
	{
		$uncompleted_run = false;
		$chapters_id_array = explode(",", $chapters_id);
		foreach ($chapters_id_array as $chapterID) {
			$chapter_id = (int)$chapterID;
			$result_chapter = mysql_query("SELECT COUNT(*) FROM chapters WHERE id = ".$chapter_id.";") or error($PN.'24');
			$array_chapter = mysql_fetch_row($result_chapter);
			if($array_chapter[0] == 1)
			{
				$result = mysql_query("SELECT id FROM assignment_chapter WHERE assignment_id = ".$assignment_id." and chapter_id = ".$chapter_id.";") or error($PN.'25');
				$array = mysql_fetch_array($result);
				if(!$array)
				{
					mysql_query("insert into assignment_chapter (chapter_id, assignment_id, status, assignment_time) values (".$chapter_id.",".$assignment_id.",1 , NOW());") or error($PN.'26');	  
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
		mysql_query("update assignment set status=2 where id=".$assignment_id." and status=3;") or error($PN.'19');

		//Set Complete=0 in assessment Table
		mysql_query("update assessment set complete=0, complete_time='0' where id=".$assessment_id." and complete=1;") or error($PN.'20');		
	}
	
	$response = array();
	$response['Result'] = "OK";
	print json_encode($response);

	@mysql_close();
?>