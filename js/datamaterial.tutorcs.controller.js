/*
* Controller the handles data across application
*/
(function () {
	'use strict';
	
	// the 'tutorCS' part come from teh name of the app we created in tutorcs.module.js
	var myApp = angular.module("tutorcs");
	
	// 'dataControl' is used in the HTML file when defining ng-controller attribute
	myApp.controller("materialControl", function($scope, $http, $window){
		// define profiledata for the app
        // in the html code we will refer to data.tutorCS. The data part comes from $scope.data, the moives part comes from the JSON object below
		$http.get("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/getmaterial.php") 
            .then(function(response) {
                // response.data.value has value come from the getaccounts.php file $response['value']['allinfo'] = $allinfo;
                $scope.data = response.data.value;
            });
		
		$scope.query = {};
		$scope.queryBy = "$";
		
		// function to check if user is logged in
        $scope.checkifloggedin = function() {
          $http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/isloggedin.php")
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
		
		// function to send new new content to newcontent.php
        $scope.newContent = function(materialDetails) {
			var materialUpload= angular.copy(materialDetails);
          	$http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/newcontent.php", materialUpload)
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/pages/admin_prof/viewcontent.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };  
		
	});
	
	
})();