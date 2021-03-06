(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formAnio',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/anios/form.html',
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

    	function controller(AniosFactory,error){
    		var vm=this;

			// Variables básicas
			var basicFactory= AniosFactory;
			vm.ndts={};

			// Variables adicionales
	
			// Funciones basicas
			vm.getData=getData;
			vm.actData=actData;
			vm.newData=newData;
			vm.hayExistente=hayExistente;
			vm.accion=accion;

			// Funciones adicionales
			
			// Lanzamiento Automático

			// Lanzamiento obligatorio
			if (typeof(vm.existente)!='undefined') {
				vm.getData(vm.existente);
			}

			/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function getData(id){
				return basicFactory.gDt(id).then(function(res){
					//console.log(res);
					vm.ndts=res.data;
					vm.ndts.anio=parseInt(vm.ndts.anio);
				});
			}
		
			function actData(id){
				return basicFactory.mDt(id,vm.ndts).then(function(res){
					//console.log(res);
					vm.getData(vm.existente);
					error.setAlerta('Se ha actualizado el registro.');
				},function(res){
					error.setError('Se ha presentado un error. No se actualiza el registro.');
				});
			}

			function newData(){
				return basicFactory.aDt(vm.ndts).then(function(res){
					//console.log(res);
					error.setAlerta('Se ha creado el registro.');
					vm.ndts={};
				},function(res){
					error.setError('Se ha presentado un error. No se crea el registro.');
				});
			}

			function accion(){
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
    	}
	}
})();