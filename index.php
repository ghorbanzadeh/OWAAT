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
	header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en" ng-app="asvs" xmlns="http://www.w3.org/1999/html">
	<head>

		<link rel="shortcut icon" href="images/icon.ico" />

		<link href="intro/introjs.css" rel="stylesheet">
		<script type="text/javascript" src="intro/intro.js"></script>

		<script src="js/jquery-1.7.2.js"></script>

		<link href="icheck/skins/square/blue.css" rel="stylesheet">
		<script src="icheck/icheck.js?v=1.0.2"></script>
		
		<link href="jtable/themes/lightcolor/blue/jtable.css" rel="stylesheet" type="text/css" />
		<script src="js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
		<script src="jtable/jquery.jtable.js" type="text/javascript"></script>

		<link type="text/css" rel="stylesheet" href="notify/style.css" />
		<link type="text/css" rel="stylesheet" href="notify/ui.notify.css" />
		<script src="notify/jquery.notify.js" type="text/javascript"></script>

		<link rel="stylesheet" href="chosen/style.css">
		<link rel="stylesheet" href="chosen/chosen.css">
		<script src="chosen/chosen.jquery.js" type="text/javascript"></script>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>OWASP ASVS Assessment Tool (OWAAT)</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet" href="css/bootstrapcdn/twitter-bootstrap/2.0.4/css/bootstrap-combined.min.css"/>
		<link rel="stylesheet" href="css/googlapis/ajax/libs/jqueryui/1.8.22/themes/blitzer/jquery-ui.css"/>

		<style>
			body {
				font-family: "Arial";
			}

			#welcome {
				width: 1100px;
			}

			footer {
				font-size: small;
				text-align: center;
			}

			td th {
				text-align: left;
			}

			.img_icon
			{
				width: 32px;
				height: 32px;
				cursor: pointer;
			}
			
			#admin_comment_wizard_wait
			{
				text-align: center;
			}
			
			#wait_image
			{
				width: 30px;
				height: 30px;
			}
			
			#comment_position222
			{
				position:absolute;
				left:460px;
				right:50px;
				top:168px;
			}
			#assignment_save_position2222
			{
				position:relative;
				left:420px;
				top:-180px;
			}
			.page_size_select
			{
				width: 60px;
			}
			.goto_page_select
			{
				width: 50px;
			}
			
			#user-welcome{min-height:18px;padding:12px;margin-bottom:5px;background-color:#f5f5f5;border:1px solid #eee;border:1px solid rgba(0,0,0,0.05);-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,0.05);-moz-box-shadow:inset 0 1px 1px rgba(0,0,0,0.05);box-shadow:inset 0 1px 1px rgba(0,0,0,0.05)}
		
			#admin_comment_wizard_textbox
			{
				resize: none;
			}

			#verification_user_comment_textbox
			{
				resize: none;
			}
			
			#container_notify
			{
				z-index:3000;
			}
			
			#container_help
			{
				z-index:2000;
			}
		</style>

		<script>
			$(function() {
				$('#menu').tabs();
				$('#menu').tabs('select', '#menu-4');
			});
		</script>


	</head>

	<body ng-controller="WizardController">

		<br/>
		<br/>

		<div id="container_notify" style="display:none">

			<div id="message_notify">
				<a class="ui-notify-close ui-notify-cross" href="#">X</a>
				<div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" /></div>
				<h1>#{title}</h1>
				<p>#{text}</p>
			</div>

		</div>

		<?php if(isset($_SESSION['user'])){ ?>
			<div id="container_help" style="display:none; top:auto; left:0; bottom:0; margin:0 0 10px 10px">
				<div id="help_continue">
					<?php if(isset($_SESSION['admin'])){ ?>
						<div id="help0" class="hide" title="Help - User Management">
							<p>
								<div>
									<label for="help01">
										<input type="radio" id="help01" name="help0" value="01" checked> Add New User
									</label>
									<label for="help02">
										<input type="radio" id="help02" name="help0" value="02"> Change A User Password
									</label>
									<label for="help03">
										<input type="radio" id="help03" name="help0" value="03"> Edit A User Profile
									</label>
									<label for="help04">
										<input type="radio" id="help04" name="help0" value="04"> Delete A User
									</label>
									<label for="help05">
										<input type="radio" id="help05" name="help0" value="05"> Show A User Logs
									</label>
									<?php if($_SESSION['id'] == 1){ ?>
										<label for="help06">
											<input type="radio" id="help06" name="help0" value="06"> Delete A User Logs
										</label>
									<?php } ?>
								</div>
							</p>
						</div>
					<?php }else{ ?>
						<div id="help0" class="hide" title="Help - User Management">
							<p>
								<div>
									<label for="help02">
										<input type="radio" id="help02" name="help0" value="02" checked> Change Your Password
									</label>
									<label for="help03">
										<input type="radio" id="help03" name="help0" value="03"> Edit Your Profile
									</label>
								</div>
							</p>
						</div>
					<?php } if(isset($_SESSION['admin'])){ ?>
						<div id="help1" class="hide" title="Help - ASVS Rules">
							<p>
								<div>
									<label for="help11">
										<input type="radio" id="help11" name="help1" value="11" checked> Add New Rule
									</label>
									<label for="help12">
										<input type="radio" id="help12" name="help1" value="12"> Edit A Rule
									</label>
									<label for="help13">
										<input type="radio" id="help13" name="help1" value="13"> Delete A Rule
									</label>
									<label for="help14">
										<input type="radio" id="help14" name="help1" value="14"> Edit Methodology
									</label>
								</div>
							</p>
						</div>

						<div id="help2" class="hide" title="Help - Assessments">
							<p>
								<div>
									<label for="help21">
										<input type="radio" id="help21" name="help2" value="21" checked> Add New Assessment
									</label>
									<label for="help22">
										<input type="radio" id="help22" name="help2" value="22"> Edit An Assessment
									</label>
									<label for="help23">
										<input type="radio" id="help23" name="help2" value="23"> Delete An Assessment
									</label>
									<label for="help24">
										<input type="radio" id="help24" name="help2" value="24"> Add An Assignment
									</label>
									<label for="help25">
										<input type="radio" id="help25" name="help2" value="25"> Delete An Assignment
									</label>
									<label for="help26">
										<input type="radio" id="help26" name="help2" value="26"> Show Results
									</label>
								</div>
							</p>
						</div>
					<?php } ?>
						<div id="help3" class="hide" title="Help - My Assignments">
							<p>
								<div>
									<label for="help31">
										<input type="radio" id="help31" name="help3" value="31" checked> Rule Verification
									</label>
									<label for="help32">
										<input type="radio" id="help32" name="help3" value="32"> Add A Comment
									</label>
									<label for="help33">
										<input type="radio" id="help33" name="help3" value="33"> Delete Review
									</label>
								</div>
							</p>
						</div>
					<?php if(isset($_SESSION['admin'])){ ?>
						<div id="help4" class="hide" title="Help - Report">
							<p>
								<div>
									<label for="help41">
										<input type="radio" id="help41" name="help4" value="41" checked> Create New Report
									</label>
									<label for="help42">
										<input type="radio" id="help42" name="help4" value="42"> Edit Report
									</label>
									<label for="help43">
										<input type="radio" id="help43" name="help4" value="43"> Edit Review
									</label>
									<label for="help44">
										<input type="radio" id="help44" name="help4" value="44"> Download Report
									</label>
									<label for="help45">
										<input type="radio" id="help45" name="help4" value="45"> Delete Report
									</label>
								</div>
							</p>
						</div>
					<?php } ?>
					<p style="margin-top:10px;text-align:center">
						<button class="btn btn-primary ui-notify-close help_cancel">&nbsp;Cancel&nbsp;</button>
						&nbsp;&nbsp;&nbsp;<button class="btn btn-primary help_class">&nbsp;&nbsp;Help&nbsp;&nbsp;</button>
					</p>
				</div>
			</div>
		<?php } ?>

		<section id="welcome" class="hero-unit container hide">
			<div id="welcome2" class="hide">

			<div id="user-welcome">
				<div style="float: left; color: #0000B2;">
					<h4>Hello <?php echo @$_SESSION['user']; ?></h4>
				</div>
				<div style="float: right;">
					<img class="img_icon" src="images/help.png" title="Help" ng-click="help()">
					&nbsp;<img class="img_icon" src="images/exit.png" title="Logout" ng-click="logout()">
				</div>
			</div>

			<?php

				if(isset($_SESSION['admin']))
					include 'admin_menu.php'; 
				else if(isset($_SESSION['user']))
					include 'user_menu.php';
				else 
				{
					include 'function.php';
					$_SESSION['token'] = mt_rand_str(30);
				}

				//CSRF Protection
				echo '<input type="hidden" id="token" value="'.@$_SESSION['token'].'">';
			?>

			</div>

			<div id="server_message_shown" class="hide" title="Error">
				<p>
					<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
					<div id="server_message"></div>
				</p>
			</div>

			<div id="login" class="hide formholder ">
				<input type="text" id="uname" placeholder="User Name">	
				<br/>
				<input type="password" id="password" placeholder="Password">
			<br/><br/>
			<button class="btn-large btn-primary"  ng-click="login()">Login</button></center>
			</div>
			
		</section>

		<section id="wizard" class="hide">
			<header>
				<div class="progress progress-danger">
					<div class="bar" style="width: {{edittedPercentage()}}%;"></div>
				</div>
			</header>

			<section class="screen form-inline" ng-repeat="rule in report.rules" ng-class="rule.showEdit">
				<button class="btn" ng-click="prev()" ng-class="edittedIsNotFirst()">
					<i class="icon-arrow-left"></i>
					Previous
				</button>

				<button class="btn btn-primary" ng-click="next(rule)" ng-class="edittedIsNotLast()">
					Next
					<i class="icon-arrow-right icon-white"></i>
				</button>
				<button class="btn btn-primary" ng-class="edittedIsLast()" ng-click="done(rule)">Close</button>
				<button class="btn btn-danger" ng-click="next2(rule)" ng-class="edittedIsNotLast()">Save &amp; Next</button>
				<button class="btn btn-danger" ng-click="done2(rule)" ng-class="edittedIsLast()">Save &amp; Close</button>
				<br style="clear: both"/>
				<br style="clear: both"/>

				<header>
					<h1>V{{rule.chapter_id}} - {{getChapterTitle(rule.chapter_id)}}</h1>

					<h2>V{{rule.chapter_id}}.{{rule.rule_number}} - {{rule.title}} (Level: {{rule.level}})</h2>
				</header>
				<div class="well">
					<p>How To Verify?</p>
					<p>{{rule.methodology}}</p>
				</div>

				<div class="well">
					<p>Pass/Fail</p>
					<p>
						<label for="statusNone">
							<input ng-checked="rule.PassOrFail == 0" type="radio" id="statusNone" value="0" ng-click="none(rule)"/>
							None
						</label>
						<br/>
						<label for="statusPass">
							<input ng-checked="rule.PassOrFail == 1" type="radio" id="statusPass" value="1" ng-click="pass(rule)"/>
							Passed
						</label>
						<br/>
						<label for="statusFail">
							<input ng-checked="rule.PassOrFail == 2" type="radio" id="statusFail" value="2" ng-click="fail(rule)"/>
							Failed
						</label>
					</p>
				</div>

				<hr/>

				<section class="comment">
					<p ng-class="rule.showNone">Comment:</p>
					<p ng-class="rule.showPass">How has this been verified?</p>
					<p ng-class="rule.showFail">Why can this not be verified?</p>
					<textarea id="textbox_comment" class="span8"
							  cols="80"
							  rows="5" ng-model="rule.comment"></textarea>		  
				</section>

			</section>

		</section>

		<hr/>

		<script src="js/googleapis/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js"></script>

		<script src="js/googleapis/ajax/libs/angularjs/1.0.1/angular.min.js"></script>

		<?php
			include 'footer.html';

			if(isset($_SESSION['admin']))
				echo "<script src=\"asvs-admin.js\"></script>";
			else if(isset($_SESSION['user']))
				echo "<script src=\"asvs-user.js\"></script>";
			else
				echo "<script src=\"asvs.js\"></script>";
		?>

		<script>

			<?php if(!isset($_SESSION['user'])){ ?>
			   $('#welcome2').hide();
			   $('#login').show();
			<?php } else{ ?>
			   $('#welcome2').show();
			   $('#login').hide();
			<?php } ?>

			$('#welcome').show();
			
		</script>
	</body>
</html>