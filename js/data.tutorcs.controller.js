/*
* Controller the handles data across application
*/
(function () {
	'use strict';
	
	// the 'tutorCS' part come from teh name of the app we created in tutorcs.module.js
	var myApp = angular.module("tutorcs");
	
	// 'dataControl' is used in the HTML file when defining ng-controller attribute
	myApp.controller("dataControl", function($scope, $http, $window){
		
		
		// this variable will hold the page number that should be highlighted in the menu bar
        // 0 is for index.html
        $scope.menuHighlight = 0;
		
		// function to send new account information to web api to add it to the database
        $scope.newAccount = function(accountDetails) {
<<<<<<< HEAD
          var accountupload = angular.copy(accountDetails);
          
          $http.post("../script/newaccount.php", accountupload)
=======
          	var accountupload = angular.copy(accountDetails);
          	$http.post("../script/newaccount.php", accountupload)
>>>>>>> 81f46cd9c3a23c0d0cf4989ebd04dccf08167da8
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "newaccount.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };  
		
		// function to log the user in
		$scope.login = function(accountDetails) {
          var accountupload = angular.copy(accountDetails);
          $http.post("../script/login.php", accountupload)
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "../index.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };
        
        
        // function to log the user out
        $scope.logout = function() {
          $http.post("logout.php")
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "../index.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };             
        
        // function to check if user is logged in
        $scope.checkifloggedin = function() {
          $http.post("isloggedin.php")
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // set $scope.isloggedin based on whether the user is logged in or not
                        $scope.isloggedin = response.data.loggedin;
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };  
	});
	
	
})();