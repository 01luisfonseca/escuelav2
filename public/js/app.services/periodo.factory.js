(function(){
	'use strict';
	angular.module('escuela')
	.factory('PeriodosFactory',factory);

	function factory($http){
        var url= 'api/periodos';
		var fc={
			gDt: gDt,
			gDts: gDts,
            gSDt:gSDt,
            gRDts: gRDts,
            aDt: aDt,
			mDt: mDt,
            dDt: dDt,
            gEFac:gEFac
		};

		return fc;

		/////////////////
        function gDt(id){
            return $http.get(url+'/'+id);
        }
        function gDts(){
            return $http.get(url);
        }
        function gSDt(texto){
            return $http.get(url+'/search/'+texto);
        }
        function gRDts(first){
            return $http.get(url+'/range/'+first);
        }
        function aDt(data){
            return $http.post(url,data);
        }
        function mDt(id,data){
            return $http.put(url+'/'+id,data);
        }
        function dDt(id){
            return $http.delete(url+'/'+id);
        }
        function gEFac(fac){
            return $http.delete(url+'/fac/'+fac);
        }       
	}
})();