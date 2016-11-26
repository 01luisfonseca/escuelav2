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
			$scope.errorSc=error;
			vm.error={};

			// Variables adicionales
	
			// Funciones basicas
			vm.actError=actError;
			vm.actAlerta=actAlerta;

			// Funciones adicionales
			
			// Lanzamiento Automático
			$scope.$watch('errorSc.error.msj',actError);
			$scope.$watch('errorSc.alerta.msj',actAlerta);
			
			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			function actError(){
				console.log('Se ha actualizado el error');
				vm.error=error.error;
			}

			function actAlerta(){
				console.log('Se ha actualizado la advertencia');
				vm.alerta=error.alerta;
			}

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			
    	}
	}
})();