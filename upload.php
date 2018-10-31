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
	if(!file_exists('install/sc.php') || !file_exists('install/upload/UploadHandler.php'))
		exit();

	include 'install/sc.php';//Security Code.
	include 'function.php';

	if(!isset($time)/* || $time < (time() - 10*60)*/)
	{
		$sc = mt_rand_str(30);
		$time = time();
		file_put_contents("install/sc.php","<?php
\$sc = '".$sc."'; //Security Code.
\$time = '".$time."';//It is valid for 10 minutes.
?>");
		error($PN.'10', $con);
	}

	if(!isset($_COOKIE['ASVS_Tool_SC']) || ($_COOKIE['ASVS_Tool_SC'] != $sc))
		error($PN.'11', $con);

	require('install/upload/UploadHandler.php');
	$upload_handler = new UploadHandler();

?>