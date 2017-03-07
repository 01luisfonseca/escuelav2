(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('HonorCtrl',controller);

	function controller(animPage,IngreyEgreFactory,NivelesHasAniosFactory,AniosFactory,$rootScope){
		var vm=this;

		// Variables básicas
		vm.anios=[];
		vm.notas=[];
		vm.sel={
			anio: -1,
            pers: -1,
            nivel: -1
		};
	
		// Funciones basicas
		vm.calcGen=calcGen; // Obtiene todas las notas de todos los niveles
		vm.calcNivel=calcNivel;// Obtiene la tabla de un nivel.
		vm.perSel=perSel;   // Verifica si se hizo seleccion de periodo
		vm.nivelSel=nivelSel;// Verifica si se hizo seleccion de nivel

		// Lanzamiento Automático
		animPage.show('honor',function(){});
		getAnios();
		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function calcNivel(){
			vm.notas=[];
			var arr=vm.anios[vm.sel.anio].niveles_has_anios.splice(vm.sel.nivel, 1);
			return getAllNiveles(arr);
		}
		function calcGen(){
			vm.notas=[];
			var arr=JSON.parse(JSON.stringify(vm.anios[vm.sel.anio].niveles_has_anios));
 			return getAllNiveles(arr);
 		}
 		function perSel(){
 			return vm.sel.pers>=0;
 		}
 		function nivelSel(){
 			return vm.sel.nivel>=0;
 		}
 		function getAnios(){
 			$rootScope.$broadcast('cargando',true);
 			return AniosFactory.gDts().then(function(res){
 				$rootScope.$broadcast('cargando',false);
 				vm.anios=res.data;
 			});
 		}
 		function getAllNiveles(stack){
 			if (!stack.length) return console.log('Longitud no permitida');
 			$rootScope.$broadcast('cargando',true);
 			return NivelesHasAniosFactory.gNAl(stack[0].id).then(function(res){
 				vm.notas.push(res.data);
 				stack.shift();
 				if (stack.length) {
 					return getAllNiveles(stack);
 				}else{
 					$rootScope.$broadcast('cargando',false);
 					calcTheBest(vm.notas);
 					return true;
 				}
 			});
 		}
 		function calcTheBest(notas){
 			console.log(notas);
 		}	
	}
})();