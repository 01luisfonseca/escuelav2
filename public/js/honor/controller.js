(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('HonorCtrl',controller);

	function controller(animPage,IngreyEgreFactory,AniosFactory){
		var vm=this;

		// Variables básicas
		vm.anios=[];
		vm.sel={
			anio: -1,
            nivel: -1
		};
	
		// Funciones basicas

		// Lanzamiento Automático
		animPage.show('honor',function(){});
		getAnios();
		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
 		function getAnios(){
 			return AniosFactory.gDts().then(function(res){
 				vm.anios=res.data;
 			});
 		}		
	}
})();