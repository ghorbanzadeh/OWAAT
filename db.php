<?php

$host = 'db';
$user = 'devuser';
$pass = 'devpass';

$databaseName = 'devdb';

$con = mysql_connect($host,$user,$pass);
$dbs = mysql_select_db($databaseName, $con) or error($PN.'10');

?>