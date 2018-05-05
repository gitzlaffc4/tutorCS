/*
* Controller the handles data across application
*/
(function () {
	'use strict';
	
	// the 'tutorCS' part come from teh name of the app we created in tutorcs.module.js
	var myApp = angular.module("tutorcs");
	
	// 'dataControl' is used in the HTML file when defining ng-controller attribute
	myApp.controller("dataControl", function($scope, $http, $window){
		// define profiledata for the app
        // in the html code we will refer to data.tutorCS. The data part comes from $scope.data, the moives part comes from the JSON object below
  		$http.get("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/getprofileinfo.php") 
            .then(function(response) {
                //response.data.value has value come from  the getprofileinfo.php file $response['value']['userinfo'] = $userinfo;
                $scope.data = response.data.value;
             });
		

		$scope.query = {};
		$scope.queryBy = "$";
		
		
		// this variable will hold the page number that should be highlighted in the menu bar
        // 0 is for index.html
		// 1 is for 
        $scope.menuHighlight = 0;
		
		// function to send new account information to web api to add it to the database
        $scope.newAccount = function(accountDetails) {
          	var accountupload = angular.copy(accountDetails);
          	$http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/newaccount.php", accountupload)
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "pages/admin_prof/newaccount.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };  
		
		// function to log the user in
		$scope.login = function(accountDetails) {
          var accountupload = angular.copy(accountDetails);
          $http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/login.php", accountupload)
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
					}
				   	// successful
					// send user to correct landing page
					if ((response.data.student == '1') && (response.data.tutor == '1')){
						$window.location.href = '../index.html';
					}
				   	if ((response.data.professor == '1') && (response.data.admin == '1')){
						$window.location.href = 'admin_prof/homehtml';
					}
				   	if (response.data.student == '1'){
						$window.location.href = 'student/home.html';
					}
				   	if (response.data.tutor == '1'){
						$window.location.href = 'tutor/home.html';
					}
				   	if (response.data.professor == '1'){
						$window.location.href = 'admin_prof/home.html';
					}
				   	if (response.data.admin == '1'){
						$window.location.href = 'admin_prof/home.html';
					}
				} else {
                    alert('unexpected error');
               }
            });                        
        };
        
        
        // function to log the user out
        $scope.logout = function() {
          $http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/logout.php")
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "../tutorCS/index.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };             
        
        // function to check if user is logged in
        $scope.checkifloggedin = function() {
          $http.post("script/isloggedin.php")
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // set $scope.isloggedin based on whether the user is logged in or not
                        $scope.isloggedin = response.data.loggedin;
						$scope.currentHawkID = response.data.HAWKID;
						$scope.isadmin = response.data.admin;
						$scope.isprofessor = response.data.professor;
						$scope.istutor = response.data.tutor;
						$scope.isstudent = response.data.student;
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };
	});
})();