(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('explicrendimDir',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/estudiantil/explicrendim.html',
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

    	function controller(RendimientoFactory)
		{
    		var vm=this;

			// Variables básicas

			// Variables adicionales
	
			// Funciones basicas
			
			
			// Funciones adicionales
			
			// Lanzamiento Automático
			

			// Lanzamiento obligatorio

			///////////////////////// FUNCIONES ADICIONALES /////////////////////////////
			
			

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			
    	}
	}
})();