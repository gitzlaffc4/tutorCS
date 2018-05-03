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
		$scope.searchUser = '';     // set the default search/filter term
		$scope.oneAtATime = true; // used to only display one row at a time
	
	
		/*
		 * Set edit mode of a particular user
		 * on is true if we are setting edit mode to be on, false otherwise
		 * user corresponds to the user for which we are setting an edit mode
		 */
		// $scope.setEditMode = function(on, user) {
		//	if (on) {
		//		// editplayer matches the ng-model used in the form we use to edit player information
		//		$scope.edituser = angular.copy(user);
		//		player.editMode = true;
		//	} else {
		//		// if editplayer is null we assume no player is currently being edited
		//		$scope.edituser = null;
		//		player.editMode = false;
		//	}
		//};


		/*
		 * Gets the edit mode for a user player
		 */
		//$scope.getEditMode = function(user) {
		//	return user.editMode;
		//};
	
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

	});
})();