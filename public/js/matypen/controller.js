(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('MatYPenCtrl',controller);

	function controller(animPage,NivelesHasAniosFactory,AniosFactory,MesesFactory, Saver, $timeout, $window, $rootScope){
		var vm=this;

		// Variables básicas
		vm.selecData={};
		vm.ahora=new Date();
		vm.ahora=moment(vm.ahora);
		vm.limite={
			mora: 45,
			retraso: 35
		};
		vm.statustempo=[];
		vm.status={};
		vm.datosRaw=[];
		vm.dataMesRaw=[];
		vm.cargando=false;
		vm.ocultaMatricula=false;
		vm.anios=[];
		vm.meses=[];
		vm.sel={
			anio: 0,
			mes: '',
			type: 'pen',
			tabla: false
		};
	
		// Funciones basicas
		vm.cambioAnio=cambioAnio;
		vm.cambioMes=cambioMes;
		vm.mostrarGraficos=mostrarGraficos;
		vm.grafIni=grafIni;


		// Lanzamiento Automático
		animPage.show('matypen',function(){
			activate();
		});

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function activate(){
 			vm.ocultaMatricula=true;
			initObjeto();
			getAnios();
			getMeses();
		}
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
 			$rootScope.$broadcast('cargando',true);
 			return AniosFactory.gDts().then(function(res){
 				$rootScope.$broadcast('cargando',false);
 				vm.cargando=false;
 				vm.anios=res.data;
 			},function(e){
 				$rootScope.$broadcast('cargando',false);
 				$window.alert('El servidor presenta molestias en la conexión.');
 				console.log(e);
 			});
 		}
 		function getMeses(){
 			return MesesFactory.gDts().then(function(res){
 				vm.meses=res.data;
 			});
 		}
 		function cambioAnio(){
 			vm.ocultaMatricula=false;
 			$rootScope.$broadcast('cargando',true);
 			initObjeto();
 			return NivelesHasAniosFactory.gPAl(vm.sel.anio).then(function(res){
 				$rootScope.$broadcast('cargando',false);
 				vm.datosRaw=res.data;
 				calcularDatos(res.data);
 				mostrarGraficos();
 			},function(e){
 				$rootScope.$broadcast('cargando',false);
 				$window.alert('El servidor presenta molestias en la conexión.');
 				console.log(e);
 			});
 		}
 		function cambioMes(){
 			vm.ocultaMatricula=true;
 			vm.sel.type='pen';
 			$rootScope.$broadcast('cargando',true);
 			return NivelesHasAniosFactory.gPMAl(vm.sel.anio,vm.sel.mes).then(
 				function(res){
 					$rootScope.$broadcast('cargando',false);
 					vm.statustempo=JSON.parse(JSON.stringify(vm.status));
 					vm.dataMesRaw=res.data;
 					calcularDatos(res.data);
 					mostrarGraficos();
 				},
 				function(){
 					$rootScope.$broadcast('cargando',false);
 					$window.alert('El servidor presenta molestias en la conexión.');
 					console.log(e);
 				}
 			);
 		}
 		function grafIni(){
 			if(vm.sel.anio!==0) vm.ocultaMatricula=false;
 			if(vm.sel.mes!=='') vm.status=JSON.parse(JSON.stringify(vm.statustempo));
 			vm.statustempo=[];
 			vm.sel.mes='';
 			vm.mostrarGraficos();
 		}
 		function mostrarGraficos(){
 			vm.sel.tabla= false;
 			vm.selecData= {};
 			var tipo;
 			if (vm.sel.type=='pen') {
 				if (vm.sel.mes=='') {
 					dibujaGrafico('Estado de pensiones según el año.',vm.status.pen,llamadaPension);
 				} else {
 					dibujaGrafico('Estado de pensiones según el mes de '+vm.meses[vm.sel.mes-1].nombre,vm.status.pen,llamadaPensionMes);
 				}
 			}else{
 				dibujaGrafico('Estado de matrículas según el año.',vm.status.mat,llamadaMatricula);
 			}
 			/////////////////////////////////////
 			function llamadaPension(e){
 				tipo=e.dataPoint.label;
 				var index=verTipo(tipo);
    			dibujaGrafico('Resultados de pensiones por: '+e.dataPoint.label,vm.status.pen.niveles[index],tablaExportar);
 			}
 			function llamadaPensionMes(e){
 				tipo=e.dataPoint.label;
 				var index=verTipo(tipo);
    			dibujaGrafico('Resultados de pensiones por: '+e.dataPoint.label,vm.status.pen.niveles[index],tablaExportar);
 				//tipo=e.dataPoint.label;
 				//tablaExportar(e);
 				//var index=verTipo(tipo);
    			//dibujaGrafico('Resultados de pensiones por: '+e.dataPoint.label,vm.status.pen.niveles[index],tablaExportar);
 			}
 			function llamadaMatricula(e){
 				tipo=e.dataPoint.label;
 				var index=verTipo(tipo);
    			dibujaGrafico('Resultados de matrículas por: '+e.dataPoint.label,vm.status.mat.niveles[index],tablaExportar);
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
 				if(nivel!==clase){
	 				for (var i = 0; i < vm.status[tipo].name.length; i++) {
	 					if(vm.status[tipo].name[i]==clase){
	 						for (var j = 0; j < vm.status[tipo].niveles[i].levels.length; j++) {
	 							if(vm.status[tipo].niveles[i].levels[j].name==nivel){
	 								vm.status[tipo].niveles[i].levels[j].tipo=tipo;
	 								vm.status[tipo].niveles[i].levels[j].clase=vm.status[tipo].name[i];
	 								Saver.setData('matypen',{level:vm.status[tipo].niveles[i].levels[j],mes:vm.sel.mes}); // Parece que hay que implementar promesas ya que en la primera no funciona.
									$timeout(function(){
										$window.open('/#/matypentable'); 
									},500);
	 							}
	 						}
	 						
	 					}
	 				}
 				}else{
 					let i=verTipo(nivel);
 					let info={
 						levels: vm.status[tipo].niveles[i].levels,
 						mes: vm.meses[vm.sel.mes-1],
 						tipo:vm.sel.type
 					};
 					Saver.setData('matypen',info); // Parece que hay que implementar promesas ya que en la primera no funciona.
					$timeout(function(){
						$window.open('/#/matypentable'); 
					},500);
 				}
			}
 		}
 		function calcularDatos(dts){ // Aqui vamos. debemos evaluar los datos si ha elegido mes o si es un año.
 			initObjeto(); // Inicializamos datos
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
 					//if(pagoPen>0) console.log('Estado',pagoPen, dts[i].alumnos[j].name+' '+dts[i].alumnos[j].name);
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
 			//console.log(vm.datosRaw,vm.status);
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
 				var pago;
 				/*if (type==='pen') {
					var fecha=new Date(vm.datosRaw[0].anios.anio, obj[0].mes_id, 1, 0, 0, 0, 0);
					pago=moment(fecha);
 				}else{
 					return 0; // Existe matrícula, por lo que se cuenta como pagada.
 				}*/
 				if (type!=='pen') return 0;
 				if (vm.sel.mes!=='') {
 					return 0;
 					//let miahora=new Date(vm.datosRaw[0].anios.anio,obj[0].mes_id,1,0,0,0,0);
 					//let otroah=moment(miahora);
 					//pago= otroah.diff(pago);
 				} else {
 					if (obj[0].mes_id>=(vm.ahora.month()+1)) {
 						return 0;
 					}else{
 						if((vm.ahora.month()+1)-obj[0].mes_id==1) return 1;
 						return 2;
 					}
 					//console.log('Fechas',pago,vm.ahora);
 					//pago=vm.ahora.diff(pago, 'days'); // Calculo de la diferencia de días
 					//pago=pago.diff(vm.ahora, 'days');
 				}
 				/*console.log('Compradaro', pago, vm.limite.mora,vm.limite.retraso);
 				if (pago <= vm.limite.mora) {
 					if (pago>vm.limite.retraso) {
 						return 1;
 					}else{
 						return 0;
 					}
 				}*/
 			}
 			return 2; // Si un elemento no tiene nada, entonces es porque no han pagado. Tambien aplica para los morosos
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