/*
* Controller the handles data across application
*/
(function () {
	'use strict';
	
	// the 'tutorCS' part come from teh name of the app we created in tutorcs.module.js
	var myApp = angular.module("tutorcs");
	
	// 'dataControl' is used in the HTML file when defining ng-controller attribute
	myApp.controller("profileDataControl", function($scope, $http, $window){
		// define profiledata for the app
        // in the html code we will refer to data.tutorCS. The data part comes from $scope.data, the moives part comes from the JSON object below
		$http.get("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/getaccounts.php") 
            .then(function(response) {
                // response.data.value has value come from the getaccounts.php file $response['value']['allinfo'] = $allinfo;
                $scope.data = response.data;
            });
		$scope.sortType = 'HAWKID'; // set the default sort type
		$scope.sortReverse = false;  // set the default sort order
		$scope.searchRole = 'Student';  //  Which role do you wish to view?
		$scope.viewByCourseID = null; // define which course to display users
		$scope.searchUser = '';     // set the default search/filter term
		$scope.oneAtATime = true; // used to only display one row at a time
		
		
		// function to delete a player. it receives the player's name and id and call a php web api to complete deletion from the database
        $scope.deleteUser = function(FIRSTNAME, HAWKID) {
            if (confirm("Are you sure you want to delete " + FIRSTNAME + "?")) {
          
                $http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/deleteuser.php", {"HAWKID":HAWKID})
                  .then(function (response) {
                     if (response.status == 200) {
                          if (response.data.status == 'error') {
                              alert('error: ' + response.data.message);
                          } else {
                              // successful
                              // send user back to home page
                              $window.location.href = "https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/pages/admin_prof/viewroles.html";
                          }
                     } else {
                          alert('unexpected error');
                     }
                  }
                );
            }
        };
		
		
		// function to re-enable a user. it receives the users's name and HAWKID and calls a php web api to reset permissions from the database
        $scope.enableUser = function(FIRSTNAME, HAWKID) {
            if (confirm("Are you sure you want to enable " + FIRSTNAME + "?")) {
          
                $http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/enableuser.php", {"HAWKID":HAWKID})
                  .then(function (response) {
                     if (response.status == 200) {
                          if (response.data.status == 'error') {
                              alert('error: ' + response.data.message);
                          } else {
                              // successful
                              // send user back to home page
                              $window.location.href = "https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/pages/admin_prof/viewroles.html";
                          }
                     } else {
                          alert('unexpected error');
                     }
                  }
                );
            }
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
		
		
		// function to remove an allocated tutoring session from a user's enrollment in a course
        $scope.removeAllocSession = function(HAWKID, COURSE_ID) {
			$http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/removeallocsession.php", {"HAWKID":HAWKID, "COURSE_ID":COURSE_ID})
			  .then(function (response) {
				 if (response.status == 200) {
					  if (response.data.status == 'error') {
						  alert('error: ' + response.data.message);
					  } else {
						  // successful
						  // send user back to home page
						  $window.location.href = "https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/pages/admin_prof/courselist.html";
					  }
				 } else {
					  alert('unexpected error');
				 }
			  }
			);
            
        };
		
		// function to add an allocated tutoring session from a user's enrollment in a course
        $scope.addAllocSession = function(HAWKID, COURSE_ID) {
			$http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/addallocsession.php", {"HAWKID":HAWKID, "COURSE_ID":COURSE_ID})
			  .then(function (response) {
				 if (response.status == 200) {
					  if (response.data.status == 'error') {
						  alert('error: ' + response.data.message);
					  } else {
						  // successful
						  // send user back to home page
						  $window.location.href = "https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/pages/admin_prof/courselist.html";
					  }
				 } else {
					  alert('unexpected error');
				 }
			  }
			);
            
        };
		
		
		// function to send new account information to web api to add it to the database
        $scope.enrollStudent = function(enrollmentDetails) {
			var enrollmentUpload = angular.copy(enrollmentDetails);
          	$http.post("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/enrollstudent.php", enrollmentUpload)
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/pages/admin_prof/home.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });                        
        };  
		
		
		
		
		

	});
})();