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

	$response = array();
	$rows = array();

	$result = mysqli_query($con, "SELECT id, assessment_name FROM assessment ORDER BY assessment_name") or error($PN.'11', $con);

	while ($row = mysqli_fetch_array($result))
	{
		$result_count1 = mysqli_query($con, "SELECT COUNT(*) FROM (SELECT id, chapter_id, assignment_id FROM assignment_chapter WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$row['id'].")) AS A LEFT JOIN (SELECT id, chapter_id FROM rules) AS B ON A.chapter_id = B.chapter_id;") or error($PN.'12', $con);
		$array_count1 = mysqli_fetch_array($result_count1);

		$result_count2 = mysqli_query($con, "SELECT COUNT(*) FROM assessment_rules WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$row['id'].");") or error($PN.'13', $con);
		$array_count2 = mysqli_fetch_array($result_count2);

		if($array_count1[0] != 0)
		{
			$percent = round(($array_count2[0]/$array_count1[0]), 4);
			$percent *= 100;
		}
		else
			$percent = 0;

		$row['assessment_name'] .= " (".$percent."%) ";

		$rows[] = $row;
	}

	$response['Result'] = 'OK';
	$response['Records'] = $rows;

	echo json_encode($response);

	@mysqli_close($con) ;

?>