(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('MatYPenCtrl',controller);

	function controller(animPage,NivelesHasAniosFactory,AniosFactory,MesesFactory){
		var vm=this;

		// Variables básicas
		vm.ahora=new Date();
		vm.ahora=moment(vm.ahora);
		vm.limite={
			mora: 45,
			retraso: 35
		};
		vm.status={
			mat:{
				name:['Al día', 'Retrasados', 'Morosos'],
				value:[0,0,0],
				niveles:[
					{
						tipo: 'Al día',
						ids:[],
						name: [],
						value: []
					},
					{
						tipo: 'Retrasados',
						ids:[],
						name: [],
						value: []
					},
					{
						tipo: 'Morosos',
						ids:[],
						name: [],
						value: []
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
						value: []
					},
					{
						tipo: 'Retrasados',
						ids:[],
						name: [],
						value: []
					},
					{
						tipo: 'Morosos',
						ids:[],
						name: [],
						value: []
					}
				]
			}
		};
		vm.datosRaw=[];
		vm.cargando=false;
		vm.anios=[];
		vm.meses=[];
		vm.sel={
			anio: 0,
			mes: 0
		};

		function onClick(e) {
			console.log(e.dataPoint.indexLabel);
		}
	
		// Funciones basicas
		vm.cambioAnio=cambioAnio;
		vm.cambioMes=cambioMes;


		// Lanzamiento Automático
		animPage.show('matypen',function(){});
		getAnios();
		getMeses();

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
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
 			return NivelesHasAniosFactory.gPAl(vm.sel.anio).then(function(res){
 				calcularDatos(res.data);
 			});
 		}
 		function cambioMes(){}
 		function calcularDatos(dts){
 			vm.datosRaw=dts;
 			for (var i = 0; i < dts.length; i++) {
 				for (var x = 0; x < 3; x++) { // Agrega la información básica de los arreglos. No calcula
 					vm.satus.mat.niveles[x].ids.push(dts[i].id);
 					vm.satus.mat.niveles[x].name.push(dts[i].niveles.nombre);
 					vm.satus.mat.niveles[x].value.push(0);
 					vm.satus.pen.niveles[x].ids.push(dts[i].id);
 					vm.satus.pen.niveles[x].name.push(dts[i].niveles.nombre);
 					vm.satus.pen.niveles[x].value.push(0);
 				}
 				for (var j = 0; j < dts[i].alumnos.length; j++) {// Acumulando el alumno según su tipo.
 					var pagoMat=pagoMat(dts[i].alumnos[j].pago_matricula);
 					var pagoPen=pagoPen(dts[i].alumnos[j].pago_pension);
 					vm.satus.mat.niveles[pagoMat].value[i]++;
 					vm.satus.mat.niveles[pagoPen].value[i]++;
 				}
 			}
 		}
 		function verifyPago(obj,type){
 			if (obj.length) {
 				var pago=moment(obj[0].created_at);
 				if (type=='pen') {
 					//pago=moment()
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

 					var pagoPer=moment(dts[i].alumnos[j].pago_matricula[0].created_at);
 					console.log('Diferencia en matricula. ');
 					console.log('Diferencia en pension. '+vm.ahora.diff(pagoMat, 'hours'));
 		}
 		function dibujaGrafico(data,event){
 			var datapoint=[];
 			for (var i = 0; i < data.length; i++) {
 				datapoint.push({
 					y: data[i].value,
 					indexLabel: data[i].name
 				});
 			}
 			var chart = new CanvasJS.Chart("chartContainer",
			{
				data: [
				{
					type: "pie",
					click: event,
					dataPoints: datapoint
				}
				]
			});
			chart.render();
 		}
		
	}
})();