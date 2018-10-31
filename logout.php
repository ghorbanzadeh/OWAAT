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

	header('Content-Type: text/html; charset=utf-8');

	if(isset($_SESSION['user']))
	{
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'10', $con);

		include 'db.php';
		log_save($_SESSION['id'], 3, '', $con);
		@mysqli_close($con) ;

		session_destroy();
	}

	$response = array();  

	$response['Result'] = 'OK';

	echo json_encode($response);
?>