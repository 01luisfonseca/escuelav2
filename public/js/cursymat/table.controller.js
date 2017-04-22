(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('MatYPenTableCtrl',controller);

	function controller(Saver, $location){
		var vm=this;

		// Variables básicas
		vm.selecData={};
			
		// Funciones basicas
		vm.cargaInfo=cargaInfo;
		vm.calcDiscrim=calcDiscrim;
		vm.calcfiltro=calcfiltro;
		vm. calcNivel=calcNivel;
		
		// Lanzamiento Automático
		vm.cargaInfo();
		
		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function cargaInfo(){
			vm.selecData=Saver.getData('matypen');
			console.log(vm.selecData);
			if (!vm.selecData) {
				$location.path('/authhome');
			}else{
				Saver.delData('matypen');
			}
		}
		function calcDiscrim(){
			if (vm.selecData.tipo=='pen') {
				return 'pensiones';
			}
			return 'matrículas';
		}
		function calcfiltro(){
			return vm.selecData.clase;
		}
		function calcNivel(){
			return vm.selecData.name;
		}
	}
})();