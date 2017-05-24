(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('CursYMatCtrl',controller);

	function controller(animPage, AniosFactory, NivelesHasAniosFactory, Saver, $timeout, $window, $rootScope){
		var vm=this;

		// Variables básicas
		vm.anios=[];
		vm.muestraboton=false;
		vm.muestraChartjs=false;
		vm.sel={
			anio: 0,
			per: null,
			type: 'mat'		};
		vm.datosRaw=[];
		vm.status={}; // De donde se grafica


		vm.selecData={};
		vm.ahora=new Date();
		vm.ahora=moment(vm.ahora);
		vm.limite={
			mora: 45,
			retraso: 35
		};

		
		vm.cargando=false;
			
		// Funciones basicas
		vm.periodos=periodos;
		vm.cambioAnio=cambioAnio;
		vm.mostrarGraficos=mostrarGraficos;
		vm.alprimero=alprimero;


		// Lanzamiento Automático
		animPage.show('cursymat',function(){
			activate();
		});

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function activate(){
			initObjeto();
			getAnios();
		}
 		function initObjeto(){
 			vm.status={
				mat:{
					name:[],
					value:[],
					promCurso:[],
				},
				cur:{
					name:[],
					value:[],
					filtroMateria:[],
				}
			};
 		}
 		function alprimero(){}
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
 		function periodos(){
 			for (var i = 0; i < vm.anios.length; i++) {
 				if(vm.anios[i].id==vm.sel.anio){
 					return vm.anios[i].periodos;
 				}
 			}
 			return [];
 		}
 		function cambioAnio(){
 			$rootScope.$broadcast('cargando',true);
 			initObjeto();
 			return NivelesHasAniosFactory.gNNAl(vm.sel.anio,vm.sel.per).then(function(res){
 				$rootScope.$broadcast('cargando',false);
 				vm.datosRaw=res.data;
 				calcularDatos(vm.datosRaw);
 				//mostrarGraficos();
 			},function(e){
 				$rootScope.$broadcast('cargando',false);
 				$window.alert('El servidor presenta molestias en la conexión.');
 				console.log(e);
 			});
 		}
 		function mostrarGraficos(){
 			vm.muestraboton=false;
 			vm.muestraChartjs=false;
 			for (var i = 0; i < vm.status.cur.name.length; i++) {
 				vm.status.cur.name[i]= reductNames(vm.status.cur.name[i]);
 				vm.status.cur.filtroMateria[i].reducName=vm.status.cur.name[i];
 			}
 			for (var j = 0; j < vm.status.cur.filtroMateria.length; j++) {
				var fmat=vm.status.cur.filtroMateria[j];
				for (var k = 0; k < fmat.name.length; k++) {
					fmat.name[k]=reductNames(fmat.name[k]);
				}
			}
			for (var i = 0; i < vm.status.mat.name.length; i++) {
 				vm.status.mat.name[i]= reductNames(vm.status.mat.name[i]);
 				vm.status.mat.promCurso[i].reducName = vm.status.mat.name[i];
 			}
 			for (var j = 0; j < vm.status.mat.promCurso.length; j++) {
				var fmat=vm.status.mat.promCurso[j];
				for (var k = 0; k < fmat.name.length; k++) {
					fmat.name[k]=reductNames(fmat.name[k]);
				}
			}
 			if (vm.sel.type=='mat') {
 				dibujaGrafico('Rendimiento académico por Materia.',vm.status.mat, nuevoGraficoMat);
 			}else{
 				dibujaGrafico('Rendimiento académico por Curso.',vm.status.cur, nuevoGraficoCur);
 			}
 		}
 		function nuevoGraficoMat(dt){
 			vm.muestraboton=true;
 			let dato= dt.dataPoint.label;
 			let id=posName(dato,'mat','promCurso');
 			if(id!==null) dibujaGrafico('Rendimiento de '+vm.status.mat.promCurso[id].mat+' según los cursos.',vm.status.mat.promCurso[id]);
 		}
 		function nuevoGraficoCur(dt){
 			vm.muestraboton=true;
 			let dato= dt.dataPoint.label;
 			let id= posName(dato,'cur','filtroMateria');
 			if(id!==null) dibujaGraficoFull('Rendimiento de '+vm.status.cur.filtroMateria[id].cur+' según las materias.',vm.status.cur.filtroMateria[id]);
 		}
 		function posName(dt,tipo,array){
 			let dato=dt;
 			let srcharr=vm.status[tipo][array];
 			for (var i = 0; i < srcharr.length; i++) {
 				let matching=srcharr[i].reducName;
 				if(matching==dato) return i;
 			}
 			return null;
 		}
 		function reductNames(name){
 			if(name.indexOf(' ')>-1){
 				var splitted=name.split(' ');
 				return splitted[0].substring(0,3)+'. '+splitted[splitted.length-1].substring(0,5);
 			}else{
 				if (name.indexOf('-')>-1) {
 					return name.substring(0,9); // Si tiene un separador es una palabra compuesta
 				}
 				return name.substring(0,7); // Una palabra es recortada a 7 letras
 			}
 		}
 		function calcularDatos(dts){
 			let materiasDisponibles=[];
 			let idcursos=[];
 			for (var i = 0; i < dts.length; i++) { // Por cada nivel
 				let promcur=0;
 				var mats=[]; // Separador de materias por curso
 				var matname=[];
 				var matvaluepasa=[];
 				var matvaluenopasa=[];
 				for (var j = 0; j < dts[j].materias_has_niveles.length; j++) { // Por cada materia
 					// Extracción de notas promedio por periodos
 					let prommat=0;
 					var filtro={p:[], np:[]};
 					for (var k = 0; k < dts[i].materias_has_niveles[j].materias_has_periodos[0].alumnos_has_periodos.length; k++) {
 						var alumno=dts[i].materias_has_niveles[j].materias_has_periodos[0].alumnos_has_periodos[k];
 						var notaact=parseFloat(alumno.prom);
 						prommat += notaact;
 						if (notaact >= 3) {// Filtro de nota. De 3 en adelante.
 							filtro.p.push(alumno); // Pasa
 						} else {
 							filtro.np.push(alumno); // No pasa
 						} 
 					}
 					dts[i].materias_has_niveles[j].pasan=filtro.p; // Alumnos que pasan
 					dts[i].materias_has_niveles[j].nopasan=filtro.np; // alumnos que no pasan
 					mats.push({curso: i, mat: j});
 					matname.push(dts[i].materias_has_niveles[j].materias.nombre);
 					matvaluepasa.push(dts[i].materias_has_niveles[j].pasan.length);
 					matvaluenopasa.push(dts[i].materias_has_niveles[j].nopasan.length);
 					let divisor= dts[i].materias_has_niveles[j].materias_has_periodos[0].alumnos_has_periodos.length;
 					// Guardamos el promedio de la materia para todos los alumnos del curso.
 					dts[i].materias_has_niveles[j].prom= prommat / (divisor !== 0? divisor : 1);
 					promcur += dts[i].materias_has_niveles[j].prom;
 					// Acumular las materias disponibles para sus promedios unicos posteriores
 					materiasDisponibles.push({
 						id: dts[i].materias_has_niveles[j].materias.id, 
 						nombre:dts[i].materias_has_niveles[j].materias.nombre, 
 						prom: dts[i].materias_has_niveles[j].prom,
 						curso: dts[i].niveles.nombre,
 						posmats : {curso: i, mat: j}
 					});
 					idcursos.push(dts[i].materias_has_niveles[j].materias.id);
 				}
 				let divis= dts[j].materias_has_niveles.length;
 				dts[i].prom= promcur / (divis !== 0? divis : 1);
 				vm.status.cur.name.push(dts[i].niveles.nombre); // Nombre Niveles
 				vm.status.cur.value.push(dts[i].prom); // Promedio por nivel.
 				vm.status.cur.filtroMateria.push({
 					cur:dts[i].niveles.nombre,
 					nivelid:dts[i].id,
 					posmats: mats, // Posiciones de materias
 					name: matname,
 					value: matvaluepasa,
 					value2: matvaluenopasa, //Para verificar integracion de dos barras. TODO
 				}); // Materias en curso.
 			}
 			let materias=idcursos.filter(function(value, index, self) { 
			    return self.indexOf(value) === index;
			});
			materias.sort(function(a, b){return a-b;}); // Organización numérica ascendente
			//let objMat=[];
			for (var i = 0; i < materias.length; i++) {
				let cuentael=0;
				vm.status.mat.name.push('');
				vm.status.mat.value.push(0);
				vm.status.mat.promCurso.push({
					mat:'',
					posmats: [],
					name:[],
					value:[],
				});
				for (var j = 0; j < materiasDisponibles.length; j++) {
					if(materias[i]==materiasDisponibles[j].id){
						cuentael ++;
						vm.status.mat.name[i]=materiasDisponibles[j].nombre;
						vm.status.mat.value[i] += materiasDisponibles[j].prom;
						vm.status.mat.promCurso[i].mat=materiasDisponibles[j].nombre;
						vm.status.mat.promCurso[i].posmats.push(materiasDisponibles[j].posmats);
						vm.status.mat.promCurso[i].name.push(materiasDisponibles[j].curso);
						vm.status.mat.promCurso[i].value.push(materiasDisponibles[j].prom);
					}
				}
				vm.status.mat.value[i] /= (cuentael>0? cuentael : 1);
			}
			console.log('Status:',vm.status);
 			mostrarGraficos();
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
 					label: data.name[i],
 					//indexLabel: data.value[i],
 				});
 			}
 			datapoint.sort(function(dataPoint1, dataPoint2) {
				return dataPoint2.y - dataPoint1.y;
			});
 			vm.chart = new CanvasJS.Chart(container, {
 				title:{
        			text: title
     			},
		      	axisY:{
			      maximum: 5
			    },
     			axisX:{
   					interval: 1,
   					labelAngle: 90,
 				},
            	data: [
                	{
                		click: callback,
                		type:'column',
                		indexLabelFontColor: "darkSlateGray",
						indexLabelFormatter: formatter,
						indexLabelPlacement: "outside",  
         				indexLabelOrientation: "vertical",
                    	dataPoints: datapoint
                	}
                ]
            });
            vm.chart.render();
            // //
            function formatter(e) {
					return parseFloat(e.dataPoint.y).toFixed(2);
			}
 		}
 		function dibujaGraficoFull(title,data,callback,container){
 			vm.muestraChartjs=true;
 			if (typeof callback=='undefined') {
 				callback=function(e){
 					console.log(e);
 				};
 			}
 			if (typeof container=='undefined') {
 				container="chartContainer";
 			}
 			var datapointPasan=[], datapointNoPasan=[];
 			for (var i = 0; i < data.name.length; i++) {
 				datapointPasan.push({
 					y: data.value[i],
 					label: data.name[i],
 					//indexLabel: data.value[i],
 				});
 				datapointNoPasan.push({
 					y: data.value2[i],
 					label: data.name[i],
 					//indexLabel: data.value[i],
 				});
 			}
 			//console.log(datapointPasan,datapointNoPasan);
 			var datag = {
			    labels: data.name,
			    datasets: [
			        {
			            label: "Pasan",
			            backgroundColor: "green",
			            data: data.value
			        },
			        {
			            label: "No pasan",
			            backgroundColor: "red",
			            data: data.value2
			        }
			    ]
			};
			var options={};
			$('#canvasId').empty();
			$('#canvasId').append("<canvas id='specialChart' style='width: 100%;' height='200'></canvas>");
			var canvas=document.getElementById("specialChart");
			var ctx= canvas.getContext("2d");
			ctx.clearRect(0, 0, canvas.width, canvas.height);
 			var myBarChart = new Chart(ctx, {
			    type: 'bar',
			    data: datag,
			    options: options
			});
 			/*
 			$window.chart2 = new CanvasJS.Chart(container, {
 				title:{
        			text: title
     			},
            	data: [
                	{
                		//click: callback,
                		type: "stackedColumn",
						//showInLegend: true, 
						name: "Pasan",
						dataPoints: datapointPasan
                	},
                	{
                		//click: callback,
                		type: "stackedColumn",
						//showInLegend: true, 
						name: "No pasan",
						dataPoints: datapointNoPasan
                	}
                ]
            });
            $window.chart2.render();*/
 		}
		
	}
})();