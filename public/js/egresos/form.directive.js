(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formEgreso',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/egresos/form.html',
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

    	function controller(
    		GastoFactory,
    		error,
    		$window,
    		$timeout)
		{
    		var vm=this;

			// Variables básicas
			
	
			// Funciones basicas
			

			// Lanzamiento Automático

			// Lanzamiento obligatorio

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			
    	}
	}
})();