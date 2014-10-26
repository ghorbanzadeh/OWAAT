<!--
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

-->

<!DOCTYPE html>
<html lang="en" ng-app="asvs_install" xmlns="http://www.w3.org/1999/html">
	<head>

		<link rel="shortcut icon" href="../images/icon.ico" />

		<script src="../js/jquery-1.7.2.js"></script>

		<meta charset="utf-8">
		<title>OWASP ASVS Assessment Tool (OWAAT)</title>
		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet" href="../css/bootstrapcdn/twitter-bootstrap/2.0.4/css/bootstrap-combined.min.css"/>
		<link rel="stylesheet" href="../css/googlapis/ajax/libs/jqueryui/1.8.22/themes/blitzer/jquery-ui.css"/>

		<link rel="stylesheet" href="upload/css/jquery.fileupload.css">   
   
		<style>
		
			.install {
				text-align: center;
			}

			footer {
				font-size: small;
				text-align: center;
			}

			td {
				text-align: left;
			}
			
			td.button_td {
				text-align: center;
			}
		
		</style>

	</head>

	<body ng-controller="Installer_WizardController">
		
		<div id="server_message_shown" class="hide" title="Error">
			<p>
				<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
				<div id="server_message"></div>
			</p>
		</div>

		<br/><br/>

		<section id="security_code" class="hero-unit container install">
			<h2>The Installer Of OWASP ASVS Assessment Tool</h2>
			<hr/>
	
			<div>
				<h3>
					<table align="center">

						<tr>
							<td>
								Security Code: &nbsp;
							</td>
							<td>
								<input type="password" id="sc">
							</td>
						</tr>
				
						<tr>
							<td class="button_td" colspan="2">
								<br/><button class="btn-large btn-primary"  ng-click="next()">&nbsp;Next&nbsp;</button>
							</td>
						</tr>
				
					</table>
				</h3>
			<br/><br/>
			</div>
	
		</section>

		<section id="install" class="hero-unit container hide install">
			<h2>The Installer Of OWASP ASVS Assessment Tool</h2>
			<hr/>
	
			<div>
				<h3>
					<table align="center">
			
						<tr>
							<td>
								Host Name: &nbsp;
							</td>
							<td>
								<input type="text" id="host">
							</td>
						</tr>
				
						<tr>
							<td>
								User Name: &nbsp;
							</td>
							<td>
								<input type="text" id="un">
							</td>
						</tr>
				
						<tr>
							<td>
								Password: &nbsp;
							</td>
							<td>
								<input type="password" id="password">
							</td>
						</tr>

						<tr>
							<td>
								Database Name: &nbsp;
							</td>
							<td>
								<input type="text" id="db">
							</td>
						</tr>

						<tr>
							<td class="button_td" colspan="2">
								<br/><button class="btn-large btn-primary"  ng-click="back(1)">&nbsp;Back&nbsp;</button>
									&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="install()">&nbsp;Next&nbsp;</button>
									&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="skip(1)">&nbsp;Skip&nbsp;</button>
							</td>
						</tr>
				
					</table>
				</h3>
			<br/><br/>
			</div>
	
		</section>

		<section id="install-certificate" class="hero-unit container hide install">
			<h2>The Installer Of OWASP ASVS Assessment Tool</h2>
			<hr/>

			<div>
				<h3>
					<table align="center">

						<tr>
							<td>
								Organization Name: &nbsp;
							</td>
							<td>
								<input type="text" id="organization_name">
							</td>
						</tr>

						<tr>
							<td>
								Organization Address: &nbsp;
							</td>
							<td>
								<input type="text" id="organization_address">
							</td>
						</tr>

						<tr>
							<td>
								Logo (128X128): &nbsp;
							</td>
							<td>
								<div>
									<span class="btn btn-success fileinput-button">
										<i class="glyphicon glyphicon-plus"></i>
										<span>Select file...</span>
										<input id="fileupload" type="file" name="files[]" multiple>
									</span>
									<br>
									<div id="files" class="files"></div>
								</div>
							</td>
						</tr>

						<tr>
							<td class="button_td" colspan="2">
								<br/><button class="btn-large btn-primary"  ng-click="back(2)">&nbsp;Back&nbsp;</button>
									&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="config()">&nbsp;Next&nbsp;</button>
									&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="skip(2)">&nbsp;Skip&nbsp;</button>
							</td>
						</tr>

					</table>
				</h3>
			<br/><br/>
			</div>
	
		</section>
		
		<section id="install-user" class="hero-unit container hide install">
			<h2>The Installer Of OWASP ASVS Assessment Tool</h2>
			<hr/>
				
			<h3>Create Master User</h3>
			<br/>
			<div>
				<h3>
					<table align="center">

						<tr>
							<td>
								First Name: &nbsp;
							</td>
							<td>
								<input type="text" id="first_name" maxlength="30">
							</td>
						</tr>
						
						<tr>
							<td>
								Last Name: &nbsp;
							</td>
							<td>
								<input type="text" id="last_name" maxlength="30">
							</td>
						</tr>
						
						<tr>
							<td>
								Email: &nbsp;
							</td>
							<td>
								<input type="text" id="email" maxlength="30">
							</td>
						</tr>
						
						<tr>
							<td>
								User Name: &nbsp;
							</td>
							<td>
								<input type="text" id="user_name" maxlength="30">
							</td>
						</tr>
				
						<tr>
							<td>
								Password: &nbsp;
							</td>
							<td>
								<input type="password" id="user_password" maxlength="40">
							</td>
						</tr>
				
						<tr>
							<td>
								Confirm Password: &nbsp;
							</td>
							<td>
								<input type="password" id="user_password2">
							</td>
						</tr>
				
						<tr>
							<td class="button_td" colspan="2">
								<br/><button class="btn-large btn-primary"  ng-click="back(3)">&nbsp;Back&nbsp;</button>
								&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="create()">&nbsp;Next&nbsp;</button>
								&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="skip(3)">&nbsp;Skip&nbsp;</button>
							</td>
						</tr>
				
					</table>
				</h3>
			<br/><br/>
			</div>
	
		</section>
		
		<section id="install-delete" class="hero-unit container hide install">
			<h2>The Installer Of OWASP ASVS Assessment Tool</h2>
			<hr/>
			
			<h3>Do You Want To Remove The Installation Directory?</h3>
			<br/>
			<div>
				<h3>
					<table align="center">

						<tr>
							<td  class="button_td" colspan="2">
								<br/><button class="btn-large btn-primary"  ng-click="back(4)">&nbsp;Back&nbsp;</button>
								&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="delete_dir(true)">&nbsp;Yes&nbsp;</button>
								&nbsp;&nbsp;&nbsp;<button class="btn-large btn-primary"  ng-click="delete_dir(false)">&nbsp;No&nbsp;</button>
							</td>
						</tr>

					</table>
				</h3>
			<br/><br/>
			</div>
	
		</section>

		<hr/>

		<?php
			include '../footer.html';
		?>


		<script src="../js/googleapis/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js"></script>

		<script src="../js/googleapis/ajax/libs/angularjs/1.0.1/angular.min.js"></script>

		<script src="upload/js/vendor/jquery.ui.widget.js"></script>
		<script src="upload/js/jquery.iframe-transport.js"></script>
		<script src="upload/js/jquery.fileupload.js"></script>

		<script>

			var logo_name = '';

			function server_message(message, notice)
			{
				var message_shown = '';
		
				if((typeof notice === 'undefined'))
					$('#server_message_shown').attr('title', 'Error');
				else
					$('#server_message_shown').attr('title', 'Message');
		
				if((typeof message === 'undefined'))
					message_shown = "An Unknown Error Has Been Occurred.";
				else
					message_shown = message;

				$("#server_message").text(message_shown);
		
				$("#server_message_shown").dialog({
					resizable: false,
					modal: true,
					buttons: {
						Ok: function() {
							$( this ).dialog( "close" );
						}
					}
				});
			};

			$(function () {
			   'use strict';
				var url = '../upload.php';
				$('#fileupload').fileupload({
					url: url,
					dataType: 'json',
					done: function (e, data) {
						$.each(data.result.files, function (index, file) {
							if(typeof file.error === 'undefined')
							{
								$('<p/>').text(file.name).appendTo('#files');
								logo_name = file.name;
							}
							else
							{
								server_message(file.error);
							}            
						});
					}
				})
			});

			angular.module('asvs_install', []).controller('Installer_WizardController', function ($scope, $http) {
			"use strict";

				$("#sc").focus();
				
				$('#sc').keyup(function(e){
					if(e.keyCode == 13)
					{
						$scope.next();
					}
				});

				$('#host').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#un").focus();
					}
				});
				
				$('#un').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#password").focus();
					}
				});

				$('#password').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#db").focus();
					}
				});
				
				$('#db').keyup(function(e){
					if(e.keyCode == 13)
					{
						$scope.install();
					}
				});

				$('#organization_name').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#organization_address").focus();
					}
				});

				$('#first_name').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#last_name").focus();
					}
				});
				
				$('#last_name').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#email").focus();
					}
				});
				
				$('#email').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#user_name").focus();
					}
				});
				
				$('#user_name').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#user_password").focus();
					}
				});
				
				$('#user_password').keyup(function(e){
					if(e.keyCode == 13)
					{
						$("#user_password2").focus();
					}
				});
				
				$('#user_password2').keyup(function(e){
					if(e.keyCode == 13)
					{
						$scope.create();
					}
				});

				$scope.back = function(x) {
					if(x===1)
					{
						$('#install').hide();
						$('#security_code').show();
						$("#sc").focus();
					}
					else if (x===2)
					{
						$('#install-certificate').hide();
						$('#install').show();
						$("#host").focus();
					}
					else if (x===3)
					{
						$('#install-user').hide();
						$('#install-certificate').show();
						$("#organization_name").focus();
					}
					else
					{
						$('#install-delete').hide();
						$('#install-user').show();
						$("#first_name").focus();
					}
				};

				$scope.next = function() {
					if($("#sc").val() == '')
					{
						server_message("The Security Code Is Not Valid, Please Check The 'install\\sc.php' File!");
						return false;
					}
					else
					{

						$.post("install.php?action=security_code",
						{
							sc:$('#sc').val()
						},
						function(data,status){
							var data2;
							if(!(typeof data === 'undefined'))
							{
								data2 = jQuery.parseJSON(data);
								if("OK".localeCompare(data2.Result) == 0)
								{
									var now = new Date();
									var time = now.getTime();
									var expireTime = time + 20*36000;
									now.setTime(expireTime);
									document.cookie = 'ASVS_Tool_SC='+$('#sc').val()+';expires='+now.toGMTString()+';path=/';

									$('#security_code').hide();					
									$('#install').show();
									$("#host").focus();
								}
								else
								{
									server_message(data2.Message, 3);
									$('#sc').val('');
								}
							}
							else
								server_message('', 3);
						});					

					}
				};

				$scope.skip = function(x) {
					if(x===1)
					{
						$('#install').hide();
						$('#install-certificate').show();	
						$("#organization_name").focus();
					}
					else if(x===2)
					{
						$('#install-certificate').hide();
						$('#install-user').show();	
						$("#first_name").focus();						
					}
					else
					{
						$('#install-user').hide();
						$('#install-delete').show();					
					}
				};

				$scope.install = function() {

					$.post('install.php?action=create_table',
					{
						host:$("#host").val(),
						un:$("#un").val(),
						password:$("#password").val(),
						db:$("#db").val(),
						sc:$("#sc").val()
					},
					function(data,status){
						var data2;
						if(!(typeof data === 'undefined'))
						{
							data2 = jQuery.parseJSON(data);
							if("OK".localeCompare(data2.Result) == 0)
							{
								$('#install').hide();
								$('#install-certificate').show();	
								$("#organization_name").focus();
							}
							else
								server_message(data2.Message, 3);
						}
						else
							server_message('', 3);
					});				

				};

				$scope.config = function() {

					$.post('install.php?action=config',
					{
						organization_name:$("#organization_name").val(),
						organization_address:$("#organization_address").val(),
						logo:logo_name,
						sc:$("#sc").val()
					},
					function(data,status){
						var data2;
						if(!(typeof data === 'undefined'))
						{
							data2 = jQuery.parseJSON(data);
							if("OK".localeCompare(data2.Result) == 0)
							{
								$('#install-certificate').hide();				
								$('#install-user').show();
								$("#first_name").focus();
							}
							else
								server_message(data2.Message, 3);
						}
						else
							server_message('', 3);
					});				

				};
				
				$scope.create = function() {
					if($("#user_password").val() == $("#user_password2").val())
					{
						$.post('install.php?action=create_user',
						{
							fname:$("#first_name").val(),
							lname:$("#last_name").val(),
							email:$("#email").val(),
							uname:$("#user_name").val(),
							password:$("#user_password").val(),
							sc:$("#sc").val()
						},
						function(data,status){
							var data2;
							if(!(typeof data === 'undefined'))
							{
								data2 = jQuery.parseJSON(data);
								if("OK".localeCompare(data2.Result) == 0)
								{
									$('#install-user').hide();
									$('#install-delete').show();
								}
								else
									server_message(data2.Message, 3);
							}
							else
								server_message('', 3);
						});

					}
					else
						server_message('The Password And Confirmation Password Do Not Match!');
				};

				$scope.delete_dir = function(yes) {
					if(yes == true)
					{
						$.post('install.php?action=delete',
						{
							sc:$("#sc").val()
						},
						function(data,status){
							var data2;
							if(!(typeof data === 'undefined'))
							{
								data2 = jQuery.parseJSON(data);
								if("OK".localeCompare(data2.Result) == 0)
								{
									window.location.assign('../index.php');
								}
								else
									server_message(data2.Message, 3);
							}
							else
								server_message('', 3);
						});					

					}
					else
						window.location.assign('../index.php');
				};
			});

		</script>
	</body>
</html>