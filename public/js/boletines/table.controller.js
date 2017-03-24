(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('JuiciosTableCtrl',controller);

	function controller($interval, $stateParams, NivelesHasAniosFactory, opcion){
		var vm=this;

		// Variables básicas
		vm.nhaId=$stateParams.nivel;
		vm.data={};
		vm.institucion={};
	
		// Funciones basicas
		vm.recortar=recortar;

		// Lanzamiento Automático
		activate();

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function activate(){
			getDatas();
			$interval(()=>{vm.institucion=opcion.get('Organización');},1000);
		}
		function recortar(text){
			return text!='Estudiantes'? text.substring(0, 3): text;
		}
 		function getDatas(){
 			return NivelesHasAniosFactory.gNAl(vm.nhaId).then(function(res){
 				vm.data=res.data;
 			});
 		}		
	}
})();