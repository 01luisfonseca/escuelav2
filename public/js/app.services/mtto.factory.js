(function(){
	'use strict';
	angular.module('escuela')
	.factory('MttoFactory',factory);

	function factory($http){
		var fc={
            url: 'api/mantenimiento',
			gDt: gDt
		};

		return fc;

		/////////////////
        function gDt(sect){
            return $http.get(fc.url+'/'+sect);
        }
	}
})();