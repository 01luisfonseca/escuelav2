(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('TirillaCajaCtrl',controller);

	function controller(Saver, $location, opcion){
		var vm=this;

		// Variables básicas
		vm.info={};
		vm.ahora=new Date();
	
		// Funciones basicas
		vm.cargaInfo=cargaInfo;
		vm.buscarOpt=buscarOpt;

		// Lanzamiento Automático
		vm.cargaInfo();

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function cargaInfo(){
			vm.info=Saver.getData('liqcaja');
			console.log(vm.info);
			if (!vm.info) {
				$location.path('/authhome');
			}else{
				Saver.delData('liqcaja');
			}
		}
		function buscarOpt(name){
			return opcion.get(name);
		}
	}
})();