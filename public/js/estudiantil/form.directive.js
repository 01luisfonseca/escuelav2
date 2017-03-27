(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formEstudiantil',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/estudiantil/form.html',
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
			vm.niveles=[];
			vm.infoNivel={};
			vm.nivelSeleccionado=-1;

			// Variables adicionales
	
			// Funciones basicas
			vm.tabSelec=tabSelected;
			vm.setTab=setTab;
			vm.obtenerNiveles=obtenerNiveles;
			vm.obtenerNotasNivel=obtenerNotasNivel;
			vm.promedioIndicador=promedioIndicador;
			vm.promedioPeriodo=promedioPeriodo;
			vm.promedioMateria=promedioMateria;
			
			// Funciones adicionales
			
			// Lanzamiento Automático
			vm.obtenerNiveles();

			// Lanzamiento obligatorio

			///////////////////////// FUNCIONES ADICIONALES /////////////////////////////
			function promediador(materiaId,periodoId,indicadorId){
				var materias=vm.infoNivel.data.niveles_has_anios.materias_has_niveles;
				for (var i = 0; i < materias.length; i++) {
					var totalPeriodo=0;
					for (var j = 0; j < materias[i].materias_has_periodos.length; j++) {
						var totalIndicadores=0;
						for (var k = 0; k < materias[i].materias_has_periodos[j].indicadores.length; k++) {
							var totalTipo=0;
							for (var l = 0; l < materias[i].materias_has_periodos[j].indicadores[k].tipo_nota.length; l++) {
								totalTipo+=parseFloat(materias[i].materias_has_periodos[j].indicadores[k].tipo_nota[l].notas[0].calificacion);
							}
							totalTipo/=materias[i].materias_has_periodos[j].indicadores[k].tipo_nota.length;
							if(typeof indicadorId!='undefined' || typeof indicadorId!='null'){
								if (materias[i].materias_has_periodos[j].indicadores[k].id==indicadorId) {
									return totalTipo;
								}
							}
							totalIndicadores+=totalTipo*parseFloat(materias[i].materias_has_periodos[j].indicadores[k].porcentaje)/100;
						}
						if(typeof periodoId!='undefined' || typeof periodoId!='null'){
							if (materias[i].materias_has_periodos[j].id==periodoId) {
								return totalIndicadores;
							}
						}
						totalPeriodo+=totalIndicadores;
					}
					if(typeof materiaId!='undefined' || typeof materiaId!='null'){
						if (materias[i].id==materiaId) {
							return totalPeriodo/materias[i].niveles_has_periodos.length;
						}
					}
				}
			}
			

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			function setTab(i){
				vm.nivelSeleccionado=i;
				if (i>=0) {
					vm.obtenerNotasNivel(vm.niveles.data[i].id);
				}
			}
			function tabSelected(i){
				return vm.nivelSeleccionado==i;
			}
			function obtenerNiveles(){
				return RendimientoFactory.gDts().then(function(res){
					vm.niveles=res;
				});
			}
			function obtenerNotasNivel(alumnos_id){
				return RendimientoFactory.gDt(alumnos_id).then(function(res){
					vm.infoNivel=res;
				});
			}
			function promedioIndicador(indicadorId){
				return promediador(null,null,indicadorId);
			}
			function promedioPeriodo(periodoId){
				return promediador(null,periodoId,null);
			}
			function promedioMateria(materiaId){
				return promediador(materiaId,null,null);
			}			
    	}
	}
})();