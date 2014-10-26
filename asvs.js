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
		
		$("#uname").focus();
		
		$scope.login = function() {
		    var un;
			var user_name = $("#uname").val();
			var password = $("#password").val();

			if(user_name == '' || password == '')
			{
				server_message('Please Ensure All Fields Are Filled!');
				return false;
			}
			
			$("#uname").val('');
			$("#password").val('');

			$.post("login.php",
			{
				uname:user_name,
				password:password
			},
			function(data,status){
				var data2;
				if(!(typeof data === 'undefined'))
				{
					data2 = jQuery.parseJSON(data);
					if("OK".localeCompare(data2.Result) == 0)
						window.location.reload();
					else
						server_message(data2.Message);
				}
				else
					server_message();
			});

		};
		
		var message_ok = false;
		$('#uname').keyup(function(e){
			if(message_ok === false)
			{
				if(e.keyCode == 13)
				{
					$("#password").focus();
				}
			}
			else
				message_ok = false;
		});
		
		$('#password').keyup(function(e){
			if(e.keyCode == 13)
			{
				$scope.login();
			}
		});

		function server_message(message)
		{
			var message_shown = '';
			if((typeof message === 'undefined'))
				message_shown = "An Unknown Error Has Been Occurred.";
			else
				message_shown = message;

			$("#server_message").text(message_shown);

			$("#server_message_shown").dialog({
				resizable: false,
				modal: true,
				close: uname_focus,
				buttons: {
					Ok: function() {
						message_ok = true;
						$( this ).dialog( "close" );
					}
				}
			});
		};
		
		function uname_focus()
		{
			$("#uname").focus();
		};
});