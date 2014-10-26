<?php

$host = 'localhost';
$user = 'root';
$pass = '';

$databaseName = 'asvs2014';

$con = mysql_connect($host,$user,$pass);
$dbs = mysql_select_db($databaseName, $con) or error($PN.'10');

?>