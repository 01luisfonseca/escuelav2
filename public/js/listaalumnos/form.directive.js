(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formListalumn',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/listaalumnos/form.html',
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

    	function controller(ListaAlumnosFactory)
		{
    		var vm=this;

			// Variables básicas
			vm.sel={
				anioIdx: -1,
				nivel: 0
			};
			vm.anios={};
			vm.alumnos={};
	
			// Funciones basicas
			vm.getAnios=getAnios;
			vm.buscarAlumnos=buscarAlumnos;
			vm.buscarNiveles=buscarNiveles;
			vm.exportarAlumnos=exportarAlumnos;
			
			// Lanzamiento Automático
			vm.getAnios();

			// Lanzamiento obligatorio

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			function getAnios(){
				return ListaAlumnosFactory.gDts().then(function(res){
					vm.anios=res;
				});
			}
			function buscarAlumnos(){
				return ListaAlumnosFactory.gDt(vm.sel.nivel).then(function(res){
					vm.alumnos=res;
				});
			}
			function buscarNiveles(idx){
				if (vm.anios.data) {
					if (vm.anios.data.length) {
						if(idx >= 0){
							return vm.anios.data[idx].niveles_has_anios;
						}
					}
				}
				return [];
			}
			function exportarAlumnos(id){
				var defaultFileName='Listado';
				return ListaAlumnosFactory.gEDt(id).then(
        			function (res) {
            			var type = res.headers('Content-Type');
            			var disposition = res.headers('Content-Disposition');
            			if (disposition) {
                			var match = disposition.match(/.*filename=\"?([^;\"]+)\"?.*/);
                			if (match[1]) defaultFileName = match[1];
            			}
            			defaultFileName = defaultFileName.replace(/[<>:"\/\\|?*]+/g, '_');
            			var blob = new Blob([res.data], { type: type });
            			saveAs(blob, defaultFileName);                   
        			}
        		);
			}
			
    	}
	}
})();