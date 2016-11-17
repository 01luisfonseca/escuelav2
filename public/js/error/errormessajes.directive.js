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

    	function controller(error){
    		var vm=this;

			// Variables básicas
			vm.error=error;

			// Variables adicionales
	
			// Funciones basicas

			// Funciones adicionales
			
			// Lanzamiento Automático
			
			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			
    	}
	}
})();