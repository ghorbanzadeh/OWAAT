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
	if(!isset($_SESSION['user']))
		error($PN.'10', $con);
	
	header('Content-Type: text/html; charset=utf-8');
  
	include 'db.php';

	$response = array();
	$rows = array();
  
	if(!isset($_GET['type']) || !isset($_GET['id']))
		error($PN.'11', $con);

	$id = (int)$_GET['id'];

	$type = (int)$_GET['type'];
	
	$result = mysqli_query($con, "SELECT user_id FROM assignment WHERE id = ".$id.";") or error($PN.'16', $con);
	$array = mysqli_fetch_array($result);

	if($array['user_id']!=$_SESSION['id'])
		error($PN.'17', $con);

	if($type == 0)
		$result = mysqli_query($con, "SELECT id, chapter_name FROM chapters WHERE id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$id.");") or error($PN.'12', $con);
	else
		$result = mysqli_query($con, "SELECT id, chapter_name FROM chapters WHERE id IN (SELECT chapter_id FROM assignment_chapter WHERE assignment_id =".$id." AND status=".$type.");") or error($PN.'13', $con);
    
	while ($row = mysqli_fetch_array($result))
	{

		$result_count1 = mysqli_query($con, "SELECT COUNT(*) FROM rules WHERE chapter_id = ".$row['id'].";") or error($PN.'14', $con);
		$result_count2 = mysqli_query($con, "SELECT COUNT(*) FROM assessment_rules WHERE assignment_id=".$id." AND rule_id IN (SELECT id FROM rules WHERE chapter_id = ".$row['id'].");") or error($PN.'15', $con);

		$array_count1 = mysqli_fetch_array($result_count1);
		$array_count2 = mysqli_fetch_array($result_count2);

		if($array_count1[0] != 0)
		{
			$percent = round(($array_count2[0]/$array_count1[0]), 4);
			$percent *= 100;
		}
		else
			$percent = 0;

		$row['percent'] = $percent;

		$rows[] = $row;
	}

  	$response['Result'] = 'OK';
	$response['Records'] = $rows;

	echo json_encode($response);

	@mysqli_close($con) ;

?>