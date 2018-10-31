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
	include "function.php";
	if(!isset($_SESSION['admin']))
		error($PN.'10', $con);

	header('Content-Type: text/html; charset=utf-8');
	
	include 'db.php';

	if(!isset($_GET['users_id']) || !isset($_GET['assessment_id']))
		error($PN.'11', $con);

	$assessment_id = (int) $_GET['assessment_id'];

	$users_id_array = explode(",", $_GET['users_id']);
	$users_id = '';
	foreach($users_id_array as $userID)
	{
		if($users_id != '')
			$users_id .= ',';

		$userID = trim($userID);
		$users_id .= (int)$userID;
	}

	$response = array();
	$rows = array();

	if($users_id==0)
		$result = mysqli_query($con, "SELECT B.id, B.uname FROM (SELECT user_id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_comment != '') AS A LEFT JOIN (SELECT id, uname FROM users) AS B ON A.user_id = B.id ORDER BY B.uname") or error($PN.'12', $con);
	else
		$result = mysqli_query($con, "SELECT B.id, B.uname FROM (SELECT user_id FROM assignment WHERE assessment_id = ".$assessment_id." AND user_id IN (".$users_id.") AND user_comment != '') AS A LEFT JOIN (SELECT id, uname FROM users) AS B ON A.user_id = B.id ORDER BY B.uname") or error($PN.'13', $con);
  
	while ($row = mysqli_fetch_array($result))
	{
		$rows[] = $row;
	}   

	$response['Result'] = 'OK';
	$response['Records'] = $rows;

	echo json_encode($response);

	@mysqli_close($con) ;

?>