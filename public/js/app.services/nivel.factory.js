(function(){
	'use strict';
	angular.module('escuela')
	.factory('NivelesFactory',factory);

	function factory($http){
		var fc={
            url: '/api/niveles',
			gDt: gDt,
			gDts: gDts,
            gSDt:gSDt,
            gRDts: gRDts,
            aDt: aDt,
			mDt: mDt,
            dDt: dDt
		};

		return fc;

		/////////////////
        function gDt(id){
            return $http.get(fc.url+'/'+id);
        }
        function gDts(){
            return $http.get(fc.url);
        }
        function aDt(data){
            return $http.post(fc.url,data);
        }
        function mDt(id,data){
            return $http.put(fc.url+'/'+id,data);
        }
        function dDt(id){
            return $http.delete(fc.url+'/'+id);
        }      
	}
})();