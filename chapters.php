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
		error($PN.'10');
		
	header('Content-Type: text/html; charset=utf-8');

	include 'db.php';

	$response = array();
	$rows = array();

	$result = mysql_query("SELECT id, chapter_name FROM chapters order by id") or error($PN.'11');
  
	while ($row = mysql_fetch_array($result))
	{
		$rows[] = $row;
	}   

	$response['Result'] = 'OK';
	$response['Records'] = $rows;

	echo json_encode($response);

	@mysql_close();

?>