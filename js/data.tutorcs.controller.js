/*
* Controller the handles data across application
*/
(function () {
	'use strict';
	
	// the 'tutorCS' part come from teh name of the app we created in tutorcs.module.js
	var myApp = angular.module("tutorcs");
	
	// 'dataControl' is used in the HTML file when defining ng-controller attribute
	myApp.controller("dataControl", function($scope, $http){
		// this variable will hold the page number that should be highlighted in the menu bar
        // 0 is for index.html
        $scope.menuHighlight = 0;
	})
})();