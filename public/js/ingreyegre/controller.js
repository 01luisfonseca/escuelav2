(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('IngreyEgreCtrl',controller);

	function controller(animPage,IngreyEgreFactory,AniosFactory){
		var vm=this;

		// Variables básicas
		vm.datosRaw=[];
		vm.cargando=false;
		vm.anios=[];
		vm.meses=[];
		vm.sel={
			anio: 0,
			mes: 0,
			type: 'pen'
		};
	
		// Funciones basicas
		vm.cambioAnio=cambioAnio;
		vm.mostrarGraficos=mostrarGraficos;


		// Lanzamiento Automático
		animPage.show('ingreyegre',function(){});
		getAnios();
		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
 		function getAnios(){
 			return AniosFactory.gDts().then(function(res){
 				vm.anios=res.data;
 			});
 		}
 		function cambioAnio(){
 			return IngreyEgreFactory.gDts(findAnio(vm.sel.anio)).then(function(res){
 				vm.datosRaw=res.data;
 				mostrarGraficos(res.data);
 			});
 			function findAnio(id){
 				for (var i = 0; i < vm.anios.length; i++) {
 					if(vm.anios[i].id=id){
 						return vm.anios[i].anio;
 					}
 				}
 			}
 		}
 		function mostrarGraficos(elems){
            //console.log(elems);
 			dibujaGrafico('Estado de Ingresos y Egresos según el año.',[elems.Ingresos.valores,elems.Gastos.valores],llamadaPension);
 			/////////////////////////////////////
 			function llamadaPension(e){
 				console.log(e);
 				//var index=verTipo(tipo);
    			//dibujaGrafico('Resultados de pensiones por: '+e.dataPoint.label,vm.status.pen.niveles[index], tablaExportar);
 			}
    		function tablaExportar(e){
 				var sel=e.dataPoint.label;
 				console.log(sel, tipo, vm.sel.type);
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
    					return 0;
    					break;
  				}
 			}
 		}
 		function dibujaGrafico(title,data,callback,container){
 			console.log(data);
 			if (typeof callback=='undefined') {
 				callback=function(e){
 					console.log(e);
 				};
 			}
 			if (typeof container=='undefined') {
 				container="chartContainer";
 			}
 			vm.chart = new CanvasJS.Chart(container, {
                toolTip: {
					shared: true
				},
 				title:{
        			text: title
     			},
            	data: [
                	{
        				click: callback,
        				name: "Ingresos",
                		type:'column',
                    	dataPoints: data[0]
                	},
                	{
        				click: callback,
        				name: "Egresos",
                		type:'column',
                    	dataPoints: data[1]
                	}
                ]
            });
            vm.chart.render();
 		}
		
	}
})();