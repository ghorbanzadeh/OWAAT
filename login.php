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

	include 'settings.php';

	session_regenerate_id(true); 

	header('Content-Type: text/html; charset=utf-8');

	include 'function.php';
	include 'db.php';

	if(isset($_SESSION['user']))
		error($PN.'10', $con);

		$response = array();

	if(isset($_POST['uname']) && isset($_POST['password']) && !empty($_POST['uname']) && !empty($_POST['password']))
	{
		$uname = mysqli_real_escape_string($con, $_POST['uname']);
		$password = mysqli_real_escape_string($con, $_POST['password']);
	
		$result = mysqli_query($con, "SELECT id, uname, administrator FROM users WHERE uname='".$uname."' AND password='".sha1($password)."' AND enabled=1") or error($PN.'11', $con);
  
		$array = mysqli_fetch_array($result);

		if($array)
		{
			$response['Result'] = 'OK';
			
			$_SESSION['user']= $array["uname"];
			$_SESSION['id']= $array["id"];
			$_SESSION['activity_time'] = time();
			if($array['administrator'] == 1)
				$_SESSION['admin']= 1;

			$_SESSION['token'] = mt_rand_str(30);

			$data['uname'] = $uname;
			log_save($array["id"], 1, $data, $con);
		}
		else
		{
			$data['uname'] = $uname;
			log_save(0, 2, $data, $con);
			error($PN.'12', $con);
		}
	}
	else
		error($PN.'13', $con);
	
	print json_encode($response);

	@mysqli_close($con) ;
	
	function log_login($user_id, $uname, $ip, $status, $con)
	{
		global $PN;
		mysqli_query($con, "INSERT INTO logging(user_id, ip, data, time, action) VALUES(".$user_id.", '".$ip."', '[uname=".$uname."]', NOW(),".$status.");") or error($PN.'14', $con);
	}
?>