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

	header('Content-Type: text/html; charset=utf-8');
  
	include 'db.php';

	$response = array();

  	if(isset($_GET['assessment_id']) && isset($_GET['user_id']))
	{
		if(!isset($_SESSION['admin']))
			error($PN.'10');

		$assessment_id = (int) $_GET['assessment_id'];

		$user_id = (int) $_GET['user_id'];

		if(!isset($_GET['user_comment']))
		{
			$result = mysql_query("SELECT id, user_comment FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id = ".$user_id.";") or error($PN.'11');
		}
	}
	else
	{
		if(!isset($_SESSION['user']))
			error($PN.'17');

		if(!isset($_GET['assignment_id']))
			error($PN.'12');
	
		$assignment_id = (int) $_GET['assignment_id'];

		$user_id = $_SESSION['id'];

		$result = mysql_query("SELECT user_id FROM assignment WHERE id = ".$assignment_id.";") or error($PN.'18');
		$array = mysql_fetch_array($result);

		if($array['user_id']!=$user_id)
			error($PN.'19');

		if(!isset($_GET['user_comment']))
		{
			$result = mysql_query("SELECT id, user_comment FROM assignment WHERE id = ".$assignment_id." AND user_id = ".$user_id.";") or error($PN.'13');
		}
		else//set comment
		{
	
			$user_comment_tmp = strip_tags($_GET['user_comment']);
			$user_comment = mysql_real_escape_string($user_comment_tmp);
			$user_comment = trim($user_comment);

			if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
				error($PN.'14');

			mysql_query("UPDATE assignment SET user_comment='".$user_comment."' WHERE id = ".$assignment_id." AND user_id = ".$user_id.";") or error($PN.'15');

			$result = mysql_query("SELECT id, user_comment FROM assignment WHERE id = ".$assignment_id." AND user_id = ".$user_id.";") or error($PN.'16');

		}
	}

	$row = mysql_fetch_object($result);

	$response['Result'] = 'OK';
	$response['Record'] = $row;

	echo json_encode($response);

	@mysql_close();

?>