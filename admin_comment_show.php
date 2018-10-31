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
  
	include 'db.php';

  	if(!isset($_GET['assignment_id']))
		error($PN.'11', $con);

	$assignment_id = (int) $_GET['assignment_id'];
	$user_id = $_SESSION['id'];

	$result = mysqli_query($con, "SELECT user_id FROM assignment WHERE id = ".$assignment_id.";") or error($PN.'12', $con);
	$array = mysqli_fetch_array($result);

	if($array['user_id']!=$user_id)
		error($PN.'13', $con);

	$response = array();

	$result = mysqli_query($con, "SELECT id, admin_comment FROM assignment WHERE id = ".$assignment_id.";") or error($PN.'14', $con);
	$row = mysqli_fetch_array($result);

	$response['Result'] = 'OK';
	$response['Record'] = $row;

	echo json_encode($response);

	@mysqli_close($con) ;

?>