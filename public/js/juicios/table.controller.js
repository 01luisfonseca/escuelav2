(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('JuiciosTableCtrl',controller);

	function controller($stateParams, NivelesHasAniosFactory){
		var vm=this;

		// Variables básicas
		vm.nhaId=$stateParams.nivel;
	
		// Funciones basicas

		// Lanzamiento Automático

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
 		function getAnios(){
 			return AniosFactory.gDts().then(function(res){
 				vm.anios=res.data;
 			});
 		}		
	}
})();