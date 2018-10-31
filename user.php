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
      error($PN.'10', $con);
	
    header('Content-Type: text/html; charset=utf-8');
	
	include 'db.php';
	
	if(!isset($_GET["action"]))
	  error($PN.'11', $con);

	if($_GET["action"] != "list")
		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'12', $con);

	if(($_GET["action"] == "create") || (($_GET["action"] == "update") && (!isset($_POST['password']))))
	{
		if(isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]))
		{
			$fname_tmp = strip_tags($_POST['fname']);
			$fname = mysqli_real_escape_string($con, $fname_tmp);
			$fname = trim($fname);
			$fname = mb_convert_encoding($fname, 'UTF-8');

			$lname_tmp = strip_tags($_POST['lname']);
			$lname = mysqli_real_escape_string($con, $lname_tmp);
			$lname = trim($lname);
			$lname = mb_convert_encoding($lname, 'UTF-8');

			$email_tmp = strip_tags($_POST['email']);
			$email = mysqli_real_escape_string($con, $email_tmp);
			$email = trim($email);
			$email = mb_convert_encoding($email, 'UTF-8');

			if(empty($fname) || empty($lname) || empty($email))
				error($PN.'13', $con);
				
			if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
				error($PN.'41', $con);
		}
		else
			error($PN.'14', $con);
		
		if($_GET["action"] == "create")
		{
			if(isset($_POST["uname"]) && isset($_POST["password"]))
			{
				$uname_tmp = strip_tags($_POST['uname']);
				$uname = mysqli_real_escape_string($con, $uname_tmp);
				$uname = trim($uname);

				$uname = strtolower($uname);

				$uname = mb_convert_encoding($uname, 'UTF-8');
				if (!preg_match("/^[a-z0-9_-]{3,30}$/", $uname))
					error($PN.'42', $con);

				$password_tmp = strip_tags($_POST['password']);
				$password = mysqli_real_escape_string($con, $password_tmp);
				$password = trim($password);

				if(empty($uname) && empty($password))
					error($PN.'15', $con);
					
				if(strlen($password) < 6)
					error($PN.'16', $con);
		
				if($password != @$_POST['password2'])
					error($PN.'17', $con);
			}
			else
				error($PN.'18', $con);
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
		$password = mysqli_real_escape_string($con, $password_tmp);
		$password = trim($password);

		if(strlen($password) < 6)
			error($PN.'19', $con);
		
		if($password != @$_POST['password2'])
			error($PN.'20', $con);
	}
	
	
    if(($_GET["action"] == "update") || ($_GET["action"] == "delete"))
	{
      if(isset($_POST['id']))
      {
		$id = (int)$_POST['id'];
      }
	  else
	    error($PN.'21', $con);
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
					error($PN.'22', $con);

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
						error($PN.'23', $con);
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
			error($PN.'24', $con);
			
	    if(isset($_GET["select"]))
		  $select = (int)$_GET['select'];
	    else
		  $select = 0;

		$rows = array();

		if($select == 0)
		{
			$result = mysqli_query($con, "SELECT COUNT(*) AS RecordCount FROM users;") or error($PN.'25', $con);
			$row = mysqli_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysqli_query($con, "SELECT id, fname, lname, email, uname, administrator, enabled FROM users ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'26', $con);

			while($row = mysqli_fetch_array($result))
			{
				$rows[] = $row;
			}
		}
		else
		{
			$recordCount = 0;

			$result = mysqli_query($con, "SELECT id, fname, lname, email, uname, administrator, enabled FROM users where id=".$select.";") or error($PN.'27', $con);

			if($row = mysqli_fetch_array($result))
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
	
		$result1 = mysqli_query($con, "SELECT uname FROM users WHERE uname = '".$uname."';") or error($PN.'28', $con);
		$row1 = mysqli_fetch_array($result1);
		
		if($row1)
		  error($PN.'29', $con);
		
		mysqli_query($con, "INSERT INTO users(fname, lname, email, uname, password, administrator, enabled) VALUES('".$fname."','".$lname."','".$email."','".$uname."','".sha1($password) ."',".$administrator.",".$enabled.")") or error($PN.'30', $con);

		$result = mysqli_query($con, "SELECT id, fname, lname, email, uname, administrator, enabled FROM users WHERE id = LAST_INSERT_ID();") or error($PN.'31', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "update")
	{
		if(($id == 1) && ($_SESSION['id'] != 1))
			error($PN.'32', $con);

		if(empty($password))
			mysqli_query($con, "UPDATE users SET fname='".$fname."', lname='".$lname."', email='".$email."', administrator=".$administrator.", enabled=".$enabled." WHERE id = ".$id.";") or error($PN.'33', $con);
		else
			mysqli_query($con, "UPDATE users SET password='".sha1($password)."' WHERE id = ".$id.";") or error($PN.'34', $con);

		$result = mysqli_query($con, "SELECT id, fname, lname, email, uname, administrator, enabled FROM users WHERE id = ".$id.";") or error($PN.'35', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else if($_GET["action"] == "delete")
	{
		if($id == 1)
			error($PN.'36', $con);
		
		$result = mysqli_query($con, "SELECT COUNT(*) FROM assignment WHERE user_id = ".$id.";") or error($PN.'37', $con);
		$row = mysqli_fetch_row($con, $result);
		if($row[0] > 0)
			error($PN.'38', $con);

		mysqli_query($con, "DELETE FROM users WHERE id = ".$id.";") or error($PN.'39', $con);

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'40', $con);

	@mysqli_close($con) ;
?>