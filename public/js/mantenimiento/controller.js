(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('MttoCtrl',controller);

	function controller(animPage,MttoFactory,NivelesHasAniosFactory){
		var vm=this;

		// Variables básicas
		vm.niveles=[];
		vm.statusIndicador=0;
		vm.statusPeriodo=0;
	
		// Funciones basicas
		vm.actIndicPers=actIndicPers;		// Actualiza indicadores.
		vm.actPers=actPers;					// Actualiza periodos.
		vm.estacargando=estacargando;		// Verifica si está cargando el progreso

		// Lanzamiento Automático
		animPage.show('mtto',function(){});
		activate();
		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function activate(){
			getNiveles();
		}
		function getNiveles(){
			return NivelesHasAniosFactory.gDts().then(
				(dt)=>{
					vm.niveles=dt.data;
				},
				(e)=>{
					console.error(e);
				}
			);
		}
		function actPers(){
			let dts=JSON.parse(JSON.stringify(vm.niveles));
			let items=0;
			for (var i = 0; i < dts.length; i++) {
				items += dts[i].alumnos.length;
			}
			actionNiveles('periodo',dts,callback,items);

			function callback(dt){
				console.log('Periodo', dt);
				vm.statusPeriodo = dt;
			}
		}
		function actIndicPers(){
			let dts=JSON.parse(JSON.stringify(vm.niveles));
			let items=0;
			for (var i = 0; i < dts.length; i++) {
				items += dts[i].alumnos.length;
			}
			actionNiveles('indicador',dts,callback,items);

			function callback(dt){
				console.log('Indicador', dt);
				vm.statusIndicador = dt;
			}
		}
		function actionNiveles(action,dts,medidor,items,counter){
			if(!counter) counter=0;
			return MttoFactory.gDt(action+'/'+dts[0].id+'/'+dts[0].alumnos[0].id).then(
				(dt)=>{
					counter++;
					medidor(counter*100/items);
					dts[0].alumnos.shift();
					if(dts[0].alumnos.length>0){
						return actionNiveles(action,dts,medidor,items,counter);
					}else{
						dts.shift();
						if(dts.length>0){
							return actionNiveles(action,dts,medidor,items,counter);
						}else{
							medidor(0);
							return true;
						}
					}
				},
				(e)=>{
					medidor(0);
					console.error(e);
					return false;
				}
			);
		}
		function estacargando(){
			if( (vm.statusIndicador<0 && vm.statusIndicador>100) || (vm.statusPeriodo<0 && vm.statusPeriodo>100) ){
				return true;
			}
			return false;
		}
	}
})();