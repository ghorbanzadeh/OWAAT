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

	if(!isset($_SESSION['user']))
		exit();
?>

<div id="menu">

	<ul>
		<li><a id="um" ng-click="user()" href="#menu-1">User Management</a></li>
		<li><a id="ma" ng-click="verification()" href="#menu-4">My Assignments</a></li>
	</ul>
	
	<div id="menu-1">
		<p>
			<div class="well">

				<div id="UserTable" style="width: 1015px;"></div>
				
				<div id="change_user_password" class="hide" title="Edit User Password">
					<p>
						<form class="jtable-dialog-form jtable-edit-form"><div class="jtable-input-field-container"><div class="jtable-input-label">Password</div><div class="jtable-input jtable-password-input"><input maxlength="40" id="Edit2-password" name="password" type="password"></div></div><div class="jtable-input-field-container"><div class="jtable-input-label">Confirm Password</div><div class="jtable-input jtable-password-input"><input maxlength="40" id="Edit2-password2" name="password2" type="password"></div></div></form>
					</p>
				</div>

			</div>
		</p>
	</div>

	<div id="menu-4">
		<p>
			<div  class="well">

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
</div>