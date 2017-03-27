(function(){
	'use strict';
	angular.module('escuela')
	.factory('TipoFactory',factory);

	function factory($http){
		var fc={
            url: 'api/tipo',
			getTipo: getTipo,
			getTipos: getTipos,
            addTipo: addTipo,
			modTipo: modTipo,
            delTipo: delTipo
		};

		return fc;

		/////////////////
        function getTipo(id){
            return $http.get(fc.url+'/'+id);
        }
        function getTipos(){
            return $http.get(fc.url);
        }
        function addTipo(data){
            return $http.post(fc.url,data);
        }
        function modTipo(id,data){
            return $http.post(fc.url+'/'+id,data);
        }
        function delTipo(id){
            return $http.get(fc.url+'/'+id);
        }
	}
})();