(function(){
	'use strict';
	angular.module('escuela')
	.factory('IngreyEgreFactory',factory);

	function factory($http){
		var fc={
            url: '/api/ingreyegre',
			eDt: eDt,
			gDts: gDts,
		};

		return fc;

		/////////////////
        function eDt(anio,mes){
            return $http.get(fc.url+'/'+anio+'/'+mes);
        }
        function gDts(anio){
            return $http.get(fc.url+'/'+anio);
        }      
	}
})();