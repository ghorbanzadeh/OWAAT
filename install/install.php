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
		error($PN.'10');
	}


	if(!isset($_POST['sc']) || ($_POST['sc'] != $sc))
		error($PN.'11');

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
			error($PN.'12');

		$con = mysql_connect($host,$user,$pass);
		if(!$con)
			error($PN.'23');

		mysql_query("CREATE DATABASE ".$databaseName);

		@mysql_close($con);	

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
			error($PN.'24');
		}
		
		if($response['Result'] == "OK")
		{

			//Write To The db.php File
			file_put_contents("../db.php","<?php

\$host = '".$host."';
\$user = '".$user."';
\$pass = '".$pass."';

\$databaseName = '".$databaseName."';

\$con = mysql_connect(\$host,\$user,\$pass);
\$dbs = mysql_select_db(\$databaseName, \$con) or error(\$PN.'10');

?>");

		}
	}
	else if($_GET['action'] == "config")
	{
		$organization_name = $_POST['organization_name'];//Organization Name
		$organization_address = $_POST['organization_address'];//Organization Address
		$logo = $_POST['logo'];//Logo

		if(empty($organization_name) || empty($organization_address) || empty($logo))
			error($PN.'12');

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
			$fname = mysql_real_escape_string($fname_tmp);
			$fname = trim($fname);

			$lname_tmp = strip_tags($_POST['lname']);
			$lname = mysql_real_escape_string($lname_tmp);
			$lname = trim($lname);

			$email_tmp = strip_tags($_POST['email']);
			$email = mysql_real_escape_string($email_tmp);
			$email = trim($email);
	
			$password_tmp = strip_tags($_POST['password']);
			$password = mysql_real_escape_string($password_tmp);
			$password = trim($password);
			
			$uname_tmp = strip_tags($_POST['uname']);
			$uname = mysql_real_escape_string($uname_tmp);
			$uname = trim($uname);

			if(empty($fname) || empty($lname) || empty($email) || empty($password) || empty($uname))
				error($PN.'13');
				
			if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
				error($PN.'14');
				
			if(strlen($password) < 6)
				error($PN.'15');
		}
		else
			error($PN.'16');
			
		$result_exists = mysql_query("SELECT uname FROM users WHERE uname = '".$uname."' and id != 1;") or error($PN.'17');
		$row_exists = mysql_fetch_array($result_exists);

		if($row_exists)
			error($PN.'18');

		$result1 = mysql_query("SELECT * FROM users where id = 1") or error($PN.'19');
		$row1 = mysql_fetch_array($result1);

		if($row1)
		{
			mysql_query("UPDATE users SET fname='".$fname."', lname='".$lname."', email='".$email."', uname='".$uname."', password='".sha1($password)."', administrator=1, enabled=1 WHERE id = 1;") or error($PN.'20');
		}
		else
		{
			mysql_query("INSERT INTO users(id, fname, lname, email, uname, password, administrator, enabled) VALUES(1,'".$fname."','".$lname."','".$email."','".$uname."','".sha1($password) ."',1,1)") or error($PN.'21');
		}

		$response['Result'] = "OK";
		@mysql_close();

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
		error($PN.'22');

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