(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('MatYPenCtrl',controller);

	function controller(animPage,NivelesHasAniosFactory,AniosFactory,MesesFactory, Saver, $timeout, $window){
		var vm=this;

		// Variables básicas
		vm.selecData={};
		vm.ahora=new Date();
		vm.ahora=moment(vm.ahora);
		vm.limite={
			mora: 45,
			retraso: 35
		};
		vm.status={};
		vm.datosRaw=[];
		vm.cargando=false;
		vm.anios=[];
		vm.meses=[];
		vm.sel={
			anio: 0,
			mes: 0,
			type: 'pen',
			tabla: false
		};
	
		// Funciones basicas
		vm.cambioAnio=cambioAnio;
		vm.cambioMes=cambioMes;
		vm.mostrarGraficos=mostrarGraficos;


		// Lanzamiento Automático
		animPage.show('matypen',function(){});
		initObjeto();
		getAnios();
		getMeses();

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
 		function initObjeto(){
 			vm.status={
				mat:{
					name:['Al día', 'Retrasados', 'Morosos'],
					value:[0,0,0],
					niveles:[
						{
							tipo: 'Al día',
							ids:[],
							name: [],
							value: [],
							levels:[]
						},
						{
							tipo: 'Retrasados',
							ids:[],
							name: [],
							value: [],
							levels:[]
						},
						{
							tipo: 'Morosos',
							ids:[],
							name: [],
							value: [],
							levels:[]
						}
					]
				},
				pen:{
					name:['Al día', 'Retrasados', 'Morosos'],
					value:[0,0,0],
					niveles:[
						{
							tipo: 'Al día',
							ids:[],
							name: [],
							value: [],
							levels:[]
						},
						{
							tipo: 'Retrasados',
							ids:[],
							name: [],
							value: [],
							levels:[]
						},
						{
							tipo: 'Morosos',
							ids:[],
							name: [],
							value: [],
							levels:[]
						}
					]
				}
			};
 		}
 		function getAnios(){
 			return AniosFactory.gDts().then(function(res){
 				vm.anios=res.data;
 			});
 		}
 		function getMeses(){
 			return MesesFactory.gDts().then(function(res){
 				vm.meses=res.data;
 			});
 		}
 		function cambioAnio(){
 			initObjeto();
 			return NivelesHasAniosFactory.gPAl(vm.sel.anio).then(function(res){
 				vm.datosRaw=res.data;
 				calcularDatos(res.data);
 				mostrarGraficos();
 			});
 		}
 		function cambioMes(){
 			initObjeto();
 		}
 		function mostrarGraficos(){
 			vm.sel.tabla= false;
 			vm.selecData= {};
 			var tipo;
 			if (vm.sel.type=='pen') {
 				dibujaGrafico('Estado de pensiones según el año.',vm.status.pen,llamadaPension);
 			}else{
 				dibujaGrafico('Estado de matrículas según el año.',vm.status.mat,llamadaMatricula);
 			}
 			/////////////////////////////////////
 			function llamadaPension(e){
 				tipo=e.dataPoint.label;
 				var index=verTipo(tipo);
    			dibujaGrafico('Resultados de pensiones por: '+e.dataPoint.label,vm.status.pen.niveles[index],tablaExportar);
 			}
 			function llamadaMatricula(e){
 				tipo=e.dataPoint.label;
 				var index=verTipo(tipo);
    			dibujaGrafico('Resultados de matrículas por: '+e.dataPoint.label,vm.status.pen.niveles[index],tablaExportar);
 			}
 			function tablaExportar(e){
 				var sel=e.dataPoint.label;
 				exportarLista(sel, tipo, vm.sel.type);
 			}
 			function verTipo(tipo){
    			switch(tipo){
    				case 'Al día':
    					return 0;
    					break;
   					case 'Retrasados':
   						return 1;
   						break;
    				case 'Morosos':
    					return 2;
    					break;
  				}
 			}
 			function exportarLista(nivel, clase, tipo){
 				vm.sel.tabla=true;
 				for (var i = 0; i < vm.status[tipo].name.length; i++) {
 					if(vm.status[tipo].name[i]==clase){
 						for (var j = 0; j < vm.status[tipo].niveles[i].levels.length; j++) {
 							if(vm.status[tipo].niveles[i].levels[j].name==nivel){
 								vm.status[tipo].niveles[i].levels[j].tipo=tipo;
 								vm.status[tipo].niveles[i].levels[j].clase=vm.status[tipo].name[i];
 								Saver.setData('matypen',vm.status[tipo].niveles[i].levels[j]); // Parece que hay que implementar promesas ya que en la primera no funciona.
								$timeout(function(){
									$window.open('/#/matypentable'); 
								},500);
 							}
 						}
 						
 					}
 				}
			}
 		}
 		function calcularDatos(dts){
 			for (var i = 0; i < dts.length; i++) {
 				for (var x = 0; x < 3; x++) { // Agrega la información básica de los arreglos. No calcula
 					vm.status.mat.niveles[x].ids.push(dts[i].id);
 					vm.status.mat.niveles[x].name.push(dts[i].niveles.nombre);
 					vm.status.mat.niveles[x].value.push(0);
 					vm.status.mat.niveles[x].levels.push({ id: dts[i].id, name: dts[i].niveles.nombre, alumnos: []});
 					vm.status.pen.niveles[x].ids.push(dts[i].id);
 					vm.status.pen.niveles[x].name.push(dts[i].niveles.nombre);
 					vm.status.pen.niveles[x].value.push(0);
 					vm.status.pen.niveles[x].levels.push({ id: dts[i].id, name: dts[i].niveles.nombre, alumnos: []});
 				}
 				for (var j = 0; j < dts[i].alumnos.length; j++) {// Acumulando el alumno según su tipo.
 					var pagoMat=verifyPago(dts[i].alumnos[j].pago_matricula);
 					var pagoPen=verifyPago(dts[i].alumnos[j].pago_pension, 'pen');
 					vm.status.mat.niveles[pagoMat].value[i]+=1;
 					vm.status.pen.niveles[pagoPen].value[i]+=1;
 					var alu=dts[i].alumnos[j], obj=undefined;
 					if (alu.name) {
 						obj={
 							name: alu.name, 
 							lastname: alu.lastname, 
 							identificacion: alu.identificacion,
 							valor:0,
 							fecha:0
 						};
 					}
 					if (vm.status.mat.niveles[pagoMat].levels[i]) {
 						obj.fecha=dts[i].alumnos[j].pago_matricula[0]? dts[i].alumnos[j].pago_matricula[0].created_at : 0;
 						obj.valor=dts[i].alumnos[j].pago_matricula[0]? dts[i].alumnos[j].pago_matricula[0].valor : 0;
 						vm.status.mat.niveles[pagoMat].levels[i].alumnos.push(obj);
 					}
 					if (vm.status.pen.niveles[pagoPen].levels[i]) {
 						obj.fecha=dts[i].alumnos[j].pago_pension[0]? dts[i].alumnos[j].pago_pension[0].created_at : 0;
 						obj.valor=dts[i].alumnos[j].pago_pension[0]? dts[i].alumnos[j].pago_pension[0].valor : 0;
 						vm.status.pen.niveles[pagoPen].levels[i].alumnos.push(obj);
 					}
 				}
 			}
 			promediarRes();
 		}
 		function promediarRes(){
 			for (var i = 0; i < vm.status.pen.niveles.length; i++) {
 				for (var j = 0; j < vm.status.pen.niveles[i].value.length; j++) {
 					vm.status.pen.value[i]+=vm.status.pen.niveles[i].value[j];
 				}
 				for (var j = 0; j < vm.status.mat.niveles[i].value.length; j++) {
 					vm.status.mat.value[i]+=vm.status.mat.niveles[i].value[j];
 				}
 			}
 		}
 		function verifyPago(obj, type){
 			if (obj.length) {
 				var pago=moment(obj[0].created_at);
 				if (type=='pen') {
 					var fecha=new Date(vm.datosRaw[0].anios.anio, obj[0].mes_id, 1, 0, 0, 0, 0);
 					pago=moment(fecha);
 				}else{
 					return 0;
 				}
 				pago=vm.ahora.diff(pago, 'days');
 				if (pago<=vm.limite.mora) {
 					if (pago>vm.limite.retraso) {
 						return 1;
 					}else{
 						return 0;
 					}
 				}
 			}
 			return 2;
 		}
 		function dibujaGrafico(title,data,callback,container){
 			if (typeof callback=='undefined') {
 				callback=function(e){
 					console.log(e);
 				};
 			}
 			if (typeof container=='undefined') {
 				container="chartContainer";
 			}
 			var datapoint=[];
 			for (var i = 0; i < data.name.length; i++) {
 				datapoint.push({
 					y: data.value[i],
 					label: data.name[i]
 				});
 			}
 			vm.chart = new CanvasJS.Chart(container, {
 				title:{
        			text: title
     			},
     			axisX:{
   					interval: 1,
 				},
            	data: [
                	{
                		click: callback,
                		type:'column',
                    	dataPoints: datapoint
                	}
                ]
            });
            vm.chart.render();
 		}
		
	}
})();