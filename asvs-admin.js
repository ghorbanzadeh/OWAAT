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
				users_shown();
				user_table();
				user_run = true;
			}
		};

		function users_shown(selected_id)
		{
			$("#chosen-select-uname").empty();
			$("#chosen-select-uname").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-uname").append($('<option></option>').attr("value", 0).text("ALL"));
			$("#chosen-select-uname").trigger("chosen:updated");
			$("#chosen-select-uname").chosen("{}");
			$http.get("uname.php").then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined'))
							$("#chosen-select-uname").append($('<option></option>').attr("value", value.id).text(value.uname));
					});
					$("#chosen-select-uname").trigger("chosen:updated");
					$("#chosen-select-uname").chosen("{}");

					if(!(typeof selected_id === 'undefined'))
					{
						$('#chosen-select-uname').val(selected_id).trigger("chosen:updated");

						if(log === true)
							log_table();
						else
							user_table();
					}
				}
				else
					message_notify(response.data.Message, 3);
			});
		};
		
		$("#chosen-select-uname").change(function() {
			if(log === true)
				log_table();
			else
				user_table();
		});
		
		function user_table()
		{
			var messages_new = {
				addNewRecord: 'Add New User',
				editRecord: 'Edit User Profile',
				deleteText: 'Delete User'
			}

			var user_id = $("#chosen-select-uname").val();
			if(user_id == '')
				user_id = 0;

			$('#UserTable').jtable('destroy');
			$('#UserTable').jtable({
				messages: messages_new,
				title: 'Users',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'uname ASC',
				deleteConfirmation: function(data) {
					data.deleteConfirmMessage = "Are You Sure To Delete User '" + data.record.uname + "'?";
				},
				actions: {
					listAction: 'user.php?action=list&select='+user_id,
					createAction: 'user.php?action=create&token='+$("#token").val(),
					updateAction: 'user.php?action=update&token='+$("#token").val(),
					deleteAction: 'user.php?action=delete&token='+$("#token").val()
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false,
						sorting: false
					},
					uname: {
						title: 'Uname',
						edit: false,
						width: '23%'
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
						title: 'Admin',
						width: '5%',
						type: 'radiobutton',
						options: { '0': 'No', '1': 'Yes' },
						defaultValue: '0'
					},
					enabled: {
						title: 'Status',
						width: '5%',
						type: 'radiobutton',
					    options: { '0': 'Blocked', '1': 'Active' },
						defaultValue: '1',
					},
					log_button: {
						title: '',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<button title="Show User Logs" class="jtable-command-button jtable-logs-command-button user_logs_show" user_id="' + data.record.id + '"><span>Show User Logs</span></button>';
						}
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
				formCreated: function(event, data){
					$('#Edit-fname').attr('maxlength', '30');
					$('#Edit-lname').attr('maxlength', '30');
					$('#Edit-email').attr('maxlength', '30');
					$('#Edit-uname').attr('maxlength', '30');

					if(data.formType == 'create')
					{
						$('#Edit-password').attr('maxlength', '40');
						$('#Edit-password2').attr('maxlength', '40');
					}
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
					
					if(data.formType == 'create')
					{
						var uname = $("#Edit-uname").val();
						var password = $("#Edit-password").val();
						var password2 = $("#Edit-password2").val();

						if(uname.trim() == '')
						{
							message_notify('Please Ensure All Fields Are Filled!', 3);
							return false;
						}
					
						if(password.trim() == '')
						{
							message_notify("Please Ensure 'Password' Field Is Filled!", 3);
							return false;
						}
						
						if(password.length < 6)
						{
							message_notify('The Password Must Be At Least 6 Characters!', 3);
							return false;
						}
					
						if(password != password2)
						{
							message_notify('The Password And Confirmation Password Do Not Match!', 3);
							return false;
						}
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
							message_notify('The Password Must Be At Least 6 Characters!', 3);
							return false;
						}
					
						if(password != password2)
						{
							message_notify('The Password And Confirmation Password Do Not Match!', 3);
							return false;
						}

						$.post("user.php?action=update&token="+$("#token").val(),
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
									message_notify('The Password Of User ' + data2.Record.uname + ' Has Been Changed!', 1);
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
			$("#Edit2-password").val('');
			$("#Edit2-password2").val('');
		};

		$('#UserTable').on("click", ".user_logs_show", function() {
			var id = $(this).attr('user_id');
			$("#UserTable").hide();
			$("#LogTable").show();
			$("#back_button").show();
			log = true;
			users_shown(id);
		});

		$scope.user_management = function() {
			var user_id = $("#chosen-select-uname").val();
			$("#LogTable").hide();
			$("#back_button").hide();
			$("#UserTable").show();
			log = false;
			if(user_id != '')
				users_shown(user_id);
		};

		var log = false;
		function log_table()
		{
			var messages_new = {
				deleteText: 'Delete Log'
			}

			$('#LogTable').jtable('destroy');
			$('#LogTable').jtable({
				messages: messages_new,
				title: 'Logs',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'time DESC',
				deleteConfirmation: function(data) {
					data.deleteConfirmMessage = "Are You Sure To Delete The Log?";
				},
				actions: {
					listAction: 'logging.php?action=list&user_id='+$("#chosen-select-uname").val(),
					deleteAction: 'logging.php?action=delete&token='+$("#token").val()
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false,
						sorting: false
					},
					uname: {
						title: 'Uname',
						edit: false,
						width: '20%'
					},
					ip: {
						title: 'IP',
						width: '10%'
					},
					data: {
						title: 'Data',
						width: '38%'
					},
					time: {
						title: 'Time',
						width: '12%'
					},
					action: {
						title: 'Action',
						width: '20%'
					}
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				},
				recordDeleted: function(event, data)
				{
					message_notify('The Log Has Been Deleted.', 1);
				}
			});
			$('#LogTable').jtable('load');

		};


		///////////////////////////rules///////////////////////////////////////
		var chapter_run = false;
		$scope.chapter = function() {
		    if(chapter_run === false)
			{
				$('#rules-methodology input').iCheck({
					radioClass: 'iradio_square-blue',
					increaseArea: '20%'
				});
				$('#rules-methodology #methodology-select').iCheck('check');

				$("#chosen-select-chapter").empty();
				$("#chosen-select-chapter").append($('<option></option>').attr("value", '').text(''));
				$("#chosen-select-chapter").append($('<option></option>').attr("value", 0).text("ALL"));
				$("#chosen-select-chapter").trigger("chosen:updated");	
				$("#chosen-select-chapter").chosen("{}");
				$http.get("chapters.php").then(function(response){
					if("OK".localeCompare(response.data.Result) == 0)
					{
						var data = response.data.Records;
						$.each(data,function(i, value)
						{
							if(!(typeof value.id === 'undefined'))
								$("#chosen-select-chapter").append($('<option></option>').attr("value", value.id).text("V" + value.id + ": " + value.chapter_name));
						});
						$("#chosen-select-chapter").trigger("chosen:updated");	
						$("#chosen-select-chapter").chosen("{}");
					}
					else
						message_notify(response.data.Message, 3);
				});

				chapter_run = true;
			}
		};
		
		var rm = true;
		$('#rules-methodology #rules-select').on('ifChecked', function(){
			rm = false;
			Rule_Table();
		});
		
		$('#rules-methodology #methodology-select').on('ifChecked', function(){
			rm = true;
			Methodology_Table();
		});
		
		$("#chosen-select-chapter").change(function() {
			if(rm === true)
				Methodology_Table();
			else
				Rule_Table();
		});
		
		function Rule_Table()
		{
			var messages_new = {
				addNewRecord: 'Add New Rule',
				editRecord: 'Edit Rule',
				deleteText: 'Delete Rule'
			}
			
			$('#Rule-MethodologyTable').jtable('destroy');	
			$('#Rule-MethodologyTable').jtable({
				messages: messages_new,
				title: 'Rules',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'chapter_id ASC, rule_number ASC',
				deleteConfirmation: function(data) {
					data.deleteConfirmMessage = 'Are You Sure To Delete Rule V' + data.record.chapter_id + '.' + data.record.rule_number + '?';
				},
				actions: {
					listAction: 'rules-action.php?action=list&select='+$("#chosen-select-chapter").val(),
					createAction: 'rules-action.php?action=create&token='+$("#token").val(),
					updateAction: 'rules-action.php?action=update&token='+$("#token").val(),
					deleteAction: 'rules-action.php?action=delete&token='+$("#token").val()
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
						title: 'Chapter',
						width: '5%'
					},
					rule_number: {
						title: 'Number',
						width: '5%'
					},
					title: {
						title: 'Title',
						type: 'textarea',
						width: '75%'
					},
					level: {
						title: 'Level',
						type: 'radiobutton',
					    options: { '1': '1', '2': '2', '3': '3' },
						defaultValue: '1',
						width: '5%'
					}
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				},
				formCreated: function(event, data){
					$('#Edit-chapter_id').attr('maxlength', '2');
					$('#Edit-rule_number').attr('maxlength', '3');
					$('#Edit-level').attr('maxlength', '2');
					if(data.formType == 'create')
					{
						var chapter_id = $('#chosen-select-chapter').val();
						if(chapter_id != '' && chapter_id != 0)
						{
							$('#Edit-chapter_id').val(chapter_id);
							$('#Edit-rule_number').focus();
						}
					}

				},
				formSubmitting: function(event, data){
					var chapter_id = $('#Edit-chapter_id').val();
					var rule_number = $('#Edit-rule_number').val();
					var level = $('#Edit-level').val();
					var title = $('#Edit-title').val();
					var intRegex = /^\d+$/;

					if(!intRegex.test(chapter_id))
					{
						message_notify("Chapter Number Must Be An Integer Value!", 3);
						return false;
					}
					
					if(!intRegex.test(rule_number))
					{
						message_notify("Rule Number Must Be An Integer Value!", 3);
						return false;
					}
					
					/*if(!intRegex.test(level))
					{
						message_notify("Level Must Be An Integer Value!", 3);
						return false;
					}*/
					
					if(title.trim() == '')
					{
						message_notify("Please Ensure 'Title' Field Is Filled!", 3);
						return false;
					}
				},
				recordAdded: function(event, data) {
					message_notify('The Rule Has Been Created.', 1);
				},
				recordUpdated: function(event, data){
					message_notify('The Rule Has Been Updated.', 1);
				},
				recordDeleted: function(event, data)
				{
					message_notify('The Rule Has Been Deleted.', 1);
				}
			});
			$('#Rule-MethodologyTable').jtable('load');

		};
		
		function Methodology_Table()
		{
			var messages_new = {
				editRecord: 'Edit Methodology'
			}
			
			var selected_chapter = $("#chosen-select-chapter").val();
			if(selected_chapter == '' || selected_chapter == null)
				selected_chapter = 0;

			$('#Rule-MethodologyTable').jtable('destroy');	
			$('#Rule-MethodologyTable').jtable({
				messages: messages_new,
				title: 'Methodology',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'chapter_id ASC, rule_number ASC',
				actions: {
					listAction: 'methodology-action.php?action=list&select='+selected_chapter,
					updateAction: 'methodology-action.php?action=update&token='+$("#token").val()
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
						title: 'Chapter',
						create: false,
						edit: false,
						width: '5%'
					},
					rule_number: {
						title: 'Number',
						create: false,
						edit: false,
						width: '5%'
					},
					title: {
						title: 'Title',
						type: 'textarea',
						create: false,
						edit: false,
						width: '50%'
					},
					level: {
						title: 'Level',
						create: false,
						edit: false,
						width: '5%'
					},
					methodology: {
						title: 'How To Verify?',
						type: 'textarea',
						create: false,
						width: '25%'
					}
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				},
				recordUpdated: function(event, data){
					message_notify('The Methodology Has Been Updated.', 1);
				}
			});
			$('#Rule-MethodologyTable').jtable('load');

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

							$("#chosen-select-assessment-chapter").append($('<option></option>').attr("value", value.id).text("V" + value.id + ": " + value.chapter_name + " (" + value.percent + "%) "));
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


		///////////////////////////Assessment//////////////////////////////////
		var assessment_run = false;
		$scope.assessment = function() {
			if(assessment_run === false)
			{
				$('#assessment-or-assignment input').iCheck({
					radioClass: 'iradio_square-blue',
					increaseArea: '20%'
				});
				$('#assessment-or-assignment #assessment-select').iCheck('check');

				assessment_run = true;
			}
			assignment_run = false;
	    };

		var assignment_select_first = false;
		var assessment_select = 1;
		$("#chosen-select-assessment-all").change(function() {
			if(assessment_select === 1)
				Assessment_Table();
			else if(assessment_select === 2)
			{
				if(assignment_select_first === true)
				{
					$("#assignment_save_position").show();
					assignment();
				}
				Assignment_Table();
			}
			else
			{
				assignment_results_show();
				$('#show_results_user_name').hide();
				$('#show_results_user_comment').hide();
			}

			admin_comment();
		});
		
		$("#chosen-select-uname-assignment").change(function() {
			uname_assignment_show();
		});
		
		function uname_assignment_show()
		{
			var user_id = $("#chosen-select-uname-assignment").val();
			var results = '<p>' + $('#chosen-select-uname-assignment option:selected').text() + ' Uncompleted Assignments:</p>';
			var NotFound = true;

			if(user_id == '')
				return false;
			$("#user_assignment_show").html(results);
			$http.get("user_assignment_show.php?action=assessment&user_id=" + user_id).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.assignment_id === 'undefined'))
						{
							NotFound = false;
							results += '<a href="javascript:void(0)" class="user_assignment_show_a" assignment_id = "' + value.assignment_id + '">' + value.assessment_name + '</a><br/>';
						}
					});
				
					if(NotFound === true)
						results += 'Not Found.';

					$("#user_assignment_show").html(results);
				}
				else
					message_notify(response.data.Message, 3);
			});
			
			$('#user_chapter_show').text('Click on the each assessment to show corresponding uncompleted chapters.');
			$('#user_assignment_show').show();
			$('#user_chapter_show').show();

			admin_comment();
		}

		$('#user_assignment_show').on("click", ".user_assignment_show_a", function() {
			var results = '<p>Uncompleted Chapters Of ' + $(this).text() + ':</p>';
			var id = $(this).attr('assignment_id');
			var NotFound = true;
			$("#user_chapter_show").html(results);
			$http.get("user_assignment_show.php?action=chapter&assignment_id=" + id).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.chapter_id === 'undefined'))
						{
							NotFound = false;
							results += value.chapter_name + '<br/>';
						}
					});

					if(NotFound === true)
						results += 'Not Found.';

					$("#user_chapter_show").html(results);
				}
				else
					message_notify(response.data.Message, 3);
			});
			
		});

		function assessment_all(selected_id)
		{
			$("#chosen-select-assessment-all").empty();
			$("#chosen-select-assessment-all").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-assessment-all").trigger("chosen:updated");			  
			$("#chosen-select-assessment-all").chosen("{}");
			$http.get("assessment-show.php").then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined'))
							$("#chosen-select-assessment-all").append($('<option></option>').attr("value", value.id).text(value.assessment_name));
					});
					$("#chosen-select-assessment-all").trigger("chosen:updated");			  
					$("#chosen-select-assessment-all").chosen("{}");

					if(!(typeof selected_id === 'undefined'))
					{
						$('#chosen-select-assessment-all').val(selected_id).trigger("chosen:updated");
						Assessment_Table();
					}
				}
				else
					message_notify(response.data.Message, 3);
			});
		};

		function Assessment_Table()
		{
			var messages_new = {
				addNewRecord: 'Add New Assessment',
				editRecord: 'Edit Assessment',
				deleteText: 'Delete Assessment'
			}

			$('#AssessmentTable').jtable('destroy');
			$('#AssignmentTable').jtable('destroy');
			$('#AssignmentTable-results-show').jtable('destroy');
			$('#AssessmentTable').jtable({
				messages: messages_new,
				title: 'Assessments',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'assessment_name ASC',
				deleteConfirmation: function(data) {
					data.deleteConfirmMessage = 'Are You Sure To Delete Assessment ' + data.record.assessment_name + '?';
				},
				actions: {
					listAction: 'assessment.php?action=list&select='+$("#chosen-select-assessment-all").val(),
					createAction: 'assessment.php?action=create&token='+$("#token").val(),
					updateAction: 'assessment.php?action=update&token='+$("#token").val(),
					deleteAction: 'assessment.php?action=delete&token='+$("#token").val()
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false,
						sorting: false
					},
					assessment_name: {
						title: 'Assessment',
						width: '15%'
					},
					description: {
						title: 'Description',
						type: 'textarea',
						width: '30%'
					},
					uname: {
						title: 'Created By',
						create: false,
						edit: false,
						width: '15%'
					},
					create_time: {
						title: 'Create Time',
						create: false,
						edit: false,
						width: '12%'
					},
					completed: {
						title: 'Completed',
						width: '5%',
						create: false,
						edit: false,
						sorting: false
					},
					complete_time: {
						title: 'Completed Time',
						create: false,
						edit: false,
						width: '13%'
					},
					assignment_button: {
						title: '',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<button title="Show Assignments" class="jtable-command-button jtable-assignment-command-button Show_Assignments" assignment_id="' + data.record.id + '"><span>Show Assignments</span></button>';
						}
					},
					results_button: {
						title: '',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<button title="Show Results" class="jtable-command-button jtable-results-command-button Assessment_Show_Results" assignment_id="' + data.record.id + '"><span>Show Results</span></button>';
						}
					}
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				},
				formCreated: function(event, data){
					$('#Edit-assessment_name').attr('maxlength', '50');
				},
				formSubmitting: function(event, data){
					var assessment_name = $("#Edit-assessment_name").val();

					if(assessment_name.trim() == '')
					{
						message_notify("Please Ensure The 'Assessment' Field Is Filled!", 3);
						return false;
					}
				},
				recordAdded: function(event, data) {
					message_notify('The Assessment Has Been Created.', 1);
					assessment_all(data.record.id);
				},
				recordUpdated: function(event, data){
					message_notify('The Assessment Has Been Updated.', 1);
				},
				recordDeleted: function(event, data)
				{
					assessment_all();
					message_notify('The Assessment Has Been Deleted.', 1);
				}
			});
			$('#AssessmentTable').jtable('load');
	
		};

		$('#AssessmentTable').on("click", ".Show_Assignments", function() {
			var id = $(this).attr('assignment_id');
			$('#assessment-or-assignment #assignment-select').iCheck('check');
			$('#chosen-select-assessment-all').val(id).trigger("chosen:updated");
			$("#assignment_save_position").show();
			assignment();
			Assignment_Table();
			assignment_select_first = false;
		});

		$('#AssessmentTable').on("click", ".Assessment_Show_Results", function() {
			var id = $(this).attr('assignment_id');
			$('#assessment-or-assignment #assignment-select_show_results').iCheck('check');
			$('#chosen-select-assessment-all').val(id).trigger("chosen:updated");
			assignment_results_show();
		});

		var assignment_all_selected_chapter_id = [];

		function Assignment_Table()
		{
			var messages_new = {
				deleteText: 'Delete Assignment'
			}

			assignment_all_selected_chapter_id = [];
			$('#AssessmentTable').jtable('destroy');
			$('#AssignmentTable').jtable('destroy');
			$('#AssignmentTable').jtable({
				messages: messages_new,
				title: 'Assignments (Select check boxes to display results)',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'assignment_time DESC',
				deleteConfirmation: function(data) {
					data.deleteConfirmMessage = 'This Assignment And Its Coresponding Review Will Be Deleted. Are You Sure?';
				},
				actions: {
					listAction: 'assignment.php?action=list&assessment_id='+$("#chosen-select-assessment-all").val(),
					deleteAction: 'assignment.php?action=delete&token='+$("#token").val()
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false,
						sorting: false
					},
					uname: {
						title: 'Uname',
						create: false,
						edit: false,
						width: '15%'
					},
					chapter_id: {
						title: 'Ch.',
						create: false,
						edit: false,
						width: '5%'
					},
					chapter_name: {
						title: 'Chapter',
						create: false,
						edit: false,
						width: '40%'
					},
					completed: {
						title: 'Completed',
						create: false,
						edit: false,
						sorting: false,
						width: '10%'
					},
					assignment_time: {
						title: 'Assignment Time',
						create: false,
						edit: false,
						width: '20%'
					},
					rsults_checkbox: {
						title: 'Show Results',
						create: false,
						edit: false,
						sorting: false,
						width: '10%',
						display: function(data) {
							return '<input type="checkbox" class="Show_Results" id="' + data.record.id + '">';
						}
					}
				},
				recordsLoaded: function(event, data) {

					jtable_select_style();

					$('.Show_Results').iCheck({
						checkboxClass: 'icheckbox_square-blue',
						increaseArea: '10%'
					});

					$.each(assignment_all_selected_chapter_id, function(i){
						var id = assignment_all_selected_chapter_id[i];
						
						$.each(data.records,function(i, value)
						{								
							if(value.id === id)
								$('.Show_Results#'+id).iCheck('check');
						});
					});

					$('.Show_Results').on('ifChecked', function(){
						var assignment_chapter_id = $(this).attr('id');

						if(assignment_chapter_id == null || assignment_chapter_id == '')
						{
							message_notify("Please Ensure All Field Is Filled!", 3);
							return false;
						}

						if(assignment_all_selected_chapter_id.indexOf(assignment_chapter_id) == -1)
							assignment_all_selected_chapter_id.push(assignment_chapter_id);

						results_show(assignment_all_selected_chapter_id.join());
					});
					
					$('.Show_Results').on('ifUnchecked', function(){
						var assignment_chapter_id = $(this).attr('id');
						$.each(assignment_all_selected_chapter_id, function(i){
							if(assignment_all_selected_chapter_id[i] === assignment_chapter_id) {
								assignment_all_selected_chapter_id.splice(i,1);
								return false;
							}
						});

						results_show(assignment_all_selected_chapter_id.join());
					});

				},
				recordDeleted: function(event, data)
				{
					uname_assignment_show();
					message_notify('The Assignment Has Been Deleted.', 1);
				}
			});
			$('#AssignmentTable').jtable('load').then;

		};

		function results_show(assignment_all_selected_chapter_id_string)
		{
			var assignment_chapters_id = '';

			assignment_chapters_id = assignment_all_selected_chapter_id_string.replace(/,/g, "%2C");

			if(assignment_chapters_id == null || assignment_chapters_id == '')
			{
				$('#AssignmentTable-results-show').jtable('destroy');
				return false;
			}

			$('#AssignmentTable-results-show').show();

			$('#AssignmentTable-results-show').jtable('destroy');
			$('#AssignmentTable-results-show').jtable({
				title: 'Results',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'chapter_id ASC, rule_number ASC',
				actions: {
					listAction: 'show_results.php?assignment_chapters_id='+assignment_chapters_id
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
						width: '2%'
					},
					rule_number: {
						title: 'No.',
						width: '2%'
					},
					title: {
						title: 'Title',
						type: 'textarea',
						width: '35%'
					},
					uname: {
						title: 'User',
						width: '10%'
					},
					PassOrFail: {
						title: 'Pass/Fail',
						type: 'radiobutton',
					    options: { '0': 'None', '1': 'Passed', '2': 'Failed' },
						defaultValue: '0',
						width: '5%'
					},
					comment: {
						title: 'Comment',
						type: 'textarea',
						width: '25%'
					},
					last_modified: {
						title: 'Last Modified',
						width: '10%'
					}						
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				}
			});
			
			$('#AssignmentTable-results-show').jtable('load');
			
		};
		
		$scope.results_show_all = function() {
			var users_id = '';
			var users_tmp = $("#chosen-select-uname-assignment_show_results").val();
			var chapters_id = '';
			var chapters_id_tmp = $('#chosen-select-chapter-assignment_show_results').val();
			var assessment_id = $("#chosen-select-assessment-all").val();

			if(users_tmp == null || chapters_id_tmp == null || assessment_id == '')
			{
				message_notify("Please Ensure All Field Is Filled!", 3);
				return false;
			}

			users_tmp = users_tmp.toString();
			users_id = users_tmp.replace(/,/g, "%2C");

			chapters_id_tmp = chapters_id_tmp.toString();
			chapters_id = chapters_id_tmp.replace(/,/g, "%2C");
			
			$('#AssignmentTable-results-show').show();

			$('#AssignmentTable-results-show').jtable('destroy');
			$('#AssignmentTable-results-show').jtable({
				title: 'Results',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'chapter_id ASC, rule_number ASC',
				actions: {
					listAction: 'show_results_assessment.php?assessment_id=' + assessment_id + '&users_id=' + users_id + '&chapters_id=' + chapters_id
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
						width: '2%'
					},
					rule_number: {
						title: 'No.',
						width: '2%'
					},
					title: {
						title: 'Title',
						type: 'textarea',
						width: '35%'
					},
					uname: {
						title: 'User',
						width: '10%'
					},
					PassOrFail: {
						title: 'Pass/Fail',
						type: 'radiobutton',
					    options: { '0': 'None', '1': 'Passed', '2': 'Failed' },
						defaultValue: '0',
						width: '5%'
					},
					comment: {
						title: 'Comment',
						type: 'textarea',
						width: '25%'
					},
					last_modified: {
						title: 'Last Modified',
						width: '10%'
					}						
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				}
			});
			
			$('#AssignmentTable-results-show').jtable('load');

		};
		
		$('#assessment-or-assignment #assessment-select').on('ifChecked', function(){
			$("#assignment_save_position").hide();
			$("#assignment_show_results").hide();
			$('#show_results_user_name').hide();
			$('#show_results_user_comment').hide();
			$('#user_assignment_results_show').hide();

			$('#user_assignment_show').hide();
			$('#user_chapter_show').hide();
			
			assessment_select = 1;
			assessment_all();
			Assessment_Table();
		});
		
		var assignment_select_first_checked = true;
		$('#assessment-or-assignment #assignment-select').on('ifChecked', function(){
			$("#assignment_show_results").hide();
			$('#show_results_user_name').hide();
			$('#show_results_user_comment').hide();
			$('#user_assignment_results_show').show();

			assessment_select = 2;
			assignment_select_first = true;

			$('#AssignmentTable-results-show').jtable('destroy');

			$('#AssessmentTable').jtable('destroy');
			
			if(assignment_select_first_checked === true)
			{
				$('#new_assignment_comment').iCheck({
					checkboxClass: 'icheckbox_square-blue',
					increaseArea: '20%'
				});
				$('#new_assignment_comment').iCheck('uncheck');
				assignment_select_first_checked = false;
			}

			if($("#chosen-select-assessment-all").val() != '')
			{
				$("#assignment_save_position").show();
				Assignment_Table();
				assignment();
				assignment_select_first = false;
			}
			
			if($("#chosen-select-uname-assignment").val() != '' && $("#chosen-select-uname-assignment").val() != null)
			{
				$('#user_assignment_show').show();
				$('#user_chapter_show').show();
			}
		});

		$('#assessment-or-assignment #assignment-select_show_results').on('ifChecked', function(){
			
			$("#assignment_save_position").hide();
			$('#show_results_user_name').hide();
			$('#show_results_user_comment').hide();
			$('#user_assignment_results_show').show();

			$('#user_assignment_show').hide();
			$('#user_chapter_show').hide();

			$('#AssessmentTable').jtable('destroy');
			$('#AssignmentTable').jtable('destroy');
			$('#AssignmentTable-results-show').jtable('destroy');
			
			assessment_select = 3;

			if($("#chosen-select-assessment-all").val() != '')
				assignment_results_show();

		});

		$('#new_assignment_comment').on('ifChecked', function(){
			admin_comment();
		});

		$('#new_assignment_comment').on('ifUnchecked', function(){
			admin_comment_send = false;
			$('#admin_comment_wizard').hide();
		});

		function admin_comment()
		{
			var assessment_id = $("#chosen-select-assessment-all").val();
			var user_id = $("#chosen-select-uname-assignment").val();

			$("#admin_comment_wizard_textbox").val('');

			if(assessment_id == '' || user_id == '')
			{
				return false;
			}

			if($('#new_assignment_comment').prop('checked')){
							
				$('#admin_comment_wizard_wait').show();
				
				$http.get("admin_comment.php?assessment_id=" + assessment_id + "&user_id=" + user_id).then(function(response){
					var data = response.data;
					if("OK".localeCompare(data.Result) == 0)
					{
						if(!(typeof data.Record.id === 'undefined'))
							$('#admin_comment_wizard_textbox').val(data.Record.admin_comment);
						else
							$('#admin_comment_wizard_textbox').val('');
						admin_comment_send = true;
						$('#admin_comment_wizard').show();
					}
					else
						message_notify(data.Message, 3);
					$('#admin_comment_wizard_wait').hide();
				});
			}
		};
		
		var admin_comment_changed = false;
		$("#admin_comment_wizard_textbox").change(function() {
			admin_comment_changed = true;
		});
		
		$("#admin_comment_wizard_textbox").blur(function() {
			var user_id = $("#chosen-select-uname-assignment").val();
			var assessment_id = $("#chosen-select-assessment-all").val();
			var admin_comment = $("#admin_comment_wizard_textbox").val();	

			if(admin_comment_changed === false || user_id == '' || assessment_id == '' || admin_comment_send === false)
				return false;

			admin_comment = admin_comment.replace(/\n/g, "%0D%0A");

			$http.get("admin_comment.php?assessment_id=" + assessment_id + "&user_id=" + user_id + "&admin_comment=" + admin_comment+'&token='+$("#token").val()).then(function(response){	
				var data = response.data;
				if("OK".localeCompare(data.Result) == 0)
				{
					if(!(typeof data.Record.id === 'undefined'))
					{
						$('#admin_comment_wizard_textbox').val(data.Record.admin_comment);
						message_notify('The Comment Has Been Saved.', 1);
						admin_comment_changed = false;
					}
				}
				else
					message_notify(data.Message, 3);
			});
		});

		function assignment_results_show()
		{
			var assessment_id = $("#chosen-select-assessment-all").val();
			var All_Inserted = false;

			if(assessment_id == '')
			{
				message_notify("Please Ensure All Field Is Filled!", 3);
				return false;
			}
			
			$("#assignment_show_results").show();

			$("#chosen-select-uname-assignment_show_results").empty();
			$("#chosen-select-uname-assignment_show_results").append($('<option></option>').attr("value", '').text(''));
		    $("#chosen-select-uname-assignment_show_results").trigger("chosen:updated");
            $("#chosen-select-uname-assignment_show_results").chosen("{}");

            $http.get("uname.php?assessment_id="+assessment_id).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined'))
						{
							if(All_Inserted === false)
							{
								$("#chosen-select-uname-assignment_show_results").append($('<option></option>').attr("value", 0).text('ALL'));
								All_Inserted = true;
							}
							$("#chosen-select-uname-assignment_show_results").append($('<option></option>').attr("value", value.id).text(value.uname));
						}
					});
					$("#chosen-select-uname-assignment_show_results").trigger("chosen:updated");
					$("#chosen-select-uname-assignment_show_results").chosen("{}");   
				}
				else
					message_notify(response.data.Message, 3);
			});

			$("#chosen-select-chapter-assignment_show_results").empty();
			$("#chosen-select-chapter-assignment_show_results").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-chapter-assignment_show_results").trigger("chosen:updated");	
			$("#chosen-select-chapter-assignment_show_results").chosen("{}");

		}
		
		$("#chosen-select-uname-assignment_show_results").change(function() {

			var assessment_id = $("#chosen-select-assessment-all").val();
			var users_id = $("#chosen-select-uname-assignment_show_results").val();
			var users_id_tmp = '';
			var All_Inserted = false;
			var results = '<p>Comments posted by users:</p>';
			var all_select = false;

			$("#chosen-select-chapter-assignment_show_results").empty();
			$("#chosen-select-chapter-assignment_show_results").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-chapter-assignment_show_results").trigger("chosen:updated");	
			$("#chosen-select-chapter-assignment_show_results").chosen("{}");
			
			$("#show_results_user_name").html(results);
			if(assessment_id == '' || users_id == null)
			{
				return false;
			}

			users_id_tmp = users_id.toString();
			users_id = users_id_tmp.replace(/,/g, "%2C");
	
			$http.get("chapters_assigned.php?assessment_id="+assessment_id+"&users_id="+users_id).then(function(response){
              	if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined'))
						{
							if(All_Inserted === false)
							{
								$("#chosen-select-chapter-assignment_show_results").append($('<option></option>').attr("value", 0).text('ALL'));
								All_Inserted = true;
							}
							$("#chosen-select-chapter-assignment_show_results").append($('<option></option>').attr("value", value.id).text("V" + value.id + ": " + value.chapter_name));
						}
					});
					$("#chosen-select-chapter-assignment_show_results").trigger("chosen:updated");
					$("#chosen-select-chapter-assignment_show_results").chosen("{}");
				}
				else
					message_notify(response.data.Message, 3);			  
            });

			$http.get("user_comment_posted.php?assessment_id="+assessment_id+"&users_id="+users_id).then(function(response){
              	if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined'))
						{
							results += '<a href="javascript:void(0)" class="show_results_user_name_a" user_id = "' + value.id + '">' + value.uname + '</a><br/>';		
						}
					});

					$("#show_results_user_name").html(results);
					
					$('#show_results_user_comment').text('Click on the each user name to show user comment.');
					$('#show_results_user_name').show();
					$('#show_results_user_comment').show();

				}
				else
					message_notify(response.data.Message, 3);			  
            });

		});
		
		$('#show_results_user_name').on("click", ".show_results_user_name_a", function() {
			var results = '<p>' + $(this).text() + ' Comment:</p>';
			var user_id = $(this).attr('user_id');
			var assessment_id = $("#chosen-select-assessment-all").val();
			
			if(user_id == null || user_id == '' || assessment_id == null || assessment_id == '')
				return false;

			$("#show_results_user_comment").html(results);
			$http.get("user_comment.php?assessment_id=" + assessment_id + "&user_id=" + user_id).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Record;
					if(!(typeof data.id === 'undefined'))
					{
						results += data.user_comment + '<br/>';
						results = results.replace(/\r\n|\n|\r/g, '<br/>');
					}

					$("#show_results_user_comment").html(results);
				}
			});
			
		});
		
		var assignment_run = false;
		function assignment() {
			if(assignment_run === false)
			{
				$("#chosen-select-uname-assignment").empty();
				$("#chosen-select-uname-assignment").append($('<option></option>').attr("value", '').text(''));
				$("#chosen-select-uname-assignment").trigger("chosen:updated");
				$("#chosen-select-uname-assignment").chosen("{}");

				$http.get("uname.php").then(function(response){
					if("OK".localeCompare(response.data.Result) == 0)
					{
						var data = response.data.Records;
						$.each(data,function(i, value)
						{
							if(!(typeof value.id === 'undefined'))
								$("#chosen-select-uname-assignment").append($('<option></option>').attr("value", value.id).text(value.uname));
						});
						$("#chosen-select-uname-assignment").trigger("chosen:updated");
						$("#chosen-select-uname-assignment").chosen("{}");   
					}
					else
						message_notify(response.data.Message, 3);
				});

				$("#chosen-select-chapter-assignment").empty();
				$("#chosen-select-chapter-assignment").append($('<option></option>').attr("value", '').text(''));
				$("#chosen-select-chapter-assignment").append($('<option></option>').attr("value", 0).text('ALL'));
				$("#chosen-select-chapter-assignment").trigger("chosen:updated");	
				$("#chosen-select-chapter-assignment").chosen("{}");	
				$http.get("chapters.php").then(function(response){
					if("OK".localeCompare(response.data.Result) == 0)
					{
						var data = response.data.Records;
						$.each(data,function(i, value)
						{
							if(!(typeof value.id === 'undefined'))
								$("#chosen-select-chapter-assignment").append($('<option></option>').attr("value", value.id).text("V" + value.id + ": " + value.chapter_name));
						});
						$("#chosen-select-chapter-assignment").trigger("chosen:updated");	
						$("#chosen-select-chapter-assignment").chosen("{}");		  
					}
					else
						message_notify(response.data.Message, 3);
				});
				assignment_run = true;
			}
	    };
		
		var admin_comment_send = false;
		$scope.assignment_save = function() {
			var user_id = $("#chosen-select-uname-assignment").val();
			var assessment_id = $("#chosen-select-assessment-all").val();
			var chapters_id = $("#chosen-select-chapter-assignment").val();
			var chapters_id_tmp = "";
			var req = '';

			if(user_id == '' || assessment_id == '' || chapters_id == null)
			{
				message_notify('Please Fill In All Required Fields!', 3);
				return false;
			}

			if(admin_comment_send === true && admin_comment_changed === true)
			{
				var admin_comment = $("#admin_comment_wizard_textbox").val();
				admin_comment = admin_comment.replace(/\n/g, "%0D%0A");
				req = 'user_id='+user_id+'&assessment_id='+assessment_id+'&chapters_id='+chapters_id+'&admin_comment='+admin_comment+"&token="+$("#token").val();
			}
			else
				req = 'user_id='+user_id+'&assessment_id='+assessment_id+'&chapters_id='+chapters_id+"&token="+$("#token").val();

			chapters_id_tmp = chapters_id.toString();
			chapters_id = chapters_id_tmp.replace(/,/g, "%2C");

			$http.get('assignment_save.php?'+req).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					Assignment_Table();	
					uname_assignment_show();
					message_notify('The Assignment Has Been Created.', 1);
				}
				else
					message_notify(response.data.Message, 3);
			});

		};


		///////////////////////////Report//////////////////////////////////////
		var Report_run = false;
		$scope.Report = function() {
			if(Report_run === false)
			{
				
				report_list();
				Report_run = true;
			}
	    };
		
		function assessment_list(selected_id)
		{
			$("#chosen-select-assessment-report").empty();
			$("#chosen-select-assessment-report").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-assessment-report").trigger("chosen:updated");			  
			$("#chosen-select-assessment-report").chosen("{}");

			$http.get("assessment-show.php").then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined')){
							$("#chosen-select-assessment-report").append($('<option></option>').attr("value", value.id).text(value.assessment_name));
						};
					});
					$("#chosen-select-assessment-report").trigger("chosen:updated");			  
					$("#chosen-select-assessment-report").chosen("{}");
					if(!(typeof selected_id === 'undefined'))
					{
						$('#chosen-select-assessment-report').val(selected_id).trigger("chosen:updated");
						chosen_select_assessment_report_change();
					}
				}
				else
					message_notify(response.data.Message, 3);
			});
		};
		
		function report_create() {
		
			$('#report-main').hide();
			$('#report_create').show();
			report_create_init();
			assessment_list();
			$('#AllAssignmentTable-show').jtable('destroy');
		};
		
		function report_create_init()
		{
			$("#chosen-select-uname-report").empty();
			$("#chosen-select-uname-report").append($('<option></option>').attr("value", '').text(''));
		    $("#chosen-select-uname-report").trigger("chosen:updated");
            $("#chosen-select-uname-report").chosen("{}");
			
			$("#chosen-select-chapter-report").empty();
			$("#chosen-select-chapter-report").append($('<option></option>').attr("value", '').text(''));
		    $("#chosen-select-chapter-report").trigger("chosen:updated");
            $("#chosen-select-chapter-report").chosen("{}");
		}
		
		$scope.report_create_cancel = function() {

			$('#report_create').hide();
			$('#report-main').show();			
		};

		var report_create_result_type = 1;

		$("#chosen-select-assessment-report").change(function() {
			chosen_select_assessment_report_change();
		});
		
		function chosen_select_assessment_report_change()
		{
			var All_Inserted = false;
			var assessment_id = $("#chosen-select-assessment-report").val();

			if(assessment_id == '')
				return false;

			report_create_result_type = 1;
			report_create_result();

			report_create_init();

		    $http.get("uname.php?assessment_id="+assessment_id).then(function(response){
              	if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Records;
					$.each(data,function(i, value)
					{
						if(!(typeof value.id === 'undefined')){
							if(All_Inserted === false)
							{
								$("#chosen-select-uname-report").append($('<option></option>').attr("value", 0).text('ALL'));
								All_Inserted = true;
							}
							$("#chosen-select-uname-report").append($('<option></option>').attr("value", value.id).text(value.uname));
						};

					});
					$("#chosen-select-uname-report").trigger("chosen:updated");
					$("#chosen-select-uname-report").chosen("{}");

				}
				else
					message_notify(response.data.Message, 3);
			});
		};

		var All_Selected_chapter = false;
		$("#chosen-select-uname-report").change(function() {
			var All_Inserted = false;
			var users_id_tmp = '';
			var assessment_id = $("#chosen-select-assessment-report").val();
			var users_id = $("#chosen-select-uname-report").val();

			if(assessment_id == '')
				return false;
				
			if(users_id == null)
			{
				report_create_result_type = 1;
				report_create_result();
				return false;
			}
			else
			{
				report_create_result_type = 2;
				report_create_result();
			}
			
			users_id_tmp = users_id.toString();

			users_id = users_id_tmp.replace(/,/g, "%2C");

			$("#chosen-select-chapter-report").empty();
			$("#chosen-select-chapter-report").append($('<option></option>').attr("value", '').text(''));
			$("#chosen-select-chapter-report").trigger("chosen:updated");
			$("#chosen-select-chapter-report").chosen("{}");	
			
			$http.get("chapters_assigned.php?assessment_id="+assessment_id+"&users_id="+users_id).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
						var data = response.data.Records;
						$.each(data,function(i, value)
						{
							if(!(typeof value.id === 'undefined'))
							{
								if(All_Inserted === false)
								{
									$("#chosen-select-chapter-report").append($('<option></option>').attr("value", 0).text('ALL'));
									All_Inserted = true;
								}
								$("#chosen-select-chapter-report").append($('<option></option>').attr("value", value.id).text("V" + value.id + ": " + value.chapter_name));
							}
						});
						$("#chosen-select-chapter-report").trigger("chosen:updated");
						$("#chosen-select-chapter-report").chosen("{}");
				}
				else
					message_notify(response.data.Message, 3);
			});

		});
		
		
		$("#chosen-select-chapter-report").change(function() {
			var chapters_id = $("#chosen-select-chapter-report").val();

			if(chapters_id == null)
			{
				report_create_result_type = 2;
				report_create_result();
				return false;
			}
			else
			{
				report_create_result_type = 3;
				report_create_result();
			}

		});

		var report_create_rules_id = [];
		var report_create_rules_id_unchecked = [];
		var req = '';
		var save_report_0_unchecked_event = true;
		function report_create_result()
		{
			var assessment_id = $("#chosen-select-assessment-report").val();
			var users_id = $("#chosen-select-uname-report").val();
			var chapters_id = $("#chosen-select-chapter-report").val();
			var users_id_tmp = '';
			var chapters_id_tmp = '';
			var type = report_create_result_type;

			if(type == 1 && assessment_id != '')
			{
				req = 'action=assessment&assessment_id=' + assessment_id;
			}
			else if(type == 2 && assessment_id != '' && users_id != null)
			{
				users_id_tmp = users_id.toString();
				users_id = users_id_tmp.replace(/,/g, "%2C");
				req = 'action=user&assessment_id=' + assessment_id + '&users_id=' + users_id;
			}
			else if (type == 3 && assessment_id != '' && users_id != null && chapters_id != null)
			{
				users_id_tmp = users_id.toString();
				users_id = users_id_tmp.replace(/,/g, "%2C");
				
				chapters_id_tmp = chapters_id.toString();
				chapters_id = chapters_id_tmp.replace(/,/g, "%2C");
				
				req = 'action=chapter&assessment_id=' + assessment_id + '&users_id=' + users_id + '&chapters_id=' + chapters_id;
			
			}
			else
				return false;
			
			report_create_rules_id = [];

			$('#AllAssignmentTable-show').jtable('destroy');
			$('#AllAssignmentTable-show').jtable({
				title: 'Results (Please select the review you want in the report)',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'chapter_id ASC, rule_number ASC',
				actions: {
					listAction: 'report_create_show.php?'+req
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
						width: '2%'
					},
					rule_number: {
						title: 'No.',
						create: false,
						width: '2%'
					},
					title: {
						title: 'Title',
						type: 'textarea',
						create: false,
						width: '35%'
					},
					uname: {
						title: 'User',
						create: false,
						width: '10%'
					},
					PassOrFail: {
						title: 'Pass/Fail',
						type: 'radiobutton',
					    options: { '0': 'None', '1': 'Passed', '2': 'Failed' },
						defaultValue: '0',
						create: false,
						width: '5%'
					},
					comment: {
						title: 'Comment',
						type: 'textarea',
						create: false,
						width: '30%'
					},
					last_modified: {
						title: 'Last Modified',
						create: false,
						width: '10%'
					},
					rsults_checkbox: {
						title: '<input type="checkbox" class="save_report" id="0">',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<input type="checkbox" class="save_report" id="' + data.record.id + '" report_rules_id="' + data.record.report_rules_id + '">';
						}
					}
				},
				recordsLoaded: function(event, data) {
					var PageSize = data.records.length;
					var Selected = 0;
					var Page_IDs = [];

					$('.save_report').iCheck({
						checkboxClass: 'icheckbox_square-blue',
						increaseArea: '10%'
					});

					jtable_select_style();

					$.each(data.records,function(i, value)
					{
						if(Page_IDs.indexOf(value.id) == -1)
							Page_IDs.push(value.id);
					});

					$.each(data.records,function(i, value)
					{
						if(value.selected == '1' && (report_create_rules_id.indexOf(value.id) == -1))
							report_create_rules_id.push(value.id);
					});

					$.each(report_create_rules_id, function(i){
						var id = report_create_rules_id[i];
						
						$.each(Page_IDs, function(i){
							if(Page_IDs[i] === id) 
							{
								$('.save_report#'+id).iCheck('check');
								Selected++
							}
						});
					});

					if(PageSize == Selected && Selected != 0)
						$('.save_report#0').iCheck('check');
					else
						$('.save_report#0').iCheck('uncheck');

					$('.save_report').on('ifChecked', function(){
						var assessment_rule_id = $(this).attr('id');

						if(assessment_rule_id == null || assessment_rule_id == '' || assessment_rule_id == '0')
							return false;

						if(report_create_rules_id.indexOf(assessment_rule_id) == -1)
							report_create_rules_id.push(assessment_rule_id);
					});
					
					$('.save_report').on('ifUnchecked', function(){
						var assessment_rule_id = $(this).attr('id');
						var report_rules_id = $(this).attr('report_rules_id');

						if(assessment_rule_id == null || assessment_rule_id == '' || assessment_rule_id == '0')
							return false;

						$.each(report_create_rules_id, function(i){
							if(report_create_rules_id[i] === assessment_rule_id) {
								report_create_rules_id.splice(i,1);
								if(report_create_rules_id_unchecked.indexOf(report_rules_id) == -1)
									report_create_rules_id_unchecked.push(report_rules_id);
								save_report_0_unchecked_event = false;
								$('.save_report#0').iCheck('uncheck');
								return false;
							}
						});

					});
					
					$('.save_report#0')
						.on('ifChecked', function(event) {
							$('.save_report').iCheck('check');
						})
						.on('ifUnchecked', function() {
							if(save_report_0_unchecked_event === true)
								$('.save_report').iCheck('uncheck');
							else
								save_report_0_unchecked_event = true;
						});	
				}
			});
			
			$('#AllAssignmentTable-show').jtable('load');

		};

		$scope.report_update = function() {
			var assessment_rules_id = report_create_rules_id.join();
			var assessment_rules_id_unchecked = report_create_rules_id_unchecked.join();

			assessment_rules_id = assessment_rules_id.replace(/,/g, "%2C");
			assessment_rules_id_unchecked = assessment_rules_id_unchecked.replace(/,/g, "%2C");

			if(assessment_rules_id == '' && assessment_rules_id_unchecked == '')
			{
				message_notify('Please Select At Least One Review!', 3);
				return false;
			}

			$http.get('report_create_save.php?assessment_rules_id='+assessment_rules_id+'&assessment_rules_id_unchecked='+assessment_rules_id_unchecked+'&token='+$("#token").val()).then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					report_create_rules_id_unchecked = [];
					report_create_result();
					message_notify('The Report Has Been Updated.', 1);
					$('#report_create').hide();
					$('#report-main').show();
					report_list();
				}
				else
					message_notify(response.data.Message, 3);
            });
		};
		
		function report_list()
		{
			var messages_new = {
				deleteText: 'Delete Report',
				deleteConfirmation: 'This Report Will Be Deleted. Are You Sure?'
			}

			$('#AllReportTable').jtable('destroy');
			$('#report_show_results').jtable('destroy');
			$('#AllReportTable').jtable({
				messages: messages_new,
				title: 'Report',
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'assessment_name ASC',
				actions: {
					listAction: 'report.php?action=list',
					deleteAction: 'report.php?action=delete&token='+$("#token").val()
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false,
						sorting: false
					},
					assessment_name: {
						title: 'Report',
						create: false,
						edit: false,
						width: '30%'
					},
					description: {
						title: 'Description',
						type: 'textarea',
						create: false,
						width: '55%'
					},
					completed: {
						title: 'Completed',
						create: false,
						edit: false,
						sorting: false,
						width: '6%'
					},
					Report_Download_button: {
						title: '',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<button title="Download Report" class="jtable-command-button jtable-Report-Download-command-button Download_Report" assessment_id="' + data.record.id + '"><span>Download Report</span></button>';
						}
					},
					Report_show_button: {
						title: '',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<button title="Edit Review" class="jtable-command-button jtable-Report-Show-command-button Show_Report" assessment_id="' + data.record.id + '" assessment_name="' + data.record.assessment_name + '"><span>Edit Review</span></button>';
						}
					},
					Report_Edit_button: {
						title: '',
						create: false,
						edit: false,
						sorting: false,
						width: '3%',
						display: function(data) {
							return '<button title="Edit Report" class="jtable-command-button jtable-Report-Edit-command-button Edit_Report" assessment_id="' + data.record.id + '"><span>Edit Report</span></button>';
						}
					}
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				}
			});
			$('#AllReportTable').jtable('load');

			var button = '<span class="jtable-toolbar-item jtable-toolbar-item-add-record" style=" "><span class="jtable-toolbar-item-icon"></span><span class="jtable-toolbar-item-text">Add New Report</span></span>';
			$('#AllReportTable div.jtable-main-container div.jtable-title div.jtable-toolbar').html(button);		
		};

		$('#AllReportTable').on("click", ".Edit_Report", function() {
			var id = $(this).attr('assessment_id');
			$('#report-main').hide();
			$('#report_create').show();
			report_create_init();
			assessment_list(id);
		});

		$('#AllReportTable').on("click", ".Download_Report", function() {
			var id = $(this).attr('assessment_id');
			//$('#report-main').hide();
			//$('#report_download').show();
			//download_settings();
			window.open("results.php?assessment_id="+id);
		});

		$('#AllReportTable').on("click", ".Show_Report", function() {
			var id = $(this).attr('assessment_id');
			var assessment_name = $(this).attr('assessment_name');
			report_show_results(id, assessment_name);
		});

		$('#AllReportTable').on("click", ".jtable-toolbar-item-add-record", function() {
			report_create();
		});

		/*function download_settings()
		{
			$http.get("certificate/download_settings.php?action=get").then(function(response){
				if("OK".localeCompare(response.data.Result) == 0)
				{
					var data = response.data.Record;
					$('#organization_name').html(data.organization_name + '&nbsp;&nbsp;<a href="javascript:void(0)" id="organization_name_change">(change)</a>');
					$('#organization_address').html(data.organization_address + '&nbsp;&nbsp;<a href="javascript:void(0)" id="organization_address_change">(change)</a>');
					$('#logo').html(data.logo + '&nbsp;&nbsp;<a href="javascript:void(0)" id="logo_change">(change)</a>');

					$("#organization_name_change").bind("click", function(e){
						alert(2);
					});

					$("#organization_address_change").bind("click", function(e){
						alert(3);
					});

					$("#logo_change").bind("click", function(e){
						alert(4);
					});

				}
				else
					message_notify(response.data.Message, 3);
			});
		}*/

		function report_show_results(assessment_id, assessment_name)
		{
			var messages_new = {
				editRecord: 'Rule Verification',
				deleteText: 'Delete Verification',
				deleteConfirmation: 'This verification will be deleted. Are you sure?'
			}

			$('#report_show_results').jtable('destroy');
			$('#report_show_results').jtable({
				messages: messages_new,
				title: 'The results of ' + assessment_name,
				paging: true,
				pageSize: 10,
				sorting: true,
				defaultSorting: 'chapter_id ASC, rule_number ASC',
				actions: {
					listAction: 'report_result.php?action=list&assessment_id='+assessment_id,
					updateAction: 'report_result.php?action=update&token='+$("#token").val(),
					deleteAction: 'report_result.php?action=delete&token='+$("#token").val()
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
						width: '2%'
					},
					rule_number: {
						title: 'No.',
						create: false,
						edit: false,
						width: '2%'
					},
					title: {
						title: 'Title',
						type: 'textarea',
						create: false,
						edit: false,
						width: '35%'
					},
					PassOrFail: {
						title: 'Pass/Fail',
						type: 'radiobutton',
					    options: { '0': 'None', '1': 'Passed', '2': 'Failed' },
						defaultValue: '0',
						create: false,
						width: '5%'
					},
					comment: {
						title: 'Comment',
						type: 'textarea',
						create: false,
						width: '30%'
					},
					last_modified: {
						title: 'Last Modified',
						create: false,
						edit: false,
						width: '10%'
					}
				},
				recordsLoaded: function(event, data) {
					jtable_select_style();
				}
			});
			$('#report_show_results').jtable('load');
	
		};	


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
					var selected = $('input:radio[name=help' + selected_tab + ']:checked').val();
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
			$("#help0").hide();
			$("#help1").hide();
			$("#help2").hide();
			$("#help3").hide();
			$("#help4").hide();
			$("#help" + selected_tab).show();
		};

		function help_switch(selected)
		{
			switch(selected) {
				case '01':
					help_user_add();
					break;
				case '02':
					help_user_password();
					break;
				case '03':
					help_user_edit();
					break;
				case '04':
					help_user_delete();
					break;
				case '05':
					help_log_show();
					break;
				case '06':
					help_log_delete();
					break;
				case '11':
					help_rule_add();
					break;
				case '12':
					help_rule_edit();
					break;
				case '13':
					help_rule_delete();
					break;
				case '14':
					help_methodology_edit();
					break;
				case '21':
					help_assessment_add();
					break;
				case '22':
					help_assessment_edit();
					break;
				case '23':
					help_assessment_delete();
					break;
				case '24':
					help_assignment_add();
					break;
				case '25':
					help_assignment_delete()
					break;
				case '26':
					help_results_show()
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
				case '41':
					help_report_create();
					break;
				case '42':
					help_report_edit();
					break;
				case '43':
					help_review_edit();
					break;
				case '44':
					help_report_download();
					break;
				case '45':
					help_report_delete();
					break;
				default:
					message_notify('', 3);
			}
		};

		function help_user_add()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');
			
			if ($('#LogTable').is(':visible'))
			{
				$("#back_button").attr('data-step', '1');
				$("#back_button").attr('data-intro', "Click On The 'Back' Button");				
			}
			else
			{
				$("#UserTable .jtable-toolbar").attr('data-step', '1');
				$("#UserTable .jtable-toolbar").attr('data-intro', "Click On The 'Add New User' Button");
			}

			introJs().start();
		};
		
		function help_user_password()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#LogTable').is(':visible'))
			{
				$("#back_button").attr('data-step', '1');
				$("#back_button").attr('data-intro', "Click On The 'Back' Button");				
			}
			else
			{
				$("#list-uname").attr('data-step', '1');
				$("#list-uname").attr('data-intro', 'Select The User');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .Change_Password").attr('data-step', '2');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .Change_Password").attr('data-intro', "Click On The 'Change Password' Button");
			}

			introJs().start();
		};
		
		function help_user_edit()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#LogTable').is(':visible'))
			{
				$("#back_button").attr('data-step', '1');
				$("#back_button").attr('data-intro', "Click On The 'Back' Button");				
			}
			else
			{
				$("#list-uname").attr('data-step', '1');
				$("#list-uname").attr('data-intro', 'Select The User');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-step', '2');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-intro', "Click On The 'Edit User Profile' Button");
			}

			introJs().start();
		};

		function help_user_delete()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#LogTable').is(':visible'))
			{
				$("#back_button").attr('data-step', '1');
				$("#back_button").attr('data-intro', "Click On The 'Back' Button");				
			}
			else
			{
				$("#list-uname").attr('data-step', '1');
				$("#list-uname").attr('data-intro', 'Select The User');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-step', '2');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-intro', "Click On The 'Delete User' Button");
			}

			introJs().start();
		};

		function help_log_show()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#LogTable').is(':visible'))
			{
				$("#list-uname").attr('data-step', '1');
				$("#list-uname").attr('data-intro', 'Select The User');
			}
			else
			{
				$("#list-uname").attr('data-step', '1');
				$("#list-uname").attr('data-intro', 'Select The User');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-logs-command-button").attr('data-step', '2');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-logs-command-button").attr('data-intro', "Click On The 'Show User Logs' Button");
			}

			introJs().start();
		};

		function help_log_delete()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#LogTable').is(':visible'))
			{
				$("#LogTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-step', '1');
				$("#LogTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-intro', "Click On The 'Delete Log' Button");

			}
			else
			{
				$("#list-uname").attr('data-step', '1');
				$("#list-uname").attr('data-intro', 'Select The User');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-logs-command-button").attr('data-step', '2');
				$("#UserTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-logs-command-button").attr('data-intro', "Click On The 'Show User Logs' Button");
			}

			introJs().start();
		};

		function help_rule_add()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(rm === false)
			{
				$("#Rule-MethodologyTable .jtable-toolbar").attr('data-step', '1');
				$("#Rule-MethodologyTable .jtable-toolbar").attr('data-intro', "Click On The 'Add New Rule' Button");
			}
			else
			{
				$("#rules-methodology").attr('data-step', '1');
				$("#rules-methodology").attr('data-intro', "Click On The 'ASVS Rules' RadioButton");
			}

			introJs().start();
		};
		
		function help_rule_edit()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(rm === false)
			{
				$("#Rule-MethodologyTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-step', '1');
				$("#Rule-MethodologyTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-intro', "Click On The 'Edit Rule' Button That Correspond To The Desired Rule");
			}
			else
			{
				$("#rules-methodology").attr('data-step', '1');
				$("#rules-methodology").attr('data-intro', "Click On The 'ASVS Rules' RadioButton");
			}

			introJs().start();
		};
		
		function help_rule_delete()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(rm === false)
			{
				$("#Rule-MethodologyTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-step', '1');
				$("#Rule-MethodologyTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-intro', "Click On The 'Delete Rule' Button That Correspond To The Desired Rule");
			}
			else
			{
				$("#rules-methodology").attr('data-step', '1');
				$("#rules-methodology").attr('data-intro', "Click 'ASVS Rules' RadioButton");
			}

			introJs().start();
		};
		
		function help_methodology_edit()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(rm === true)
			{
				$("#Rule-MethodologyTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-step', '1');
				$("#Rule-MethodologyTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-intro', "Click On The 'Edit Methodology' Button That Correspond To The Desired Rule");
			}
			else
			{
				$("#rules-methodology").attr('data-step', '1');
				$("#rules-methodology").attr('data-intro', "Click On The 'Methodology' RadioButton");
			}

			introJs().start();
		};

		function help_assessment_add()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(assessment_select == 1)
			{
				$("#AssessmentTable .jtable-toolbar").attr('data-step', '1');
				$("#AssessmentTable .jtable-toolbar").attr('data-intro', "Click On The 'Add New Assessment' Button");
			}
			else
			{
				$("#assessment-or-assignment").attr('data-step', '1');
				$("#assessment-or-assignment").attr('data-intro', "Click On The 'Assessment' RadioButton");
			}

			introJs().start();
		};
		
		function help_assessment_edit()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(assessment_select == 1)
			{
				$("#select-assessment-all").attr('data-step', '1');
				$("#select-assessment-all").attr('data-intro', 'Select The Assessment');
				$("#AssessmentTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-step', '2');
				$("#AssessmentTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-intro', "Click On The 'Edit Assessment' Button");
			}
			else
			{
				$("#assessment-or-assignment").attr('data-step', '1');
				$("#assessment-or-assignment").attr('data-intro', "Click On The 'Assessment' RadioButton");
			}

			introJs().start();
		};
		
		function help_assessment_delete()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(assessment_select == 1)
			{
				$("#select-assessment-all").attr('data-step', '1');
				$("#select-assessment-all").attr('data-intro', 'Select The Assessment');
				$("#AssessmentTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-step', '2');
				$("#AssessmentTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-intro', "Click On The 'Delete Assessment' Button");
			}
			else
			{
				$("#assessment-or-assignment").attr('data-step', '1');
				$("#assessment-or-assignment").attr('data-intro', "Click On The 'Assessment' RadioButton");
			}

			introJs().start();
		};

		function help_assignment_add()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(assessment_select == 2)
			{
				if($('#chosen-select-assessment-all').val() != '')
				{
					$("#list-uname-assignment").attr('data-step', '1');
					$("#list-uname-assignment").attr('data-intro', 'Select A User');
					$("#list-chapter-assignment").attr('data-step', '2');
					$("#list-chapter-assignment").attr('data-intro', 'Select One Or More Chapters For Assigning Them To The User');
					$("#admin_comment").attr('data-step', '3');
					$("#admin_comment").attr('data-intro', 'Select The Checkbox And Then Enter Comment In The Corresponding Field, To Be Displayed To The User');
					$("#assignment_create_new").attr('data-step', '4');
					$("#assignment_create_new").attr('data-intro', "Select On The 'New Assignment' Button");
				}
				else
				{
					$("#select-assessment-all").attr('data-step', '1');
					$("#select-assessment-all").attr('data-intro', 'Select An Assessment');
				}
			}
			else
			{
				$("#assessment-or-assignment").attr('data-step', '1');
				$("#assessment-or-assignment").attr('data-intro', "Click On The 'Assignment' RadioButton");
			}

			introJs().start();
		};

		function help_assignment_delete()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(assessment_select == 2)
			{
				if($('#chosen-select-assessment-all').val() != '')
				{
					$("#AssignmentTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-step', '1');
					$("#AssignmentTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-intro', "Click On The 'Delete Assignment' Button That Correspond To The Desired Assignment");
				}
				else
				{
					$("#select-assessment-all").attr('data-step', '1');
					$("#select-assessment-all").attr('data-intro', 'Select An Assessment');
				}
			}
			else
			{
				$("#assessment-or-assignment").attr('data-step', '1');
				$("#assessment-or-assignment").attr('data-intro', "Click On The 'Assignment' RadioButton");	
			}

			introJs().start();
		};
		
		function help_results_show()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if(assessment_select == 3)
			{
				if($('#chosen-select-assessment-all').val() != '')
				{
					$("#list-uname-assignment_show_results").attr('data-step', '1');
					$("#list-uname-assignment_show_results").attr('data-intro', 'Select One Or More Users');
					$("#list-chapter-assignment_show_results").attr('data-step', '2');
					$("#list-chapter-assignment_show_results").attr('data-intro', 'Select One Or More Chapters');
					$("#show_results").attr('data-step', '3');
					$("#show_results").attr('data-intro', "Click On The 'Show Results' Button");
				}
				else
				{
					$("#select-assessment-all").attr('data-step', '1');
					$("#select-assessment-all").attr('data-intro', 'Select An Assessment');
				}
			}
			else
			{
				$("#assessment-or-assignment").attr('data-step', '1');
				$("#assessment-or-assignment").attr('data-intro', "Click On The 'Show Results' RadioButton");
			}

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
		
		function help_report_create()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if (!$('#report_create').is(':visible'))
			{
				$("#AllReportTable div.jtable-main-container div.jtable-title div.jtable-toolbar span.jtable-toolbar-item.jtable-toolbar-item-add-record").attr('data-step', '1');
				$("#AllReportTable div.jtable-main-container div.jtable-title div.jtable-toolbar span.jtable-toolbar-item.jtable-toolbar-item-add-record").attr('data-intro', "Click On The 'Add New Report' Button");			
			}
			else
			{
				$("#list-assessment-report").attr('data-step', '1');
				$("#list-assessment-report").attr('data-intro', 'Select An Assessment');
				$("#list-uname-report").attr('data-step', '2');
				$("#list-uname-report").attr('data-intro', 'Select One Or More Users (If Required)');
				$("#list-chapter-report").attr('data-step', '3');
				$("#list-chapter-report").attr('data-intro', 'Select One Or More Chapters (If Required)');
				if($('#chosen-select-assessment-report').val() != '')
				{
					$("#AllAssignmentTable-show div.jtable-main-container table.jtable tbody > tr:nth-child(1) > td:nth-child(8)").attr('data-step', '4');
					$("#AllAssignmentTable-show div.jtable-main-container table.jtable tbody > tr:nth-child(1) > td:nth-child(8)").attr('data-intro', "Select The Checkboxes That Correspond To The Desired Reviews");
					$("#report_Save_Cancel_button").attr('data-step', '5');
					$("#report_Save_Cancel_button").attr('data-intro', "Click On The 'Save' Button");
				}
			}

			introJs().start();
		};
		
		function help_report_edit()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if (!$('#report_create').is(':visible'))
			{
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-Report-Edit-command-button").attr('data-step', '1');
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-Report-Edit-command-button").attr('data-intro', "Click On 'Edit Report' Button That Correspond To The Desired Report");
			}
			else
			{
				$("#list-assessment-report").attr('data-step', '1');
				$("#list-assessment-report").attr('data-intro', 'Select The Assessment');
				$("#list-uname-report").attr('data-step', '2');
				$("#list-uname-report").attr('data-intro', 'Select One Or More Users (If Required)');
				$("#list-chapter-report").attr('data-step', '3');
				$("#list-chapter-report").attr('data-intro', 'Select One Or More Chapters (If Required)');
				if($('#chosen-select-assessment-report').val() != '')
				{
					$("#AllAssignmentTable-show div.jtable-main-container table.jtable tbody > tr:nth-child(1) > td:nth-child(8)").attr('data-step', '4');
					$("#AllAssignmentTable-show div.jtable-main-container table.jtable tbody > tr:nth-child(1) > td:nth-child(8)").attr('data-intro', "Select Or Unselect The Checkboxes That Correspond To The Desired Reviews");
					$("#report_Save_Cancel_button").attr('data-step', '5');
					$("#report_Save_Cancel_button").attr('data-intro', "Click On The 'Save' Button");
				}
			}

			introJs().start();
		};
		
		function help_review_edit()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#report_create').is(':visible'))
			{
				$("#report_Save_Cancel_button").attr('data-step', '1');
				$("#report_Save_Cancel_button").attr('data-intro', "Click On The 'Cancel' Button");
			}
			else
			{
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-Report-Show-command-button").attr('data-step', '1');
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-Report-Show-command-button").attr('data-intro', "Click On The 'Edit Review' Button That Correspond To The Desired Report");
				if ($('#report_show_results').text() != '')
				{
					$("#report_show_results div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-step', '2');
					$("#report_show_results div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-edit-command-button").attr('data-intro', "Click On The 'Rule Verification' Button To Edit Each Rule Verification");
				}
			}

			introJs().start();
		};
		
		function help_report_download()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#report_create').is(':visible'))
			{
				$("#report_Save_Cancel_button").attr('data-step', '1');
				$("#report_Save_Cancel_button").attr('data-intro', "Click On The 'Cancel' Button");
			}
			else
			{
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-Report-Download-command-button").attr('data-step', '1');
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-Report-Download-command-button").attr('data-intro', "Click On The 'Download Report' Button That Correspond To The Desired Report");
			}

			introJs().start();
		};
		
		function help_report_delete()
		{
			$("#welcome *").removeAttr('data-step');
			$("#welcome *").removeAttr('data-intro');

			if ($('#report_create').is(':visible'))
			{
				$("#report_Save_Cancel_button").attr('data-step', '1');
				$("#report_Save_Cancel_button").attr('data-intro', "Click On The 'Cancel' Button");
			}
			else
			{
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-step', '1');
				$("#AllReportTable div.jtable-main-container table.jtable tbody > tr:nth-child(1) .jtable-delete-command-button").attr('data-intro', "Click On The 'Delete Report' Button That Correspond To The Desired Report");
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