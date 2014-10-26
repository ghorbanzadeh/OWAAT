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
					error($PN.'12');

				switch ($Sorting_array[0]) {
					case "uname":
						$sort .= 'uname';
						break;
					case "ip":
						$sort .= 'ip';
						break;
					case "data":
						$sort .= 'data';
						break;
					case "time":
						$sort .= 'time';
						break;
					case "action":
						$sort .= 'action';
						break;
					default:
						error($PN.'13');
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
			error($PN.'14');
			
	    if(isset($_GET["user_id"]))
		  $user_id = (int)$_GET['user_id'];
	    else
		  $user_id = 0;

		$rows = array();

		if($user_id == 0)
		{
			$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM logging;") or error($PN.'15');
			$row = mysql_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysql_query("SELECT C.id, C.uname, C.ip, C.data, C.time, D.action FROM (SELECT A.id, B.uname, A.ip, A.data, A.time, A.action FROM (SELECT id, user_id, ip, data, time, action FROM logging) AS A LEFT JOIN (SELECT id, uname FROM users) AS B ON A.user_id = B.id) AS C LEFT JOIN (SELECT id, action FROM logging_action) AS D ON C.action = D.id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'16');

			while($row = mysql_fetch_array($result))
			{
				if($row['uname'] == '')
					$row['uname'] = 'anonymous';

				$rows[] = $row;
			}
		}
		else
		{
			$result = mysql_query("SELECT COUNT(*) AS RecordCount FROM logging WHERE user_id = ".$user_id.";") or error($PN.'23');
			$row = mysql_fetch_array($result);
			$recordCount = $row['RecordCount'];

			$result = mysql_query("SELECT C.id, C.uname, C.ip, C.data, C.time, D.action FROM (SELECT A.id, B.uname, A.ip, A.data, A.time, A.action FROM (SELECT id, user_id, ip, data, time, action FROM logging WHERE user_id = ".$user_id.") AS A LEFT JOIN (SELECT id, uname FROM users WHERE id = ".$user_id.") AS B ON A.user_id = B.id) AS C LEFT JOIN (SELECT id, action FROM logging_action) AS D ON C.action = D.id ORDER BY ".$Sorting." LIMIT ".$StartIndex.",".$PageSize.";") or error($PN.'17');

			while($row = mysql_fetch_array($result))
			{
				$rows[] = $row;
			}		
		}

		$response = array();
		$response['Result'] = "OK";
		$response['TotalRecordCount'] = $recordCount;
		$response['Records'] = $rows;
		print json_encode($response);
	}
	else if($_GET["action"] == "delete")
	{
	
		if($_SESSION['id'] != 1)
			error($PN.'22');

		if($_GET["action"] != "list")
			if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
				error($PN.'18');

		if(isset($_POST['id']))
		{
			$id = (int)$_POST['id'];
		}
		else
			error($PN.'19');

		mysql_query("DELETE FROM logging WHERE id = ".$id.";") or error($PN.'20');

		$response = array();
		$response['Result'] = "OK";
		print json_encode($response);
	}
	else
		error($PN.'21');

	@mysql_close();
?>