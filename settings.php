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
	ini_set('session.use_cookies', '1');
	ini_set('session.use_only_cookies', '1');
	ini_set('session.use_trans_sid', '0');
	ini_set('session.cookie_httponly', '1');

	$pos = strrpos($_SERVER['SCRIPT_NAME'], "/");
	$path = substr($_SERVER['SCRIPT_NAME'], 0, $pos + 1);
	ini_set('session.cookie_path', $path);
	  
	ini_set('session.name', 'ASVS_Tool');
	  
	header_remove("X-Powered-By"); 

	session_start();

	if(isset($_SESSION['user']))
	{
		if( $_SESSION['activity_time'] < (time() - 2*60*60) )
		{
			session_unset();
			session_destroy();
		}
		else
			$_SESSION['activity_time'] = time();
	}
?>