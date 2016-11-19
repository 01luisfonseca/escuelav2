(function(){
	'use strict';
	angular.module('escuela')
	.factory('RestFactory',factory);

	function factory($http){
		var fc={
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
        function gDt(url,id){
            return $http.get(url+'/'+id);
        }
        function gDts(url,){
            return $http.get(url);
        }
        function gSDt(url,texto){
            return $http.get(url+'/search/'+texto);
        }
        function gRDts(url,first){
            return $http.get(url+'/range/'+first);
        }
        function aDt(url,data){
            return $http.post(url,data);
        }
        function mDt(url,id,data){
            return $http.put(url+'/'+id,data);
        }
        function dDt(url,id){
            return $http.delete(url+'/'+id);
        }      
	}
})();