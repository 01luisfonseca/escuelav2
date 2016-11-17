(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('modEmpleado',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/empleados/empleado.modificar.html',
        	restrict: 'EA',
        	scope:{
        	},
        	controller: controller,
        	controllerAs: 'vm',
        	bindToController: true // because the scope is isolated
    	};
    	return directive;

		// Funciones
		function link(scope, element, attrs) {
      		/* */
    	}

    	function controller(EmpleadosFactory,$window,$timeout,$interval,error){
    		var vm=this;
    		var promise;

			// Variables básicas
			vm.panel=0;
			vm.dts={};
			vm.buscado='';
			vm.buscando=false;
			vm.yaBuscado=false;

			// Variables adicionales
	
			// Funciones basicas
			vm.getDatas=getDatas;
			vm.buscarData=buscarData;
			vm.delData=delData;
			vm.selecPanel=selecPanel;
			vm.esPanelSelec=esPanelSelec;
			vm.cerrarPanel=cerrarPanel;

			// Funciones adicionales

			// Lanzamiento Automático
			//vm.getDatas();
			startData();

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function startData(){
				stopData();
				vm.getDatas();
				//promise=$interval(vm.getDatas,5000);
			}

			function stopData(){
				//$interval.cancel(promise);
			}

			function getDatas(){
				return EmpleadosFactory.gDts().then(function(res){
					vm.dts=res;
					for (var i = 0; i < vm.dts.data.length; i++) {
						vm.dts.data[i].contrato_ini= new Date(vm.dts.data[i].contrato_ini);
						vm.dts.data[i].contrato_fin= new Date(vm.dts.data[i].contrato_fin);
					}
				});
			}

			function buscarData(){
				stopData();
				if(vm.buscado.length>2){
					vm.buscando=true;
					if (!vm.yaBuscado) {
						vm.yaBuscado=true;
						$timeout(searchData,1000);
					}
				}
				if(vm.buscado==''){
					return startData();
				}
				return false;
			}

			function searchData(){
				vm.yaBuscado=false;
				return EmpleadosFactory.gSDt(vm.buscado).then(function(res){
					//console.log(vm.users);
					vm.dts=res;
					for (var i = 0; i < vm.dts.data.length; i++) {
						vm.dts.data[i].contrato_ini= new Date(vm.dts.data[i].contrato_ini);
						vm.dts.data[i].contrato_fin= new Date(vm.dts.data[i].contrato_fin);
					}
					vm.buscando=false;
				});
			}

			function delData(index){
				if (!$window.confirm('¿ Seguro que desea eliminar el elemento ?')) {
					return false;
				}
				return EmpleadosFactory.dDt(vm.dts.data[index].id).then(function(res){
					vm.getDatas();
					error.setAlerta(res.data.msj);
				});
			}
	
			function selecPanel(index){
				stopData();
				vm.panel=index+1;
			}

			function esPanelSelec(index){
				return vm.panel==(index+1);
			}

			function cerrarPanel(){
				startData();
				vm.panel=0;
			}
    	}
	}
})();