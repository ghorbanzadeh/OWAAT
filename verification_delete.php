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
		error($PN.'10');

	header('Content-Type: text/html; charset=utf-8');

	include 'db.php';

	if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
		error($PN.'11');


	if(isset($_POST['id']) && isset($_GET["assignment_id"]))
	{
		$rule_id = (int)$_POST['id'];
		$assignment_id = (int)$_GET['assignment_id'];		
	}
	else
		error($PN.'12');

	$result = mysql_query("SELECT id FROM report_rules WHERE assignment_id = ".$assignment_id." AND rule_id = ".$rule_id.";") or error($PN.'13');
	if(mysql_fetch_array($result))
		error($PN.'14');

	mysql_query("DELETE FROM assessment_rules WHERE assignment_id = ".$assignment_id." AND rule_id = ".$rule_id.";") or error($PN.'15');

	$response = array();
	$response['Result'] = "OK";
	print json_encode($response);

	@mysql_close();
?>