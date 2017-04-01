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
		vm.periodosCurso=[]; // Para los Stats por curso
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
		vm.calcPromGen=calcPromGen; // Calcula el promedio de una lista de promedios.

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
 			if(id===0){
 				//console.log(vm.notastabla);
 				let varios=JSON.parse(JSON.stringify(vm.notastabla));
 				imprimeVariosBol(varios);
 			}else{
 				imprimeBol(id);
 			}

 			function imprimeBol(index,cb){
 				let miestilo=[
 				'<link rel="stylesheet" href="css/boletin.css">'
	 			];
	 			printer.div('prinZoneBoletines'+index,miestilo,cb);
	 			return true;
 			}

 			function imprimeVariosBol(arr){
 				imprimeBol(arr[0].id,(st)=>{
 					if (!st) {
 						console.error('No se imprime boletin por error.',arr[0].id)
 						return false;
 					}
 					arr.shift();
 					if(arr.length>0){
 						imprimeVariosBol(arr);
 					}else{
 						return true;
 					}
 				});
 			}
 		}
 		function calcPromGen(elm,pto){
 			if (!pto) pto='prom';
 			var prom=0, cta=0;
 			for (var i = 0; i < elm.length; i++) {
 				if (vm.periodosCurso[i].cuenta>0) {
 					prom += elm[i][pto];
 					cta++;
 				}
 			}
 			return prom/(cta>0? cta : 1);
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
 					nt.alumnos[i].periodos=[]; // Se añaden periodos al alumno
 					if(nt.alumnos[i].materias){
 						// Para guardar todos los periodos en el nuevo arreglo
 						for (var x = 0; x < nt.alumnos[i].materias[0].periodo.length; x++) {
 							nt.alumnos[i].periodos.push({
 								nombre: nt.alumnos[i].materias[0].periodo[x].nombre,
 								puesto: 0,
 								prom: 0
 							});
 						}
 					}
 					for (var j = 0; j < nt.alumnos[i].materias.length; j++) {
 						for (var k = 0; k < nt.alumnos[i].materias[j].periodo.length; k++) {
 							// Acumulamos las notas para el promedio por periodo por alumno
 							nt.alumnos[i].periodos[k].prom += nt.alumnos[i].materias[j].periodo[k].prom; 
 						}
 					}
 					for (var x = 0; x < nt.alumnos[i].periodos.length; x++) {
 						// Promedio global por periodo, por alumno
 						nt.alumnos[i].periodos[x].prom /= nt.alumnos[i].materias.length>0? nt.alumnos[i].materias.length : 1;
 					}
 				}
 				for (var r = 0; r < nt.alumnos[0].periodos.length; r++) {
 					// Inicializamos los promedios globales por curso
 					vm.periodosCurso.push({
 						nombre: nt.alumnos[0].periodos[r].nombre,
 						cuenta:0,
 						prom:0
 					});
 					// Ordenamos al alumno segun el periodo y el orden
 					nt.alumnos.sort((a,b)=>{
	 					return b.periodos[r].prom - a.periodos[r].prom;
	 				});
	 				for (var i = 0; i < nt.alumnos.length; i++) {
	 					console.log(nt.alumnos[i].periodos[r].prom);
	 					vm.periodosCurso[r].cuenta += nt.alumnos[i].periodos[r].prom;
	 				}
	 				console.log('Res,'vm.periodosCurso[r].cuenta);
	 				for (var x = 0; x < nt.alumnos.length; x++) {
	 					if (vm.periodosCurso[r].cuenta>0){
	 						nt.alumnos[x].periodos[r].puesto=x+1;
	 					}
	 				}
 				}
 				for (var i = 0; i < vm.periodosCurso.length; i++) {
 					for (var x = 0; x < nt.alumnos.length; x++) {
 						vm.periodosCurso[i].prom += nt.alumnos[x].periodos[i].prom;
 					}
 					vm.periodosCurso[i].prom /= nt.alumnos.length>0? nt.alumnos.length : 1;
 				}
 				llenarInfo();
 				vm.notastabla=nt.alumnos; // Alumnos por nivel ordenados
 			}
 		}	
 		function llenarInfo(){
 			vm.info.logo=opcion.get('Logo');
			vm.info.slogan=opcion.get('Slogan');
			vm.info.org=opcion.get('Organización');
 		}
	}
})();