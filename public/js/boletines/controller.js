(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('BoletinCtrl',controller);

	function controller(animPage,NivelesHasAniosFactory,AniosFactory,printer,$rootScope,opcion){
		var vm=this;

		// Variables básicas
		vm.info={
			logo:'',
			slogan:'',
			org:''
		};
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
		vm.calcNivel=calcNivel;// Genera la lista de alumnos y los lanza en ventanas emergentes.
		vm.perSel=perSel;   // Verifica si se hizo seleccion de periodo
		vm.nivelSel=nivelSel;// Verifica si se hizo seleccion de nivel
		vm.hayInfoAlumnos=hayInfoAlumnos;// Verifica si hay la información de alumnos para crear boletines
		vm.lanzaBoletin=lanzaBoletin;// Lanza los boletines listos para la impresión

		// Lanzamiento Automático
		animPage.show('boletines',function(){});
		getAnios();
		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function calcNivel(){
			vm.general=false;
			vm.calculado=false;
			vm.notas=[];
			vm.notastabla=[];
			let arr=[vm.anios[vm.sel.anio].niveles_has_anios[vm.sel.nivel]]	;
			let per=vm.anios[vm.sel.anio].periodos[vm.sel.pers].id;
			getAllNiveles(arr,per).then(()=>{
 				$rootScope.$broadcast('cargando',false);
			});
		}
 		function perSel(){
 			return vm.sel.pers>=0;
 		}
 		function nivelSel(){
 			return vm.sel.nivel>=0;
 		}
 		function hayInfoAlumnos(){
 			return vm.notastabla.length>0;
 		}
 		function lanzaBoletin(id){
 			console.log(opcion.get('Logo'));
 			console.log(opcion.get('Slogan'));
 			console.log(id);
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
 			return NivelesHasAniosFactory.gNAl(stack[0].id,per).then(function(res){
 				vm.notas.push(res.data);
 				stack.shift();
 				if (stack.length) {
 					return getAllNiveles(stack,per);
 				}else{
 					calcTheBest(vm.notas);
 					return true;
 				}
 			},
 			function (err){
 				$rootScope.$broadcast('cargando',false);
 				console.log(err);
 			});
 		}
 		function calcTheBest(notas){
 			if (notas.length) {
 				vm.calculado=true;
 				armarTabla(notas[0]);
 				return true;
 			}
 			console.log('No hay niveles para mostrar');
 			return false;

 			function armarTabla(nt){
 				for (var i = 0; i < nt.alumnos.length; i++) {
 					var acum=0;
 					for (var j = 0; j < nt.alumnos[i].materias.length; j++) {
 						for (var k = 0; k < nt.alumnos[i].materias[j].periodo.length; k++) {
 							if(nt.alumnos[i].materias[j].periodo[k].nombre==vm.anios[vm.sel.anio].periodos[vm.sel.pers].nombre){
 								acum += nt.alumnos[i].materias[j].periodo[k].prom;
 							}
 						}
 					}
 					acum /= nt.alumnos[i].materias.length;
 					nt.alumnos[i].promPer=acum;
 				}
 				nt.alumnos.sort((a,b)=>{
 					return b.promPer - a.promPer;
 				});
 				for (var i = 0; i < nt.alumnos.length; i++) {
 					nt.alumnos[i].puestoCurso=i+1;
 				}
 				vm.notastabla=nt.alumnos; // Alumnos por nivel ordenados
 				console.log(vm.notastabla); // Para eliminar
 			}
 		}	
	}
})();