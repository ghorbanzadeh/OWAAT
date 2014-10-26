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

angular.module('asvs', []).
    controller('WizardController', function ($scope, $http) {
        "use strict";	

		///////////////////////////users///////////////////////////////////////
		var user_run = false;
		$scope.user = function() {
			if(user_run === false)
			{
				user_table();
				user_run = true;
			}
		};

		function user_table()
		{
			var messages_new = {
				editRecord: 'Edit The Profile'
			}

			$('#UserTable').jtable('destroy');
			$('#UserTable').jtable({
				messages: messages_new,
				title: 'User Profile',
				actions: {
					listAction: 'user-profile.php?action=list&select='+$("#chosen-select-uname").val(),
					updateAction: 'user-profile.php?action=update&token='+$("#token").val()
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false
					},
					uname: {
						title: 'Uname',
						edit: false,
						width: '20%'
					},
					fname: {
						title: 'Fname',
						width: '20%'
					},
					lname: {
						title: 'Lname',
						width: '22%'
					},
					email: {
						title: 'Email',
						width: '22%'
					},
					password: {
						title: 'Password',
						list: false,
						edit: false,
						type: 'password'
					},
					password2: {
						title: 'Confirm Password',
						list: false,
						edit: false,
						type: 'password'
					},
					administrator: {
						title: 'Is Admin?',
						width: '8%',
						type: 'checkbox',
						values: { '0': '', '1': 'Yes' },
						edit: false,
						defaultValue: '0'
					},
					enabled: {
						title: 'Status',
						width: '5%',
						type: 'radiobutton',
					    options: { '0': 'Blocked', '1': 'Active' },
						edit: false,
						defaultValue: '1',
					},
					change_password_button: {
						title: '',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<button title="Change Password" class="jtable-command-button jtable-change-password-button Change_Password" user_id="' + data.record.id + '"><span>Change Password</span></button>';
						}
					}
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				},
				formSubmitting: function(event, data){
					var fname = $("#Edit-fname").val();
					var lname = $("#Edit-lname").val();
					var email = $("#Edit-email").val();
					var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

					if(fname.trim() == '' || lname.trim() == '' || email.trim() == '')
					{
						message_notify('Please Ensure All Fields Are Filled!', 3);
						return false;
					}

					if(regex.test(email) === false)
					{
						message_notify('Email Is Not Valid!', 3);
						return false;
					}
				},
				recordAdded: function(event, data) {
					message_notify('The User Account Has Been Created.', 1);
					users_shown(data.record.id);
				},
				recordUpdated: function(event, data){
					message_notify('The User Profile Has Been Updated.', 1);
				},
				recordDeleted: function(event, data)
				{
					users_shown();
					message_notify('The User Account Has Been Deleted.', 1);
				}
			});
			$('#UserTable').jtable('load');

		};
		
		$('#UserTable').on("click", ".Change_Password", function() {
			var user_id = $(this).attr('user_id');
			
			if(user_id == '')
				return false;
			
			$("#change_user_password").dialog({
				resizable: false,
				modal: true,
				width:'252px',
				close: change_user_password_dialog_close,
				buttons: {
					'Cancel': function() {
						$( this ).dialog( "close" );
					},
					'Save': function() {
						var password = $("#Edit2-password").val();
						var password2 = $("#Edit2-password2").val();
						
						$("#Edit2-password").val('');
						$("#Edit2-password2").val('');

						if(password.trim() == '')
						{
							message_notify("Please Ensure 'Password' Field Is Filled!", 3);
							return false;
						}
					
						if(password.length < 6)
						{
							message_notify('Your Password Must Be At Least 6 Characters!', 3);
							return false;
						}
					
						if(password != password2)
						{
							message_notify('The Password And Confirmation Password Do Not Match!', 3);
							return false;
						}

						$.post("user-profile.php?action=update&token="+$("#token").val(),
						{
							password:password,
							password2:password2,
							id:user_id
						},
						function(data,status){
							var data2;
							if(!(typeof data === 'undefined'))
							{
								data2 = jQuery.parseJSON(data);
								if("OK".localeCompare(data2.Result) == 0)
									message_notify('Your Password Has Been Changed!', 1);
								else
									message_notify(data2.Message, 3);
							}
							else
								message_notify('', 3);
						});
					
						$( this ).dialog( "close" );
					}
				}
			});
		});
		function change_user_password_dialog_close()
		{
			$("#Edit-password").val('');
			$("#Edit-password2").val('');
		};


		///////////////////////////verification////////////////////////////////
		var verification_run = false;
		$scope.verification = function() {
			if(verification_run === false)
			{
				$('#assessment-type input').iCheck({
					radioClass: 'iradio_square-blue',
					increaseArea: '20%'
				});
				$('#assessment-type #list-assessment-all').iCheck('check');
				
				$('#verification-type input').iCheck({
					radioClass: 'iradio_square-blue',
					increaseArea: '20%'
				});
				$('#verification-type #verification-type-list').iCheck('check');
				
				$('#verificationwizard').hide();
				$("#comment_position").text('');
				$('#comment_position').hide();

				$('#verification_user_checkbox').iCheck({
					checkboxClass: 'icheckbox_square-blue',
					increaseArea: '20%'
				});
				$('#verification_user_checkbox').iCheck('uncheck');

				assessment_type = 0;
				get_assessment();
				verification_run = true;
			}
		};
		
		$scope.verification();
		
		function get_assessment_race_condition(lock)
		{
			if(lock === true)
			{
				$('#assessment-type #list-assessment-all').iCheck('disable');
				$('#assessment-type #list-assessment-new').iCheck('disable');
				$('#assessment-type #list-assessment-uncompleted').iCheck('disable');
				$('#assessment-type #list-assessment-complete').iCheck('disable');
			}
			else
			{
				$('#assessment-type #list-assessment-all').iCheck('enable');
				$('#assessment-type #list-assessment-new').iCheck('enable');
				$('#assessment-type #list-assessment-uncompleted').iCheck('enable');
				$('#assessment-type #list-assessment-complete').iCheck('enable');
			}
		};

		var assessment_type = 0;
		$('#assessment-type #list-assessment-all').on('ifChecked', function(){
			assessment_type = 0;
			get_assessment();
		});
		
		$('#assessment-type #list-assessment-new').on('ifChecked', function(){
			assessment_type = 1;
			get_assessment();
		});
		
		$('#assessment-type #list-assessment-complete').on('ifChecked', function(){
			assessment_type = 2;
			get_assessment();
		});
		
		$('#assessment-type #list-assessment-uncompleted').on('ifChecked', function(){
			assessment_type = 3;
			get_assessment();
		});
		
		var type_list = true;
		$('#verification-type #verification-type-list').on('ifChecked', function(){
			type_list = true;
		});
		
		$('#verification-type #verification-type-wizard').on('ifChecked', function(){
			type_list = false;
		});
		
		var new_assignment_show_notify = true;

		function get_assessment() {
			get_assessment_race_condition(true);
			
			$("#comment_position").text('');
			$('#comment_position').hide();
			
			$("#chosen-select-assessment-chapter").empty();
			$("#chosen-select-assessment-chapter").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-assessment-chapter").trigger("chosen:updated");
			$("#chosen-select-assessment-chapter").chosen("{}");
				
			$("#chosen-select-assessment").empty();
			$("#chosen-select-assessment").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-assessment").trigger("chosen:updated");
            $("#chosen-select-assessment").chosen("{}");
			$http.get("assignment-show.php?type="+assessment_type).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined'))
						{
							$("#chosen-select-assessment").append($('<option></option>').attr("value", value.id).text(value.assessment_name));
						}
					});	
					$("#chosen-select-assessment").trigger("chosen:updated");
					$("#chosen-select-assessment").chosen("{}");
					get_assessment_race_condition(false);
					if("Yes".localeCompare(response.data.new_assignment) == 0)
					{
						if(new_assignment_show_notify === true)
						{
							message_notify('You Have Some New Assignment.', 2);
							new_assignment_show_notify = false;
						}
					}
					else
						new_assignment_show_notify = true;
				}
				else
					message_notify(response.data.Message, 3);
            });
		};
		
		function get_admin_comment(assignment_id)
		{
			var admin_comment = '';
			$http.get("admin_comment_show.php?assignment_id=" + assignment_id).then(function(response){
				var data = response.data;
				if("OK".localeCompare(data.Result) == 0)
				{
					if(!(typeof data.Record.id === 'undefined'))
					{
						admin_comment = data.Record.admin_comment;
						admin_comment = admin_comment.replace(/\r\n|\n|\r/g, '<br/>');
					}
				}
				else
					message_notify(data.Message, 3);
				
				$("#comment_position").html(admin_comment);
				$('#comment_position').show();
			});
		}

		var chosen_select_assessment = 0;
		$("#chosen-select-assessment").change(function() {
			var FirstRun = true;
			chosen_select_assessment = $("#chosen-select-assessment").val();
			$("#chosen-select-assessment-chapter").empty();
			$("#chosen-select-assessment-chapter").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-assessment-chapter").trigger("chosen:updated");
            $("#chosen-select-assessment-chapter").chosen("{}");
			get_admin_comment(chosen_select_assessment);
			$http.get("assignment-show-chapter.php?type=" + assessment_type + "&id=" + chosen_select_assessment).then(function(response){
			  	if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined'))
						{
							chapters_all[value.id] = value.chapter_name;
							if(FirstRun === true)
							{
								$("#chosen-select-assessment-chapter").append($('<option></option>').attr("value", '0').text('ALL'));
								FirstRun = false;
							}

							$("#chosen-select-assessment-chapter").append($('<option></option>').attr("value", value.id).text(value.chapter_name + " (" + value.percent + "%) "));
						}
					});	
					$("#chosen-select-assessment-chapter").trigger("chosen:updated");
					$("#chosen-select-assessment-chapter").chosen("{}");
				}
				else
					message_notify(response.data.Message, 3);
            });
			
			user_comment();
		});	
		
		$scope.verification_start = function() {
			var chapter_id = $("#chosen-select-assessment-chapter").val();
			var assessment_id = chosen_select_assessment;
			if(assessment_id == 0)
			{
				message_notify("Please Ensure 'Assessment' Field Is Filled!", 3);
				return false;
			}
			
			if(type_list === true)
				verification_list(chapter_id, assessment_id);
			else
				verification_wizard();			
		};
		
		function verification_wizard()
		{
			$('#VerificationTable').hide();
			report.chapters = chapters_all;
			$http.get('rules-wizard.php?type=' + assessment_type + '&chapter_id=' + $("#chosen-select-assessment-chapter").val()+'&assignment_id='+chosen_select_assessment).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					if(!(typeof response.data.Records[0] === 'undefined'))
					{
						report.rules = response.data.Records;
						report.rules = report.rules.map(function (rule) {
							var factors = {};
							rule.showEdit = 'hide';
							rule.showNone = '';
							rule.showPass = 'hide';
							rule.showFail = 'hide';
							return rule;
						});
						report.rules[0].showEdit = '';

						$scope.report = report;
						$('#wizard').dialog({resizable: false, modal:true, width:'80%', height:'800'});
						$('#verificationwizard').show();
				  
						if($('body').scrollTop()>0)
							$('body').scrollTop(0);	//Chrome
						else
							if($('html').scrollTop()>0)	//Firefox
								$('html').scrollTop(0);
					}
					else
						message_notify('', 3);
				}
				else
					message_notify(response.data.Message, 3);
			});
			
		};

		function assessment_save(rule)
		{
			var comment = "";
			if(rule.comment == null)
				comment = "";
			else
				comment = rule.comment.replace(/\n/g, "%0D%0A");
				
			$http.get("verification_save.php?assignment_id="+chosen_select_assessment+"&id="+rule.id+"&PassOrFail="+rule.PassOrFail+"&comment="+comment+"&token="+$("#token").val()).then(function(response){
					var data = response.data;
					if("OK".localeCompare(data.Result) != 0)
						message_notify(data.Message, 3);
					else
					{
						if("Yes".localeCompare(data.Record.change_status) == 0)
						{
							if("Yes".localeCompare(data.Record.completed) == 0)
							{
								if(assessment_type != 0)
									get_assessment();
								message_notify('The Assignment Has Been Completed.', 1);
							}
							else
							{
								if(assessment_type != 0)
									get_assessment();
							}
						}
					}
			});
		};
		
		function verification_list(chapter_id, assignment_id) {
			$('#verificationwizard').hide();
			
			var messages_new = {
				editRecord: 'Rule Verification',
				deleteText: 'Delete Verification',
				deleteConfirmation: 'This Review Will Be Deleted. Are You Sure?'
			}
			
			$('#VerificationTable').jtable('destroy');
			$('#VerificationTable').jtable({
				messages: messages_new,
				title: 'Verification',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'chapter_id ASC, rule_number ASC',
				actions: {
					listAction: 'rules.php?type=' + assessment_type + '&chapter_id='+chapter_id+'&assignment_id='+assignment_id,
					updateAction: 'verification_save.php?assignment_id='+assignment_id+'&token='+$("#token").val(),
					deleteAction: 'verification_delete.php?assignment_id='+assignment_id+'&token='+$("#token").val()
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false,
						sorting: false
					},
					chapter_id: {
						title: 'Ch.',
						create: false,
						edit: false,
						width: '3%'
					},
					rule_number: {
						title: 'No.',
						create: false,
						edit: false,
						width: '3%'
					},
					title: {
						title: 'Title',
						type: 'textarea',
						create: false,
						edit: false,
						width: '47%'
					},
					level: {
						title: 'L.',
						create: false,
						edit: false,
						width: '3%'
					},
					methodology: {
						title: 'How To Verify?',
						type: 'textarea',
						create: false,
						edit: false,
						width: '22%'
					},
					PassOrFail: {
						title: 'Pass/Fail',
						create: false,
						type: 'radiobutton',
					    options: { '0': 'None', '1': 'Passed', '2': 'Failed' },
						defaultValue: '0',
						width: '4%'
					},
					comment: {
						title: 'Comment',
						type: 'textarea',
						create: false,
						width: '18%'
					}						
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				},
				recordUpdated: function(event, data){
					if("Yes".localeCompare(data.serverResponse.Record.change_status) == 0)
					{
						if("Yes".localeCompare(data.serverResponse.Record.completed) == 0)
						{
							if(assessment_type != 0)
								get_assessment();
							message_notify('The Assignment Has Been Completed.', 1);
						}
						else
						{
							if(assessment_type != 0)
								get_assessment();
						}
					}
				}
			});
			
			$('#VerificationTable').jtable('load');				
			$('#VerificationTable').show();

		};


		var user_comment_send = false;
		$('#verification_user_checkbox').on('ifChecked', function(){
			user_comment();
		});
		
		$('#verification_user_checkbox').on('ifUnchecked', function(){
			user_comment_send = false;
			$('#verification_user_comment').hide();
		});
		
		function user_comment()
		{
			var assignment_id = chosen_select_assessment;

			$("#verification_user_comment_textbox").val('');

			if(assignment_id == 0)
			{
				return false;
			}

			if($('#verification_user_checkbox').prop('checked')){
							
				$('#user_comment_wait').show();
				
				$http.get("user_comment.php?assignment_id=" + assignment_id).then(function(response){
					if("OK".localeCompare(response.data.Result) == 0)
					{
						var data = response.data.Record;
						if(!(typeof data.id === 'undefined'))
						{
							$('#verification_user_comment_textbox').val(data.user_comment);
							user_comment_send = true;
							$('#verification_user_comment').show();
						}

						$('#user_comment_wait').hide();
					}
					else
						message_notify(response.data.Message, 3);
				});
			}
		}
		
		var user_comment_changed = false;
		$("#verification_user_comment_textbox").change(function() {
			user_comment_changed = true;
		});

		$("#verification_user_comment_textbox").blur(function() {
			var user_comment = $("#verification_user_comment_textbox").val();
			var assignment_id = chosen_select_assessment;

			if(user_comment_changed === false || assignment_id == 0 || user_comment_send === false)
				return false;

			user_comment = user_comment.replace(/\n/g, "%0D%0A");

			$http.get("user_comment.php?assignment_id=" + assignment_id + "&user_comment=" + user_comment + '&token='+$("#token").val()).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Record;
					if(!(typeof data.id === 'undefined'))
					{
						$('#verification_user_comment_textbox').val(data.user_comment);
						$('#verification_user_comment').show();
						message_notify('The Comment Has Been Saved.', 1);
						user_comment_changed = false;
					}
				}
				else
					message_notify(response.data.Message, 3);
			});
		});


		///////////////////////////Notify /////////////////////////////////////
		
		function create_notify( template, vars, opts )
		{
			var $container = $("#container_notify").notify();
			return $container.notify("create", template, vars, opts);
		};

		function message_notify(message_text, severity)
		{
			var message_title = '';
			var message_icon = '';
			var message_expire;
			
			if(message_text == '' || message_text == undefined)
				message_text = "An Unknown Error Has Been Occurred.";
			
			if(severity === 1)
			{
				message_title = 'Message';
				message_icon = 'notify/message.png';
				message_expire = 5*1000;
			}
			else if(severity === 2)
			{
				message_title = 'Warning';
				message_icon = 'notify/warning.png';
				message_expire = false;
			}
			else
			{
				message_title = 'Error';
				message_icon = 'notify/error.png';
				message_expire = 10*1000;
			}

			create_notify("message_notify", { title:message_title, text:message_text, icon:message_icon },{ 
				expires:message_expire
			});

		};

		function create_help( template, vars, opts )
		{
			var $container = $("#container_help").notify();
			return $container.notify("create", template, vars, opts);
		};

		function help_continue_notify()
		{	
			create_help("help_continue", { title:'Help', text:'', icon:'notify/warning.png' },{ 
				expires:false,
				queue: 1
			});
		};


		///////////////////////////Help ///////////////////////////////////////
		var help_enabled = false;
		var selected_tab = 3;
		$scope.help = function() {
			if(help_enabled === false)
			{
				help_continue_notify();
				help_show_tab();

				$(".help_class").bind("click", function(e){
					var selected;
					if(selected_tab == 0)
						selected = $('input:radio[name=help0]:checked').val();
					else
						selected = $('input:radio[name=help3]:checked').val();
					$( this ).dialog( "close" );

					help_switch(selected);
				});
				
				$(".help_cancel").bind("click", function(e){
					help_enabled = false;
				});

				help_enabled = true;
			}
		};

		$('#menu').tabs({
			show: function(event, ui) {
				selected_tab = ui.index;
				help_show_tab();
			}
		});
		
		function help_show_tab()
		{
			if(selected_tab == 0)
			{
				$("#help3").hide();
				$("#help0").show();
			}
			else
			{
				$("#help0").hide();
				$("#help3").show();
			}			
		};

		function help_switch(selected)
		{
			switch(selected) {
				case '02':
					help_user_password();
					break;
				case '03':
					help_user_edit();
					break;
				case '31':
					help_verification();
					break;
				case '32':
					help_verification_comment();
					break;
				case '33':
					help_verification_delete();
					break;
				default:
					message_notify('', 3);
			}
		};
		
		function help_user_password()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .Change_Password").attr('data-step', '1');
			$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .Change_Password").attr('data-intro', "Click On The 'Change Password' Button");

			introJs().start();
		};
		
		function help_user_edit()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-step', '1');
			$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-intro', "Click On The 'Edit User Profile' Button");

			introJs().start();
		};

		function help_verification()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			$("#assessment-type").attr('data-step', '1');
			$("#assessment-type").attr('data-intro', 'Select A Category Of Assignment');
			$("#list-assessment").attr('data-step', '2');
			$("#list-assessment").attr('data-intro', 'Select An Assessment');
			$("#list-assessment-chapter").attr('data-step', '3');
			$("#list-assessment-chapter").attr('data-intro', 'Select A Chapter (If Required)');
			$("#verification-type").attr('data-step', '4');
			$("#verification-type").attr('data-intro', 'Select Verification Mode');
			$("#list-verification-Type").attr('data-step', '5');
			$("#list-verification-Type").attr('data-intro', "Select On The 'Start Verification' Button");
			$("#VerificationTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-step', '6');
			$("#VerificationTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-intro', "Click On The 'Rule Verification' Button To Verify Each Rule");
			
			introJs().start();
		};

		function help_verification_comment()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			$("#list-assessment").attr('data-step', '1');
			$("#list-assessment").attr('data-intro', 'Select An Assessment');
			$("#user_comment_change").attr('data-step', '2');
			$("#user_comment_change").attr('data-intro', 'Select The Checkbox And Then Enter Text In The Corresponding Field');

			introJs().start();			
		};

		function help_verification_delete()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			$("#list-assessment").attr('data-step', '1');
			$("#list-assessment").attr('data-intro', 'Select An Assessment');
			$("#verification-type").attr('data-step', '2');
			$("#verification-type").attr('data-intro', "Select 'Table' Mode");
			$("#list-verification-Type").attr('data-step', '3');	
			$("#list-verification-Type").attr('data-intro', "Select On The 'Start Verification' Button");
			if ($('#VerificationTable').text() != '')
			{
				$("#VerificationTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-step', '4');
				$("#VerificationTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-intro', "Click On The 'Delete Verification' Button");	
			}

			introJs().start();
		};
		///////////////////////////////////////////////////////////////////////


		function jtable_select_style()
		{
			$(".goto_page_select").trigger("chosen:updated");
			$(".goto_page_select").chosen({
				"disable_search": true
			});
			
			$(".page_size_select").trigger("chosen:updated");
            $(".page_size_select").chosen({
				"disable_search": true
			});
		};

		$scope.logout = function() {
           	$http.get('logout.php?token='+$("#token").val()).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
			    {
			      $('#welcome2').hide();
				  $('#report').hide();
				  $('#login').show();				  
				  $("#report_name").val("");
				  report = {};
				  window.location.reload();
                }
				else
					message_notify(response.data.Message, 3);
            });	  
		};
		
		var chapters_all = {};
		
		var report = {};

        $scope.showNone = '';
        $scope.showPass = 'hide';
        $scope.showFail = 'hide';

        $scope.none = function (rule) {
			rule.PassOrFail = 0;
            rule.showNone = '';
            rule.showPass = 'hide';
            rule.showFail = 'hide';
        };
		
		$scope.pass = function (rule) {
			rule.PassOrFail = 1;
			rule.showNone = 'hide';
            rule.showPass = '';
            rule.showFail = 'hide';
        };
		
        $scope.fail = function (rule) {
            rule.PassOrFail = 2;
            rule.showNone = 'hide';
            rule.showPass = 'hide';
            rule.showFail = '';
        };

        $scope.getCurrentRule = function () {
            return report.rules.filter(function (rule) {
                return rule.showEdit !== 'hide';
            }).shift();
        };

        $scope.edittedIsLast = function () {
            var rule = $scope.getCurrentRule();
            var lastRule = report.rules[report.rules.length - 1];
            return rule.chapter_id === lastRule.chapter_id && rule.rule_number === lastRule.rule_number ? '' : 'hide';
        };

        $scope.edittedIsNotLast = function () {
            var rule = $scope.getCurrentRule();
            return $scope.edittedIsLast(rule) === 'hide' ? '' : 'hide';
        };

        $scope.edittedIsNotFirst = function () {
            var rule = $scope.getCurrentRule();
            var firstRule = report.rules[0];
            return rule.chapter_id === firstRule.chapter_id && rule.rule_number === firstRule.rule_number ? 'hide' : '';
        };

        $scope.edittedPercentage = function () {
            var percentage = 0;
            report.rules.forEach(function (rule, index) {
                if (rule.showEdit !== 'hide') {
                    percentage = (index) / report.rules.length * 100;
                }
            });
            return percentage;
        };

        $scope.next = function (rule) {
            var setEdit = false;
            for (var i = 0; i < report.rules.length; i++) {
                if (setEdit === true) {
                    report.rules[i - 1].showEdit = 'hide';
                    report.rules[i].showEdit = '';
                    break;
                }
                if (report.rules[i].showEdit !== 'hide') {
                    setEdit = true;
                }
            }
        };

		$scope.next2 = function (rule) {
			assessment_save(rule);
			$scope.next(rule);
        };

        $scope.prev = function () {
            var setEdit = false;
            for (var i = report.rules.length - 1; i >= 0; i--) {
                if (setEdit === true) {
                    report.rules[i + 1].showEdit = 'hide';
                    report.rules[i].showEdit = '';
                    break;
                }
                if (report.rules[i].showEdit !== 'hide') {
                    setEdit = true;
                }
            }
        };

        $scope.done = function (rule) {
            $('#wizard').dialog('close');
        };
		
		$scope.done2 = function (rule) {
			assessment_save(rule);
			$('#wizard').dialog('close');
        };
		
        $scope.getChapterTitle = function (chapter_id) {
            return report.chapters[chapter_id];
        };
});