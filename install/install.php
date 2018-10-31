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

	error_reporting(0);
	include 'sc.php';//Security Code.
	include '../function.php';

	if(!isset($time) || $time < (time() - 10*60))
	{
		$sc = mt_rand_str(30);
		$time = time();
		file_put_contents("sc.php","<?php
\$sc = '".$sc."'; //Security Code.
\$time = '".$time."';//It is valid for 10 minutes.
?>");
		error($PN.'10', $con);
	}


	if(!isset($_POST['sc']) || ($_POST['sc'] != $sc))
		error($PN.'11', $con);

	$response = array();

	if($_GET['action'] == "security_code")
	{
		$response['Result'] = "OK";
	}
	else if($_GET['action'] == "create_table")
	{
		$host = $_POST['host'];//Host Name
		$user = $_POST['un'];//MySQL User Name
		$pass = $_POST['password'];//MySQL password
		$databaseName = $_POST['db'];//Database Name

		if(empty($host) || empty($user) || empty($databaseName))
			error($PN.'12', $con);

		$con = mysqli_connect($host,$user,$pass,$databaseName);
		if(!$con)
			error($PN.'23');

		mysqli_query($con, "CREATE DATABASE IF NOT EXISTS ".$databaseName);

		@mysqli_close($con);

		try
		{
			$db = new PDO('mysql:host='.$host.';dbname='.$databaseName, $user, @$pass);

			$sql = file_get_contents('asvs.sql');//Load SQL Commands From The File.

			$db->exec($sql);//Execute SQL Commands

			$db = null;
		
			$response['Result'] = "OK";
		}
		catch(PDOException $ex)
		{
			error($PN.'24', $con);
		}
		
		if($response['Result'] == "OK")
		{

			//Write To The db.php File
			file_put_contents("../db.php","<?php

\$host = '".$host."';
\$user = '".$user."';
\$pass = '".$pass."';

\$databaseName = '".$databaseName."';

\$con = mysqli_connect(\$host,\$user,\$pass,\$databaseName) or error(\$PN.'10');

?>");

		}
	}
	else if($_GET['action'] == "config")
	{
		$organization_name = $_POST['organization_name'];//Organization Name
		$organization_address = $_POST['organization_address'];//Organization Address
		$logo = $_POST['logo'];//Logo

		if(empty($organization_name) || empty($organization_address) || empty($logo))
			error($PN.'12', $con);

		//Write to The db.php File
		file_put_contents("../certificate/config.php","<?php

\$organization_name = '".$organization_name."';
\$organization_address = '".$organization_address."';
\$image_name = '".$logo."';

?>");

		$response['Result'] = "OK";
	}
	else if($_GET['action'] == "create_user")
	{
		include '../db.php';
		
		if(isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["uname"]))
		{
			$fname_tmp = strip_tags($_POST['fname']);
			$fname = mysqli_real_escape_string($con, $fname_tmp);
			$fname = trim($fname);

			$lname_tmp = strip_tags($_POST['lname']);
			$lname = mysqli_real_escape_string($con, $lname_tmp);
			$lname = trim($lname);

			$email_tmp = strip_tags($_POST['email']);
			$email = mysqli_real_escape_string($con, $email_tmp);
			$email = trim($email);
	
			$password_tmp = strip_tags($_POST['password']);
			$password = mysqli_real_escape_string($con, $password_tmp);
			$password = trim($password);
			
			$uname_tmp = strip_tags($_POST['uname']);
			$uname = mysqli_real_escape_string($con, $uname_tmp);
			$uname = trim($uname);

			if(empty($fname) || empty($lname) || empty($email) || empty($password) || empty($uname))
				error($PN.'13', $con);
				
			if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
				error($PN.'14', $con);
				
			if(strlen($password) < 6)
				error($PN.'15', $con);
		}
		else
			error($PN.'16', $con);
			
		$result_exists = mysqli_query($con, "SELECT uname FROM users WHERE uname = '".$uname."' and id != 1;") or error($PN.'17', $con);
		$row_exists = mysqli_fetch_array($result_exists);

		if($row_exists)
			error($PN.'18', $con);

		$result1 = mysqli_query($con, "SELECT * FROM users where id = 1") or error($PN.'19', $con);
		$row1 = mysqli_fetch_array($result1);

		if($row1)
		{
			mysqli_query($con, "UPDATE users SET fname='".$fname."', lname='".$lname."', email='".$email."', uname='".$uname."', password='".sha1($password)."', administrator=1, enabled=1 WHERE id = 1;") or error($PN.'20', $con);
		}
		else
		{
			mysqli_query($con, "INSERT INTO users(id, fname, lname, email, uname, password, administrator, enabled) VALUES(1,'".$fname."','".$lname."','".$email."','".$uname."','".sha1($password) ."',1,1)") or error($PN.'21', $con);
		}

		$response['Result'] = "OK";
		@mysqli_close($con);

	}
	else if($_GET['action'] == "delete")//Delete Installation Directory
	{
		delete_installation_directory("../install");

		if(!file_exists('../install'))
		{
			$response['Result'] = "OK";
		}
	}
	else
		error($PN.'22', $con);

	function delete_installation_directory($dir)
	{
		foreach (glob($dir."/*") as $file)
		{
			if(is_file($file))
				unlink($file);
			else
			{
				if(is_dir($file))
					delete_installation_directory($file);
			}
		}

		@rmdir($dir);
	}

	print json_encode($response);

?>