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
          var accountupload = angular.copy(accountDetails);
          
          $http.post("../script/newaccount.php", accountupload)
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
	})
	
	
})();