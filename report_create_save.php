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

	include 'settings.php';
	include 'function.php';

	if(!isset($_SESSION['admin']))
		error($PN.'10');
	
	header('Content-Type: text/html; charset=utf-8');
	
	include 'db.php';

	if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'11');

	$assessment_rules_id_unchecked = '';
		
	if(isset($_GET['assessment_rules_id_unchecked']))
	{
		$assessment_rules_id_unchecked_array = explode(",", $_GET['assessment_rules_id_unchecked']);
		foreach($assessment_rules_id_unchecked_array as $AssessmentRulesID)
		{
			if($assessment_rules_id_unchecked != '')
				$assessment_rules_id_unchecked .= ',';

			$AssessmentRulesID = trim($AssessmentRulesID);
			$assessment_rules_id_unchecked .= (int)$AssessmentRulesID;
		}
	}
	else
		error($PN.'12');

	mysql_query("DELETE FROM report_rules WHERE id IN (".$assessment_rules_id_unchecked.");") or error($PN.'13');


	if(isset($_GET['assessment_rules_id']))
	{			
		$assessment_rules_id_array = explode(",", $_GET['assessment_rules_id']);
		foreach($assessment_rules_id_array as $AssessmentRulesID)
		{

			$AssessmentRulesID = trim($AssessmentRulesID);
			$assessment_rules_id = (int)$AssessmentRulesID;
			
			$result = mysql_query("SELECT assignment_id, rule_id FROM assessment_rules WHERE id = ".$assessment_rules_id.";") or error($PN.'14');
			$row = mysql_fetch_array($result);

			if($row)
			{
				$result_report = mysql_query("SELECT id FROM report_rules WHERE assignment_id = ".$row['assignment_id']." AND rule_id = ".$row['rule_id'].";") or error($PN.'15');
				$row_report = mysql_fetch_array($result_report);
			
				if(!$row_report)
					mysql_query("INSERT INTO report_rules (assignment_id, rule_id, PassOrFail, comment, last_modified) SELECT assessment_rules.assignment_id, assessment_rules.rule_id, assessment_rules.PassOrFail, assessment_rules.comment, assessment_rules.last_modified FROM assessment_rules WHERE assessment_rules.id = ".$assessment_rules_id.";") or error($PN.'16');
			}
		}
	}
	else
		error($PN.'17');

	$response = array();
	
	$response['Result'] = "OK";
	print json_encode($response);

	@mysql_close();
?>