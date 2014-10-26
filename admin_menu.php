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

	if(!isset($_SESSION['admin']))
		exit();
?>

<div id="menu">

	<ul>
		<li><a id="um" ng-click="user()" href="#menu-1">User Management</a></li>
		<li><a id="ar" ng-click="chapter()" href="#menu-2">ASVS Rules</a></li>
		<li><a id="at" ng-click="assessment()" href="#menu-3">Assessments</a></li>
		<li><a id="ma" ng-click="verification()" href="#menu-4">My Assignments</a></li>
		<li><a id="rt" ng-click="Report()" href="#menu-5">Report</a></li>
	</ul>
	
	<div id="menu-1">
		<p>
			<div class="well">
			
				<div id="list-uname" style="width: 355px;">
					&nbsp;<select data-placeholder="Choose a UserName ..." id="chosen-select-uname" style="width:350px;" tabindex="-1"></select>
				</div>
				<br/>

				<div id="back_button" style="width: 32px;" class="hide">
					<img class="img_icon" src="images/back.png" ng-click="user_management()" title="Back To User Management">
				</div>

				<div id="UserTable" style="width: 1015px;"></div>
				<div id="LogTable" class="hide" style="width: 1015px;"></div>
				
				<div id="change_user_password" class="hide" title="Edit User Password">
					<p>
						<form class="jtable-dialog-form jtable-edit-form"><div class="jtable-input-field-container"><div class="jtable-input-label">Password</div><div class="jtable-input jtable-password-input"><input maxlength="40" id="Edit2-password" name="password" type="password"></div></div><div class="jtable-input-field-container"><div class="jtable-input-label">Confirm Password</div><div class="jtable-input jtable-password-input"><input maxlength="40" id="Edit2-password2" name="password2" type="password"></div></div></form>
					</p>
				</div>

			</div>
		</p>
	</div>

	<div id="menu-2">
		<p>
			<div id="rules-methodology" class="well">
				<label for="rules-select">
					<input type="radio" id="rules-select" name="rules-or-methodology"> ASVS Rules
				</label>
				&nbsp;&nbsp;
				<label for="rules-select">
					<input type="radio" id="methodology-select" name="rules-or-methodology"> Methodology
				</label>
			</div>

			<div class="well">
				<div id="list-chapter" style="width: 355px;">
					&nbsp;<select data-placeholder="Choose a Chapter ..." id="chosen-select-chapter" style="width:350px;" tabindex="-1"></select> 
				</div>
				
				<br/><br/>
				<div id="Rule-MethodologyTable" style="width: 1015px;"></div>
			</div>
		</p>

	</div>

	<div id="menu-3">
		<p>
			<div class="well">

				<div style="width: 1015px; min-height:310px; display: table; overflow: auto;">
					<div style="width: 367px; float: left; display: table-cell;">
						<div class="well" style="width: 360px;">
							<div id="assessment-or-assignment">
								<label for="assessment-select">
									<input type="radio" id="assessment-select" name="assessment-or-assignment"> Assessment
								</label>
								&nbsp;&nbsp;
								<label for="assignment-select">
									<input type="radio" id="assignment-select" name="assessment-or-assignment"> Assignment
								</label>
								&nbsp;&nbsp;
								<label for="assignment-select_show_results">
									<input type="radio" id="assignment-select_show_results" name="assessment-or-assignment"> Show Results
								</label>
							</div>
							<br/>
							<div id="select-assessment-all" style="width: 355px;">
								&nbsp;<select data-placeholder="Choose an Assessment ..." id="chosen-select-assessment-all" style="width:350px;" tabindex="-1"></select> 
							</div>
						</div>

						<div class="well hide" style="width: 360px;" id="assignment_save_position">
							<div id="list-uname-assignment" style="width: 355px;">
								&nbsp;<select data-placeholder="Choose a UserName ..." id="chosen-select-uname-assignment" style="width:350px;" tabindex="-1"></select> 
							</div>
							<br/>
							<div id="list-chapter-assignment" style="width: 355px;">
								&nbsp;<select data-placeholder="Choose Chapters ..." id="chosen-select-chapter-assignment" multiple="" style="width: 350px;" tabindex="-1"></select> 
							</div>
							<br/>
							<div id="admin_comment">
								<label for="new_assignment_comment">
									&nbsp;<input type="checkbox" id="new_assignment_comment">&nbsp; Change Comment
								</label>
								<div id="admin_comment_wizard_wait" class="hide">
									<img id="wait_image" src="images/wait.gif">
								</div>
								<div id="admin_comment_wizard" class="hide">
									<br/>
									&nbsp;<textarea id="admin_comment_wizard_textbox" style="width: 340px; height: 150px;"></textarea>
								</div>
							</div>
							<br/>
							<div id="assignment_create_new">
								&nbsp;&nbsp;<button class="btn-large btn-primary" ng-click="assignment_save()">New Assignment</button>
							</div>
						</div>

						<div class="well hide" style="width: 360px;" id="assignment_show_results">
							<div id="list-uname-assignment_show_results" style="width: 355px;">
								&nbsp;<select data-placeholder="Choose UserName ..." id="chosen-select-uname-assignment_show_results" multiple="" style="width:350px;" tabindex="-1"></select> 
							</div>
							<br/>
							<div id="list-chapter-assignment_show_results" style="width: 355px;">
								&nbsp;<select data-placeholder="Choose Chapters ..." id="chosen-select-chapter-assignment_show_results" multiple="" style="width: 350px;" tabindex="-1"></select> 
							</div>
							<br/>
							<div id="show_results">
								&nbsp;&nbsp;<button class="btn-large btn-primary" ng-click="results_show_all()">Show Results</button>
							</div>

						</div>
					</div>

					<div class="well" id="user_assignment_results_show"  style="width: 543px; float: right; display: table-cell; margin-right:8px">
						<div class="well hide" id="user_assignment_show"></div>
						<div class="well hide" id="user_chapter_show"></div>

						<div class="well hide" id="show_results_user_name"></div>
						<div class="well hide" id="show_results_user_comment"></div>
					</div>

				</div>

				<div id="AssessmentTable" style="width: 1015px;"></div>
				<div id="AssignmentTable" style="width: 1015px;"></div>
				<br/>
				<br/>
				<div id="AssignmentTable-results-show" class="hide" style="width: 1015px;"></div>
			</div>
		</p>
	</div>

	<div id="menu-4">
		<p>
			<div class="well">

				<div id="assessment-type" class="well" style="width: 374px;">
					&nbsp;&nbsp;<input type="radio" id="list-assessment-all" name="assessment-type"> All
					&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="list-assessment-new" name="assessment-type"> New
					&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="list-assessment-complete" name="assessment-type"> UnCompleted
					&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="list-assessment-uncompleted" name="assessment-type"> Completed
				</div>

				<div style="width: 1015px; min-height:310px; display: table; overflow: auto;">	
					<div class="well" style="width: 374px; float: left; display: table-cell;">
						<div id="list-assessment" style="width: 355px;">
							&nbsp;<select data-placeholder="Choose an Assessment ..." id="chosen-select-assessment" style="width:350px;" tabindex="-1"></select> 
						</div>
						<br/>
						<div id="list-assessment-chapter" style="width: 355px;">
							&nbsp;<select data-placeholder="Choose a Chapter ..." id="chosen-select-assessment-chapter" style="width:350px;" tabindex="-1"></select> 
						</div>
						<br/>
						<div class="well">
							<div id="verification-type" style="width: 315px;">
								&nbsp;<input type="radio" id="verification-type-list" name="verification-type"> Table
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="verification-type-wizard" name="verification-type"> Wizard
							</div>
							<br/>

							<div id="user_comment_change">
								<label for="verification_user_checkbox">
									&nbsp;<input type="checkbox" id="verification_user_checkbox">&nbsp; Change Comment
								</label>
								<div id="user_comment_wait" class="hide">
									<img id="wait_image" src="images/wait.gif">
								</div>

								<div id="verification_user_comment" class="hide">
									&nbsp;<textarea id="verification_user_comment_textbox" style="width: 320px; height: 150px;"></textarea>
								</div>
							</div>

							<br/>
							<div id="list-verification-Type">
								&nbsp;&nbsp;<button class="btn-large btn-primary" ng-click="verification_start()">Start Verification</button>
							</div>	
						</div>
					</div>

					<div id="comment_position" style="width: 543px; float: right; display: table-cell;" class="well hide"></div>
				</div>


				<div id="VerificationTable" style="width: 1015px;"></div>

			</div>
		</p>
	</div>

	<div id="menu-5">
		<p>
			<div class="well">
				<div id="report-main">
					<br/>
					<div id="AllReportTable" style="width: 1000px;"></div>

					<br/><br/><br/>
					<div id="report_show_results" style="width: 1000px;"></div>
				</div>
				
				<div id="report_create" class="hide">
					<div class="well" style="width: 360px;">
						<div id="list-assessment-report" style="width: 355px;">
							&nbsp;<select data-placeholder="Choose an Assessment ..." id="chosen-select-assessment-report" style="width:350px;" tabindex="-1"></select> 
						</div>
						<br/>
						<div id="list-uname-report" style="width: 355px;">
							&nbsp;<select data-placeholder="Choose UserName ..." id="chosen-select-uname-report" multiple="" style="width:350px;" tabindex="-1"></select> 
						</div>
						<br/>
						<div id="list-chapter-report" style="width: 355px;">
							&nbsp;<select data-placeholder="Choose Chapter ..." id="chosen-select-chapter-report" multiple="" style="width:350px;" tabindex="-1"></select> 
						</div>
					</div>
					<div class="well">
						<div id="report_Save_Cancel_button">
							&nbsp;&nbsp;<button class="btn-large btn-primary" ng-click="report_create_cancel()">Cancel</button>
							&nbsp;&nbsp;<button class="btn-large btn-primary" ng-click="report_update()">Save</button>
						</div>
						<br/><br/>
						<div id="AllAssignmentTable-show" style="width: 980px;"></div>
					</div>
				</div>

				<!--<div id="report_download" class="hide">
					<table align="center">

						<tr>
							<td>
								Certificate: &nbsp;<br/>&nbsp;
							</td>
							<td>
								<textarea id="db"></textarea>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<br/><button class="btn-large btn-primary"  ng-click="back(1)">&nbsp;Download&nbsp;</button>
							</td>
						</tr>
				
					</table>

				</div>-->

			</div>
		</p>
	</div>
	
</div>