(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formTnota',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/tiponota/form.html',
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

    	function controller(TipoNotaFactory,error){
    		var vm=this;

			// Variables básicas
			var basicFactory= TipoNotaFactory;
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
				});
			}
		
			function actData(id){
				return basicFactory.mDt(id,vm.ndts).then(function(res){
					//console.log(res);
					vm.getData(vm.existente);
					error.setAlerta('Se ha actualizado el registro.');
					vm.ndts.visible=false;
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