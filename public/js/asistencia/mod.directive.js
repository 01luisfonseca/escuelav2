(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('modAsistencia',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/asistencia/mod.html',
        	restrict: 'EA',
        	scope:{
        		alumnoid: '='
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

    	function controller(NewasistenciaFactory,error,$window, $interval){
    		var vm=this;

			// Variables básicas
			var basicFactory=NewasistenciaFactory;
			vm.dts={};

			// Variables adicionales
			vm.anterior=0;
			vm.alumnoid=0;
	
			// Funciones basicas
			vm.getData=getData;
			vm.delData=delData;

			// Funciones adicionales

			// Lanzamiento Automático
			$interval(startData,1000);

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function startData(){
				if (vm.alumnoid>0 && vm.alumnoid!=vm.anterior) {
					vm.getData(vm.alumnoid);
				}
				if (vm.alumnoid==0) {
					vm.dts={};
				}
				vm.anterior=vm.alumnoid;
			}

			function getData(alumnoid){
				return basicFactory.alId(alumnoid).then(function(res){
					vm.dts=res;
				});
			}

			function delData(id){
				if (!$window.confirm('¿ Seguro que desea eliminar el elemento ?')) {
					return false;
				}
				return basicFactory.dDt(id).then(function(res){
					vm.getData(vm.alumnoid);
					error.setAlerta(res.data.msj);
				});
			}
    	}
	}
})();