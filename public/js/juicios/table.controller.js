(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('JuiciosTableCtrl',controller);

	function controller($stateParams, NivelesHasAniosFactory){
		var vm=this;

		// Variables básicas
		vm.nhaId=$stateParams.nivel;
		vm.data={};
	
		// Funciones basicas

		// Lanzamiento Automático
		activate();

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function activate(){
			getDatas();
		}
 		function getDatas(){
 			return NivelesHasAniosFactory.gNAl(vm.nhaId).then(function(res){
 				vm.data=res.data;
 				console.log(vm.data);
 			});
 		}		
	}
})();