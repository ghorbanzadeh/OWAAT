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

  	if(!isset($_GET['assessment_id']) || !isset($_GET['user_id']))
		error($PN.'11');

	$assessment_id = (int) $_GET['assessment_id'];
	$user_id = (int) $_GET['user_id'];
	
	$response = array();

	if(isset($_GET['admin_comment']))
	{
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'12');

		$admin_comment_tmp = strip_tags($_GET['admin_comment']);
		$admin_comment = mysql_real_escape_string($admin_comment_tmp);
		$admin_comment = trim($admin_comment);

		mysql_query("UPDATE assignment SET admin_comment='".$admin_comment."' WHERE assessment_id = ".$assessment_id." AND user_id = ".$user_id.";") or error($PN.'13');
	}

	$result = mysql_query("SELECT id, admin_comment FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id = ".$user_id.";") or error($PN.'14');

	$row = mysql_fetch_array($result);

	$response['Result'] = 'OK';
	$response['Record'] = $row;

	echo json_encode($response);

	@mysql_close();

?>