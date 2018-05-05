/*
* Controller the handles data across application
*/
(function () {
	'use strict';
	
	// the 'tutorCS' part come from teh name of the app we created in tutorcs.module.js
	var myApp = angular.module("tutorcs");
	
	// 'dataControl' is used in the HTML file when defining ng-controller attribute
	myApp.controller("studSchedControl", function($scope, $http, $window){
		// define profiledata for the app
        // in the html code we will refer to data.tutorCS. The data part comes from $scope.data, the moives part comes from the JSON object below
		$http.get("https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/studentscheduled.php") 
            .then(function(response) {
                // response.data.value has value come from the getaccounts.php file $response['value']['allinfo'] = $allinfo;
                $scope.data = response.data.value;
            });
		
		$scope.query = {};
		$scope.queryBy = "$";
		
	});
})();