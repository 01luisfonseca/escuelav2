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
		vm.notastabla=[];
		vm.gen={}; // Titulo y otros de la tabla
		vm.sel={
			anio: -1,
            pers: -1,
            nivel: -1
		};
		vm.calculado=false;
	
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
			vm.calculado=false;
			vm.notas=[];
			vm.notastabla=[];
			let arr=[vm.anios[vm.sel.anio].niveles_has_anios[vm.sel.nivel]]	;
			let per=vm.anios[vm.sel.anio].periodos[vm.sel.pers].id;
			return getAllNiveles(arr,per);
		}
		function calcGen(){
			vm.calculado=false;
			vm.notas=[];
			vm.notastabla=[];
			let arr=vm.anios[vm.sel.anio].niveles_has_anios.slice();
			let per=vm.anios[vm.sel.anio].periodos[vm.sel.pers].id;
 			return  getAllNiveles(arr,per);
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
 		function getAllNiveles(stack,per){
 			if (!stack.length) return console.log('Longitud no permitida');
 			$rootScope.$broadcast('cargando',true);
 			return NivelesHasAniosFactory.gNPAl(stack[0].id,per).then(function(res){
 				vm.notas.push(res.data);
 				stack.shift();
 				if (stack.length) {
 					return getAllNiveles(stack,per);
 				}else{
 					$rootScope.$broadcast('cargando',false);
 					calcTheBest(vm.notas);
 					return true;
 				}
 			});
 		}
 		function calcTheBest(notas){
 			if (notas.length) {
 				vm.calculado=true;
 				if (notas.length>1) {
 					armarTabla(notas,'multiple');
 				}else{
 					armarTabla(notas);
 				}
 				return true;
 			}
 			console.log('No hay niveles para mostrar');
 			return false;

 			function armarTabla(nt,type){
 				vm.gen={
 					titulo: type? 'CURSO: '+nt[0].curso: 'GENERAL DEL COLEGIO',
 					anio: nt[0].anio,
 				};
 				if (type) {
 					console.log('General',nt);
 				}else{
 					vm.notastabla=nt[0];
 				}
 			}
 		}	
	}
})();