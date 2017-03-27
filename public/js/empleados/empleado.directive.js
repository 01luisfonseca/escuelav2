(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('empleado',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/empleados/empleado.crear.html',
        	restrict: 'EA',
        	scope:{
        		existente: '='
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

    	function controller(EmpleadosFactory,UsersFactory,error){
    		var vm=this;

			// Variables básicas
			var basicFactory= EmpleadosFactory;
			vm.ndts={};

			// Variables adicionales
			vm.empleables={};
	
			// Funciones basicas
			vm.getData=getData;
			vm.actData=actData;
			vm.newData=newData;

			// Funciones adicionales
			vm.getEmpleables=getEmpleables;
			vm.accionEmpleado=accionEmpleado;
			vm.hayExistente=hayExistente;

			// Lanzamiento Automático
			vm.getEmpleables();
			if (typeof(vm.existente)!='undefined') {
				vm.getData(vm.existente);
			}

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function getEmpleables(){
				return UsersFactory.getEmpleables().then(function(res){
					//console.log(res);
					vm.empleables=res;
				})
			}

			function accionEmpleado(){
				if (typeof(vm.existente)=='undefined') {
					vm.newData();
				}
				if (vm.existente>0) {
					vm.actData(vm.existente);
				}
			}

			function hayExistente(){
				if (typeof(vm.existente)!='undefined') {
					return true;
				}
				return false;
			}

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function getData(id){
				return basicFactory.gDt(id).then(function(res){
					//console.log(res);
					vm.ndts=res.data;
					vm.ndts.contrato_ini= new Date(vm.ndts.contrato_ini);
					vm.ndts.contrato_fin= new Date(vm.ndts.contrato_fin);
					vm.ndts.salario=parseInt(vm.ndts.salario);
					vm.ndts.eps_val=parseFloat(vm.ndts.eps_val);
					vm.ndts.arl_val=parseFloat(vm.ndts.arl_val);
					vm.ndts.pension_val=parseFloat(vm.ndts.pension_val);
				});
			}
		
			function actData(id){
				return basicFactory.mDt(id,vm.ndts).then(function(res){
					//console.log(res);
					vm.getData(vm.existente);
					error.setAlerta('Se ha creado actualizado el empleado.');
				});
			}

			function newData(){
				return basicFactory.aDt(vm.ndts).then(function(res){
					//console.log(res);
					error.setAlerta('Se ha creado el empleado.');
					vm.ndts={};
				});
			}
    	}
	}
})();