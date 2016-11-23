(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('mensajes',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/error/error.html',
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

    	function controller(error,$scope){
    		var vm=this;

			// Variables básicas
			$scope.error=error;
			vm.error={};

			// Variables adicionales
	
			// Funciones basicas
			vm.actError=actError;
			vm.actAlerta=actAlerta;

			// Funciones adicionales
			
			// Lanzamiento Automático
			$scope.$watch('error.getErrorStat()',actError);
			$scope.$watch('error.getAlertaStat()',actAlerta);
			
			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			function actError(){
				vm.error=error.error;
			}

			function actAlerta(){
				vm.alerta=error.alerta;
			}

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			
    	}
	}
})();