(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formMateria',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/materias/form.html',
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

    	function controller(AniosFactory,MateriasFactory,error){
    		var vm=this;

			// Variables básicas
			var basicFactory= MateriasFactory;
			var baseFactory= AniosFactory;
			vm.ndts={};
			vm.base={};

			// Variables adicionales
	
			// Funciones basicas
			vm.getData=getData;
			vm.actData=actData;
			vm.newData=newData;
			vm.hayExistente=hayExistente;
			vm.accion=accion;
			vm.getBases=getBases;
			vm.hayBases=hayBases;

			// Funciones adicionales
			vm.selAnio=selAnio;
			
			// Lanzamiento Automático

			// Lanzamiento obligatorio
			//vm.getBases();

			if (typeof(vm.existente)!='undefined') {
				vm.getData(vm.existente);
			}

			/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////
			function selAnio(){
				//console.log(vm.ndts.anios_id);
				if (typeof(vm.ndts.anios_id)!='undefined') {
				for (var i = 0; i < vm.base.data.length; i++) {
					if (vm.base.data[i].id==vm.ndts.anios_id) {
						return vm.base.data[i].anio;
					}
				}
				}
				return 2000;
			}

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

			function getBases(){
				return baseFactory.gDts().then(function(res){
					vm.base=res;
				},function(res){
					error.setAlerta('No se encontraron años.');
				});
			}

			function hayBases(){
				try{
					console.log(typeof(vm.base.data));
					if (typeof(vm.base.data)=='array' || typeof(vm.base.data)=='object') {
						return true;
					}
					return false;
				}
				catch(err){
					console.log(err);
					return false;
				}
			}
    	}
	}
})();