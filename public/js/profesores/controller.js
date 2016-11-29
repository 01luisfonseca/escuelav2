(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('ProfesorCtrl',controller);

	function controller(animPage, AniosFactory){
		var vm=this;

		// Variables básicas
		vm.anios={};

		// Variables adicionales
	
		// Funciones basicas
		vm.getAnios=getAnios;

		// Funciones adicionales

		// Lanzamiento Automático
		animPage.show('profesores',function(){});
		vm.getAnios();

		/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function getAnios(){
			return AniosFactory.gDts().then(function(res){
				vm.anios=res;
			});
		}
	}
})();