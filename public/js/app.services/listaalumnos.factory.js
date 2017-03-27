(function(){
	'use strict';
	angular.module('escuela')
	.factory('ListaAlumnosFactory',factory);

	function factory($http){
		var fc={
            url: 'api/listados/alumnos',
			gDt: gDt,
			gDts: gDts,
            gEDt:gEDt,
		};

		return fc;

		/////////////////
        function gDt(id){
            return $http.get(fc.url+'/'+id);
        }
        function gDts(){
            return $http.get(fc.url);
        }
        function gEDt(texto){
            return $http.get(fc.url+'/exportar/'+texto, { responseType: "arraybuffer" });
        }
	}
})();