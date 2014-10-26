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

	$Page_Name = basename($_SERVER['PHP_SELF']);
	$PN = 10;
	switch ($Page_Name) {
		case 'admin_comment.php':
			$PN = '10';
			break;
		case 'assessment.php':
			$PN = '11';
			break;
		case 'verification_save.php':
			$PN = '12';
			break;
		case 'assessment-show.php':
			$PN = '13';
			break;
		case 'assignment.php':
			$PN = '14';
			break;
		case 'assignment_save.php':
			$PN = '15';
			break;
		case 'assignment-show.php':
			$PN = '16';
			break;
		case 'assignment-show-chapter.php':
			$PN = '17';
			break;
		case 'chapters.php':
			$PN = '18';
			break;
		case 'chapters_assigned.php':
			$PN = '19';
			break;
		case 'db.php':
			$PN = '20';
			break;
		case 'login.php':
			$PN = '21';
			break;
		case 'methodology-action.php':
			$PN = '22';
			break;
		case 'report.php':
			$PN = '23';
			break;
		case 'results.php':
			$PN = '24';
			break;
		case 'rules.php':
			$PN = '25';
			break;
		case 'rules-action.php':
			$PN = '26';
			break;
		case 'rules-wizard.php':
			$PN = '27';
			break;
		case 'show_results.php':
			$PN = '28';
			break;
		case 'show_results_assessment.php':
			$PN = '29';
			break;
		case 'uname.php':
			$PN = '30';
			break;
		case 'user.php':
			$PN = '31';
			break;
		case 'user_assignment_show.php':
			$PN = '32';
			break;
		case 'user_comment.php':
			$PN = '33';
			break;
		case 'user-profile.php':
			$PN = '34';
			break;
		case 'install.php':
			$PN = '35';
			break;
		case 'admin_comment_show.php':
			$PN = '36';
			break;
		case 'report_create_save.php':
			$PN = '37';
			break;
		case 'report_create_show.php':
			$PN = '38';
			break;
		case 'report_result.php':
			$PN = '39';
			break;
		case 'user_comment_posted.php':
			$PN = '40';
			break;
		case 'verification_delete.php':
			$PN = '41';
			break;
		case 'logout.php':
			$PN = '42';
			break;
		case 'logging.php':
			$PN = '43';
			break;
		case 'upload.php':
			$PN = '45';
			break;
		default:
			$PN = 'PN';
	}
	
	function validate_token($token)
	{
		$token_valid = true;

		if(!isset($_SESSION['token']) || !isset($token))
			$token_valid = false;
		else
		{
			if($_SESSION['token'] != $token)
				$token_valid = false;
		}
		
		return $token_valid;
	}
	
	function mt_rand_str ($length) {
		$alphabet_number = 'abcdefghijklmnopqrstuvwxyz1234567890';
		$string = '';
		for ($i = 0; $i < $length; $i++)
		{
			$string .= $alphabet_number[mt_rand(0, 35)];
		}
		return $string;
	}

	function error($error_number)
	{
		$PageNumber = substr($error_number, 0, 2);
		$ErrorNumber = substr($error_number, 2);
		$response = array();
		$response['Result'] = "ERROR";
		$response['Message'] = message_get($PageNumber, $ErrorNumber);
		print json_encode($response);

		@mysql_close();
		
		exit();
	}
	
	function message_get($PageNumber, $ErrorNumber)
	{

		$massage = array(
				1 => 'Your Session Has Expired, Please Log In Again!',
				2 => 'Please Fill In All Required Fields!',
				3 => 'Token Validation Error!',
				4 => 'MySQL Query Execution Error!',
				5 => 'Sorting Error!',
				6 => 'The Assessment Exists!',
				7 => 'Its Corresponding Assignment Exists, Please Delete It First!',
				8 => 'Assignment Error!',
				9 => 'Could Not Select The Database!',
				10 => 'Incorrect User Name Or Password!',
				11 => 'The Results Of Assignment Exist In The Corresponding Report!',
				12 => 'The Assessment Not Found!',
				13 => 'The Rule Exists!',
				14 => 'The Review Of This Rule Exists In Results Table!',
				15 => 'The Review Of This Rule Exists In Reports Table!',
				16 => 'The Result Not Found!',
				17 => 'The Password Must Be At Least 6 Characters!',
				18 => 'The Password And Confirmation Password Do Not Match!',
				19 => 'The User Exists!',
				20 => 'You Do Not Have Sufficient Privileges To Update The Profile Of Master User!',//, Please Use The Program Installation Wizard To Change The Profile!',
				21 => 'It Is Not Possible To Delete The Account Of Master User!',//, Please Use The Program Installation Wizard To Change Its Profile!',
				22 => 'The User Has Assignment, Please Delete It First!',
				23 => 'The User Not Found!',
				24 => 'The Action Is Incorrect!',
				25 => 'The Email Is Not Valid!',
				26 => "The Security Code Is Not Valid, Please Check The 'install\\sc.php' File!",
				27 => 'The Chapter Not Found!',
				28 => 'The Review Of This Rule Exists In Results Table, It Is Not Possible To Change The Chapter Number!',
				29 => 'The Review Of This Rule Exists In Reports Table, It Is Not Possible To Change The Chapter Number!',
				30 => 'The Review Exists In Reports Table!',
				31 => 'A User Is Logged In!',
				32 => 'You Do Not Have Sufficient Privileges To Delete Logs!',
				33 => 'The Level Number Is Incorrect!',
				34 => 'The Rule Not Found!',
				35 => 'The User Name Is Invalid!',
				36 => 'Unable To Connect To The Database!',
				37 => 'The SQL Statement Could Not Be Executed!'
			);

		$error = array(
						10	=> array(	//admin_comment.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[3],
										13 => $massage[4],
										14 => $massage[4]
								),
						11	=> array(	//assessment.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[3],
										13 => $massage[2],
										14 => $massage[2],
										15 => $massage[2],
										16 => $massage[5],
										17 => $massage[5],
										18 => $massage[2],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[6],
										24 => $massage[4],
										25 => $massage[4],
										26 => $massage[4],
										27 => $massage[4],
										28 => $massage[4],
										29 => $massage[4],
										30 => $massage[7],
										31 => $massage[24],
										32 => $massage[4],
										33 => $massage[4]
								),
						12	=> array(	//verification_save.php
										10 => $massage[1],
										11 => $massage[3],
										12 => $massage[2],
										13 => $massage[2],
										14 => $massage[2],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[4],
										24 => $massage[4],
										25 => $massage[4],
										26 => $massage[4],
										27 => $massage[4],
										28 => $massage[4],
										29 => $massage[4],
										30 => $massage[4],
										31 => $massage[8],
										32 => $massage[4]
								),
						13	=> array(	//assessment-show.php
										10 => $massage[1],
										11 => $massage[4],
										12 => $massage[4],
										13 => $massage[4],
										14 => $massage[4]
								),
						14	=> array(	//assignment.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[2],
										13 => $massage[5],
										14 => $massage[5],
										15 => $massage[2],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[2],
										19 => $massage[3],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[4],
										24 => $massage[4],
										25 => $massage[24],
										26 => $massage[4],
										27 => $massage[4],
										28 => $massage[4],
										29 => $massage[11]
								),
						15	=> array(	//assignment_save.php
										10 => $massage[1],
										11 => $massage[3],
										12 => $massage[2],
										13 => $massage[2],
										14 => $massage[4],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[4],
										24 => $massage[4],
										25 => $massage[4],
										26 => $massage[4],
										27 => $massage[4],
										28 => $massage[12],
										29 => $massage[4],
										30 => $massage[23],
										31 => $massage[4],
										32 => $massage[27]
								),
						16	=> array(	//assignment-show.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[4],
										13 => $massage[4],
										14 => $massage[4],
										15 => $massage[4],
										16 => $massage[4]
								),
						17	=> array(	//assignment-show-chapter.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[4],
										13 => $massage[4],
										14 => $massage[4],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[8]
								),
						18	=> array(	//chapters.php
										10 => $massage[1],
										11 => $massage[4]
								),
						19	=> array(	//chapters_assigned.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[4],
										13 => $massage[4],
										14 => $massage[4],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4]
								),
						20	=> array(	//db.php
										10 => $massage[9]
								),
						21	=> array(	//login.php
										10 => $massage[31],
										11 => $massage[4],
										12 => $massage[10],
										13 => $massage[2],
										14 => $massage[4]
								),
						22	=> array(	//methodology-action.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[2],
										13 => $massage[2],
										14 => $massage[5],
										15 => $massage[5],
										16 => $massage[2],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[3],
										22 => $massage[4],
										23 => $massage[4],
										24 => $massage[24]
								),
						23	=> array(	//report.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[5],
										13 => $massage[5],
										14 => $massage[2],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[3],
										20 => $massage[2],
										21 => $massage[4],
										22 => $massage[24]
								),
						24	=> array(	//results.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[4],
										13 => $massage[16],
										14 => $massage[4],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[16],
										24 => $massage[4],
										25 => $massage[4]
								),
						25	=> array(	//rules.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[5],
										13 => $massage[5],
										14 => $massage[2],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[4],
										20 => $massage[4]
								),
						26	=> array(	//rules-action.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[3],
										13 => $massage[2],
										14 => $massage[2],
										15 => $massage[2],
										16 => $massage[5],
										17 => $massage[5],
										18 => $massage[2],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[4],
										24 => $massage[13],
										25 => $massage[4],
										26 => $massage[4],
										27 => $massage[4],
										28 => $massage[13],
										29 => $massage[4],
										30 => $massage[4],
										31 => $massage[4],
										32 => $massage[14],
										33 => $massage[4],
										34 => $massage[15],
										35 => $massage[4],
										36 => $massage[24],
										37 => $massage[4],
										38 => $massage[27],
										39 => $massage[4],
										40 => $massage[4],
										41 => $massage[28],
										42 => $massage[4],
										43 => $massage[29],
										44 => $massage[4],
										45 => $massage[27],
										46 => $massage[4],
										47 => $massage[4],
										48 => $massage[4],
										49 => $massage[4],
										50 => $massage[4],
										51 => $massage[4],
										52 => $massage[4],
										53 => $massage[4],
										54 => $massage[4],
										55 => $massage[4],
										56 => $massage[4],
										57 => $massage[4],
										58 => $massage[4],
										59 => $massage[33],
										60 => $massage[34]
								),
						27	=> array(	//rules-wizard.php
										10 => $massage[1],
										11 => $massage[4],
										12 => $massage[4],
										13 => $massage[4],
										14 => $massage[2],
										15 => $massage[4],
										16 => $massage[8]
								),
						28	=> array(	//show_results.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[5],
										13 => $massage[5],
										14 => $massage[2],
										15 => $massage[4],
										16 => $massage[4]
								),
						29	=> array(	//show_results_assessment.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[5],
										13 => $massage[5],
										14 => $massage[2],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[16],
										20 => $massage[4],
										21 => $massage[4]
								),
						30	=> array(	//uname.php
										10 => $massage[1],
										11 => $massage[4],
										12 => $massage[4],
										13 => $massage[4],
										14 => $massage[4]
								),
						31	=> array(	//user.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[3],
										13 => $massage[2],
										14 => $massage[2],
										15 => $massage[2],
										16 => $massage[17],
										17 => $massage[18],
										18 => $massage[2],
										19 => $massage[17],
										20 => $massage[18],
										21 => $massage[2],
										22 => $massage[5],
										23 => $massage[5],
										24 => $massage[2],
										25 => $massage[4],
										26 => $massage[4],
										27 => $massage[4],
										28 => $massage[4],
										29 => $massage[19],
										30 => $massage[4],
										31 => $massage[4],
										32 => $massage[20],
										33 => $massage[4],
										34 => $massage[4],
										35 => $massage[4],
										36 => $massage[21],
										37 => $massage[4],
										38 => $massage[22],
										39 => $massage[4],
										40 => $massage[24],
										41 => $massage[25],
										42 => $massage[35]
								),
						32	=> array(	//user_assignment_show.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[4],
										13 => $massage[23],
										14 => $massage[4],
										15 => $massage[4],
										16 => $massage[12],
										17 => $massage[4],
										18 => $massage[24],
										19 => $massage[4],
										20 => $massage[4]
								),
						33	=> array(	//user_comment.php
										10 => $massage[1],
										11 => $massage[4],
										12 => $massage[2],
										13 => $massage[4],
										14 => $massage[3],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[1],
										18 => $massage[4],
										19 => $massage[8]
								),
						34	=> array(	//user-profile.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[2],
										13 => $massage[25],
										14 => $massage[2],										
										15 => $massage[17],
										16 => $massage[18],
										17 => $massage[4],
										18 => $massage[23],
										19 => $massage[3],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[24]
								),
						35	=> array(	//install.php
										10 => $massage[26],
										11 => $massage[26],
										12 => $massage[2],
										13 => $massage[2],
										14 => $massage[25],
										15 => $massage[17],
										16 => $massage[2],
										17 => $massage[4],
										18 => $massage[19],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[24],
										23 => $massage[36],
										24 => $massage[37]
								),
						36	=> array(	//admin_comment_show.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[4],
										13 => $massage[8],
										14 => $massage[4]
								),
						37	=> array(	//report_create_save.php
										10 => $massage[1],
										11 => $massage[3],
										12 => $massage[2],
										13 => $massage[4],
										14 => $massage[4],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[2]
								),
						38	=> array(	//report_create_show.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[5],
										13 => $massage[5],
										14 => $massage[2],
										15 => $massage[2],
										16 => $massage[24],
										17 => $massage[2],
										18 => $massage[2],
										19 => $massage[4],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[4],
										24 => $massage[4],
										25 => $massage[4],
										26 => $massage[16],
										27 => $massage[4],
										28 => $massage[4],
										29 => $massage[4]
								),
						39	=> array(	//report_result.php
										10 => $massage[1],
										11 => $massage[3],
										12 => $massage[2],
										13 => $massage[2],
										14 => $massage[5],
										15 => $massage[5],
										16 => $massage[2],
										17 => $massage[4],
										18 => $massage[4],
										19 => $massage[2],
										20 => $massage[4],
										21 => $massage[4],
										22 => $massage[4],
										23 => $massage[24]
								),
						40	=> array(	//user_comment_posted.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[4],
										13 => $massage[4]
								),
						41	=> array(	//verification_delete.php
										10 => $massage[1],
										11 => $massage[3],
										12 => $massage[2],
										13 => $massage[4],
										14 => $massage[30],
										15 => $massage[4]
								),
						42	=> array(	//logout.php
										10 => $massage[3],
										11 => $massage[4]
								),
						43	=> array(	//logging.php
										10 => $massage[1],
										11 => $massage[2],
										12 => $massage[5],
										13 => $massage[5],
										14 => $massage[2],
										15 => $massage[4],
										16 => $massage[4],
										17 => $massage[4],
										18 => $massage[3],
										19 => $massage[2],
										20 => $massage[4],
										21 => $massage[24],
										22 => $massage[32],
										23 => $massage[4]
								),
						44	=> array(	//function.php
										10 => $massage[4]
								),
						45	=> array(	//upload.php
										10 => $massage[26],
										11 => $massage[26]
								)
					);
					
		if(isset($error[$PageNumber][$ErrorNumber]))
			return '#'.$PageNumber.$ErrorNumber.' - '.$error[$PageNumber][$ErrorNumber];
		else
			return '#'.$PageNumber.$ErrorNumber.' - An Unknown Error Has Been Occurred.';
	}
	
	function log_save($user_id, $action, $data)
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		$data_save = '';
		if($data != '')
		{
			foreach($data as $variable => $value)
			{
				$data_save .= '['.$variable.'='.$value.'] ';
			}
		}

		mysql_query("INSERT INTO logging(user_id, ip, data, time, action) VALUES(".$user_id.",'".$ip."', '".$data_save."', NOW(),".$action.");") or error('4410');
	}
?>