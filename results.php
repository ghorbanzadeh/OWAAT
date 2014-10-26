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

	ini_set('max_execution_time', 300);

	header('Content-Type: text/html; charset=utf-8');

    if(!isset($_SESSION['admin']))
		error($PN.'10');
	
	include 'db.php';
	include 'certificate/config.php';

	define ('HEADER_LOGO', $image_name);

	define ('HEADER_LOGO_WIDTH', 15);

	ob_end_clean();
	// Include the main TCPDF library.
	require_once('tcpdf/tcpdf.php');

	if(isset($_GET["assessment_id"]))
	{
		$assessment_id = (int)$_GET['assessment_id'];
	}
	else
		error($PN.'11');
	
	$result_assessment_name = mysql_query("SELECT assessment_name FROM assessment WHERE id=".$assessment_id.";") or error($PN.'12');
	$row_assessment_name = mysql_fetch_array($result_assessment_name);
	if($row_assessment_name['assessment_name'])
		define ('ASSESSMENT_NAME', $row_assessment_name['assessment_name']);
	else
		error($PN.'13');
		
	define ('HEADER_TITLE', 'OWASP ASVS');
	
	define ('HEADER_STRING', ASSESSMENT_NAME." Results\n".$organization_address);

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('ASVS Assessment Tool');
	$pdf->SetTitle('Assessment Report');
	$pdf->SetSubject('Assessment Report');
	$pdf->SetKeywords('OWASP, ASVS, Verification, Report');

	// set default header data
	$pdf->SetHeaderData(HEADER_LOGO, HEADER_LOGO_WIDTH, HEADER_TITLE, HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language dependent data:
	$lg = Array();
	$lg['a_meta_charset'] = 'UTF-8';
	$lg['a_meta_dir'] = 'ltr';
	$lg['a_meta_language'] = 'en';
	$lg['w_page'] = 'Page';

	// set some language-dependent strings (optional)
	$pdf->setLanguageArray($lg);

	$level = 0;
	$result_level = mysql_query("SELECT COUNT(*) FROM report_rules WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id.") AND PassOrFail=2 AND rule_id IN (SELECT id FROM rules WHERE level=1);") or error($PN.'14');
	$row_level = mysql_fetch_array($result_level);
	if($row_level[0] == 0)
	{
		$level = 1;
		$result_level = mysql_query("SELECT COUNT(*) FROM report_rules WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id.") AND PassOrFail=2 AND rule_id IN (SELECT id FROM rules WHERE level=2);") or error($PN.'15');
		$row_level = mysql_fetch_array($result_level);
		if($row_level[0] == 0)
		{
			$level = 2;

			$result_level = mysql_query("SELECT COUNT(*) FROM report_rules WHERE assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id.") AND PassOrFail=2 AND rule_id IN (SELECT id FROM rules WHERE level=3);") or error($PN.'16');
			$row_level = mysql_fetch_array($result_level);
			if($row_level[0] == 0)
			{
				$level = 3;
			}
		}
	}

	// add a page
	$pdf->AddPage();


	$htmlfirstpage = '<span align="center" color="#000000"><h2>Application Security Verification Standard (ASVS)</h2><br/>&nbsp;<br/>&nbsp;<br/>
	<h3>'.$organization_name.'</h3>
	<h5>'.$organization_address.'</h5>
	<br/>&nbsp;<br/> <h3>'.ASSESSMENT_NAME.'</h3><br/><h4>Level: '.$level.'</h4><br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>'.date('d/m/Y').'</span>';
	$pdf->WriteHTML($htmlfirstpage, true, 0, true, 0);

	// add a page
	$pdf->AddPage();

	// set a bookmark for the current position
	$pdf->Bookmark('Certificate', 0, 0, '', 'B', array(0,64,128));

	$html_certificate = file_get_contents('certificate/certificate.html');
	// print a line using Cell()
	$pdf->Cell(0, 10, 'Certificate', 0, 1, 'L');
	$pdf->WriteHTML($html_certificate, true, 0, true, 0);

	$results_tmp_exists = mysql_query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '".$databaseName."' AND table_name = 'results_tmp';") or error($PN.'24');
	$row_tmp_exists = mysql_fetch_array($results_tmp_exists);
	if($row_tmp_exists[0] > 0)	
		mysql_query("DROP TABLE results_tmp;") or error($PN.'25');

	mysql_query("CREATE TABLE results_tmp AS SELECT E.id, F.chapter_id, F.rule_number, F.title, E.PassOrFail, E.comment FROM (SELECT C.id, C.rule_id, C.PassOrFail, C.comment FROM (SELECT A.id, A.rule_id, A.PassOrFail, A.comment, B.user_id FROM (SELECT report_rules.id, report_rules.assignment_id, report_rules.rule_id, report_rules.PassOrFail, report_rules.comment FROM report_rules WHERE report_rules.PassOrFail !=0 AND report_rules.assignment_id IN (SELECT id FROM assignment WHERE assessment_id=".$assessment_id.")) AS A left join (SELECT assignment.id, assignment.user_id FROM assignment WHERE assessment_id=".$assessment_id.") AS B on A.assignment_id=B.id) AS C left join (SELECT users.id FROM users) AS D on C.user_id=D.id) AS E left join (SELECT rules.id, rules.chapter_id, rules.rule_number, rules.title FROM rules) AS F on E.rule_id=F.id ORDER BY F.chapter_id, F.rule_number;") or error($PN.'17');
	
	$result_tmp_count = mysql_query("SELECT COUNT(*) FROM results_tmp;") or error($PN.'22');
	$row_tmp_count = mysql_fetch_array($result_tmp_count);
	if($row_tmp_count[0] == 0)
		error($PN.'23');
	
	$pdf->SetFont('dejavusans', '', 12);
	
	$result_chapter = mysql_query("SELECT id, chapter_name FROM chapters;") or error($PN.'18');
	while($row_chapter = mysql_fetch_array($result_chapter))
	{
		$pdf->SetFont('dejavusans', 'BI', 12);
		$chapter_id = $row_chapter[0];
		$chapter_name = $row_chapter[1];
		$result_count = mysql_query("SELECT COUNT(*) FROM results_tmp where chapter_id=".$chapter_id.";") or error($PN.'19');
		$row_count = mysql_fetch_array($result_count);
		if($row_count[0] > 0)
		{
			$pdf->AddPage();
			$pdf->Bookmark('Chapter '.$chapter_id.': '.$chapter_name, 0, 0, '', 'B', array(0,64,128));
			$pdf->Cell(0, 20, 'Chapter '.$chapter_id.': '.$chapter_name, 0, 1, 'L');

			$pdf->SetFont('dejavusans', '', 10);
			$htmlresults = '		
<style>
	table {
	    border-spacing: 0px;

		color: #000066;
		border-left: 0px solid #E4E9F9;
		border-right: 0px solid #E4E9F9;
		border-top: 3px solid red;
		border-bottom: 3px solid red;
		background-color: #E4E9F9;
		text-align: justify;
	}
	td.odd {padding: 10px;
		border-left: 2px solid red;
		border-right: 2px solid red;
		border-top: none;
		border-bottom: none;
		background-color: #E4E7FB;
	}
	td.even {padding: 10px;
		border-left: 2px solid red;
		border-right: 2px solid red;
		border-top: none;
		border-bottom: none;
		background-color: #F2F4FC;
	}
	tr {
		border-left: 3px solid red;
		border-right: 3px solid red;
		border-top: 3px solid red;
		border-bottom: 3px solid red;
	}
	tr.head td {
		border: 2px solid red;
		background-color: red;
		text-align: center;
		color: #ffffff;
		font-weight:bold;
		height:30px;
		vertical-align:middle;
	}
</style>
				
<table cellpadding="5">
    <tr class="head">
        <td width="35px">No.</td>
        <td width="357px">Title</td>
        <td width="247px">Comment</td>
    </tr>';
			$td_class = "odd";
			$image = "";
			$result = mysql_query("SELECT * FROM results_tmp where chapter_id=".$chapter_id.";") or error($PN.'20');
			while($row = mysql_fetch_array($result))
			{				
				if($row['PassOrFail'] == 1)
					$image = 'images/pass.png';
				else if($row['PassOrFail'] == 2)
					$image = 'images/fail.png';
				else {$image = "";}
				
				$htmlresults .= '
    <tr>
        <td class="'.$td_class.'">'.$row['rule_number'].'</td>
        <td class="'.$td_class.'"><img src="'.$image.'" style="width:20px;height:20px;"/>'.$row['title'].'</td>
        <td class="'.$td_class.'">'.$row['comment'].'</td>
    </tr>
';
				
				if($td_class=="odd")
					$td_class = "even";
				else
					$td_class ="odd";

			}
	
			$htmlresults .= '</table>';
			$pdf->WriteHTML($htmlresults, true, 0, true, 0);
		}
	}
	
	mysql_query("DROP TABLE results_tmp;") or error($PN.'21');

	// add a new page for TOC
	$pdf->addTOCPage();

	// write the TOC title
	$pdf->SetFont('times', 'B', 16);
	$pdf->MultiCell(0, 0, 'Table Of Content', 0, 'C', 0, 1, '', '', true, 0);
	$pdf->Ln();

	$pdf->SetFont('times', '', 12);

	$pdf->addTOC(2, 'times', '.', 'INDEX', 'B', array(128,0,0));

	// end of TOC page
	$pdf->endTOCPage();

	@mysql_close();
	
	//Close and output PDF document
	$pdf->Output(ASSESSMENT_NAME.'_Results.pdf', 'I');

?>
