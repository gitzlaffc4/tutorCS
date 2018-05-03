/*
 *Controller where we get the data
 */
(function () {
    'use strict';
    
    // the 'avail' part comes from the name of the app we created in avail.module.js
    var myApp = angular.module("tutorcs");
    // 'availControl' is used in the html file when defining the ng-controller attribute
    myApp.controller("availControl", function($scope, $http, $window) {
        $http.get('https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/getavailablesessions.php')
            .then(function(response) {
                $scope.data = response.data.value;
            }
                   );        
        
      
      
        /*
         *    Code for search bar
         *    With queryBy you can say which attribute you want to search under. For example if it is "name" it will only search under names. If you want to search under everything, then use "$"
         *    We are assuming there is an input element with an ng-model="query[queryBy]"
         *    We are also assuming that you are filtering through this query when you get data under ng-repeat
         */
        $scope.query = {};
        $scope.queryBy = "$";
		
		// function to send new availability to the web api to add it to the database
       	$scope.newAvail = function(availDetails) {
			var availupload = angular.copy(availDetails);
			
			$http.post("../script/newavail.php", availupload)
				.then(function (response) {
					if (response.status == 200){
						if (response.data.status == 'error') {
							alert('error: ' + response.data.message);
						}else{
							$window.location.href = "../pages/tutor/avail.html";
						}
					} else {
						alert('unexpected error');
					}
				
			});
		};
		
		$scope.deleteSession = function(HAWKID, AVAILSLOTID) {
			if (confirm("Are you sure you want to cancel the session?")) {
				
				$http.post('https://webdev.cs.uiowa.edu/~cgitzlaff/tutorCS/script/tutorcancelsession.php', {"AVAILSLOTID" : AVAILSLOTID})
					.then(function (response) {
						if(response.status == 200){
							if (response.data.status == 'error'){
								alert('error: ' + response.data.message);
							} else {
								//successful
								// send user back to avail.html
								$window.location.href = "pages/tutor/avail.html";
							}
						} else {
							alert('unexpected error 2');
						}
					}
				 );
			}
			
		};

    });


})();