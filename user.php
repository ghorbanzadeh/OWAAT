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
	
	if(!isset($_GET["action"]))
	  error($PN.'11');

	if($_GET["action"] != "list")
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'12');

	if(($_GET["action"] == "create") || (($_GET["action"] == "update") && (!isset($_POST['password']))))
	{
		if(isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]))
		{
			$fname_tmp = strip_tags($_POST['fname']);
			$fname = mysql_real_escape_string($fname_tmp);
			$fname = trim($fname);
			$fname = mb_convert_encoding($fname, 'UTF-8');

			$lname_tmp = strip_tags($_POST['lname']);
			$lname = mysql_real_escape_string($lname_tmp);
			$lname = trim($lname);
			$lname = mb_convert_encoding($lname, 'UTF-8');

			$email_tmp = strip_tags($_POST['email']);
			$email = mysql_real_escape_string($email_tmp);
			$email = trim($email);
			$email = mb_convert_encoding($email, 'UTF-8');

			if(empty($fname) || empty($lname) || empty($email))
				error($PN.'13');
				
			if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
				error($PN.'41');
		}
		else
			error($PN.'14');
		
		if($_GET["action"] == "create")
		{
			if(isset($_POST["uname"]) && isset($_POST["password"]))
			{
				$uname_tmp = strip_tags($_POST['uname']);
				$uname = mysql_real_escape_string($uname_tmp);
				$uname = trim($uname);

				$uname = strtolower($uname);

				$uname = mb_convert_encoding($uname, 'UTF-8');
				if (!preg_match("/^[a-z0-9_-]{3,30}$/", $uname))
					error($PN.'42');

				$password_tmp = strip_tags($_POST['password']);
				$password = mysql_real_escape_string($password_tmp);
				$password = trim($password);

				if(empty($uname) && empty($password))
					error($PN.'15');
					
				if(strlen($password) < 6)
					error($PN.'16');
		
				if($password != @$_POST['password2'])
					error($PN.'17');
			}
			else
				error($PN.'18');
		}

		if(isset($_POST["administrator"]))
			$administrator = (int)$_POST['administrator'];
		else
			$administrator = 0;
		
		if(isset($_POST["enabled"]))
			$enabled = (int)$_POST['enabled'];
		else
			$enabled = 0;		
	}
	
	if($_GET["action"] == "update" && isset($_POST['password']))
	{
		$password_tmp = strip_tags($_POST['password']);
		$password = mysql_real_escape_string($password_tmp);
		$password = trim($password);

		if(strlen($password) < 6)
			error($PN.'19');
		
		if($password != @$_POST['password2'])
			error($PN.'20');
	}
	
	
    if(($_GET["action"] == "update") || ($_GET["action"] == "delete"))
	{
      if(isset($_POST['id']))
      {
		$id = (int)$_POST['id'];
      }
	  else
	    error($PN.'21');
    }
	
	if($_GET["action"] == "list")
	{

		if(isset($_GET["jtSorting"]) && isset($_GET["jtStartIndex"]) && isset($_GET["jtPageSize"]))
		{
			$Sorting_array_multiple = explode(",", $_GET['jtSorting']);
			$sort = '';

			foreach($Sorting_array_multiple as $Sorting)
			{
				$Sorting = trim($Sorting);
				$Sorting_array = explode(" ", $Sorting);
				
				if($sort != '')
					$sort .= ', ';

				if(!isset($Sorting_array[0]) || !isset($Sorting_array[1]))
					error($PN.'22');

				switch ($Sorting_array[0]) {
					case "uname":
						$sort .= 'uname';
						break;
					case "fname":
						$sort .= 'fname';
						break;
					case "lname":
						$sort .= 'lname';
						break;
					case "email":
						$sort .= 'email';
						break;
					case "administrator":
						$sort .= 'administrator';
						break;
					case "enabled":
						$sort .= 'enabled';
						break;
					default:
						error($PN.'23');
				}
					
				if($Sorting_array[1] == 'ASC')
					$sort .= ' ASC';
				else
					$sort .= ' DESC';
			}

			$Sorting = $sort;
		
			$StartIndex = (int)$_GET['jtStartIndex'];
		
			$PageSize = (int)$_GET['jtPageSize'];
		}
		else
			error($PN.'24');
			
	    if(isset($_GET["select"]))
		  $select = (int)$_GET['select'];
	    else
		  $select = 0;

		$rows = array();

		if($select == 0)
		{
			$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM users;") or error($PN.'25');
			$row = mysql_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysql_query("SELECT id, fname, lname, email, uname, administrator, enabled FROM users ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'26');

			while($row = mysql_fetch_array($result))
			{
				$rows[] = $row;
			}
		}
		else
		{
			$recordCount = 0;

			$result = mysql_query("SELECT id, fname, lname, email, uname, administrator, enabled FROM users where id=".$select.";") or error($PN.'27');

			if($row = mysql_fetch_array($result))
			{
				$recordCount = 1;
				$rows[] = $row;
			}

		}

		$response = array();
		$response['Result'] = "OK";
		$response['TotalRecordCount'] = $recordCount;
		$response['Records'] = $rows;
		print json_encode($response);
	}
	else if($_GET["action"] == "create")
	{
	
		$result1 = mysql_query("SELECT uname FROM users WHERE uname = '".$uname."';") or error($PN.'28');
		$row1 = mysql_fetch_array($result1);
		
		if($row1)
		  error($PN.'29');
		
		mysql_query("INSERT INTO users(fname, lname, email, uname, password, administrator, enabled) VALUES('".$fname."','".$lname."','".$email."','".$uname."','".sha1($password) ."',".$administrator.",".$enabled.")") or error($PN.'30');

		$result = mysql_query("SELECT id, fname, lname, email, uname, administrator, enabled FROM users WHERE id = LAST_INSERT_ID();") or error($PN.'31');
		$row = mysql_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "update")
	{
		if(($id == 1) && ($_SESSION['id'] != 1))
			error($PN.'32');

		if(empty($password))
			mysql_query("UPDATE users SET fname='".$fname."', lname='".$lname."', email='".$email."', administrator=".$administrator.", enabled=".$enabled." WHERE id = ".$id.";") or error($PN.'33');
		else
			mysql_query("UPDATE users SET password='".sha1($password)."' WHERE id = ".$id.";") or error($PN.'34');

		$result = mysql_query("SELECT id, fname, lname, email, uname, administrator, enabled FROM users WHERE id = ".$id.";") or error($PN.'35');
		$row = mysql_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "delete")
	{
		if($id == 1)
			error($PN.'36');
		
		$result = mysql_query("SELECT COUNT(*) FROM assignment WHERE user_id = ".$id.";") or error($PN.'37');
		$row = mysql_fetch_row($result);
		if($row[0] > 0)
			error($PN.'38');

		mysql_query("DELETE FROM users WHERE id = ".$id.";") or error($PN.'39');

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'40');

	@mysql_close();
?>