(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('relNivel',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/niveles/rel.html',
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

    	function controller(AniosFactory,NivelesFactory, NivelesHasAniosFactory, error, $window){
    		var vm=this;
    		var nha=NivelesHasAniosFactory;

			// Variables básicas
			vm.anios={};
			vm.niveles={};
			vm.selec={
				anio:0,
				nivel:[]
			};
			vm.contadorOps=0;

			// Variables adicionales
	
			// Funciones basicas
			vm.buscarDatos=buscarDatos;
			vm.infoCompleta=infoCompleta;
			vm.selAnio=selAnio;
			vm.anioSel=anioSel;
			vm.selNivel=selNivel;
			vm.nivelSel=nivelSel;
			vm.guardarRel=guardarRel;
			vm.nivelExiste=nivelExiste;
			vm.delData=delData;

			// Funciones adicionales
			
			// Lanzamiento Automático

			// Lanzamiento obligatorio
			vm.buscarDatos();

			/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////
			

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function buscarDatos(){
				buscarAnios();
				buscarNiveles();
			}

			function buscarAnios(){
				return AniosFactory.gDts().then(function(res){
					vm.anios=res;
					return vm.anios;
				});
			}

			function buscarNiveles(){
				return NivelesFactory.gDts().then(function(res){
					vm.niveles=res;
					return vm.niveles;
				});
			}

			function infoCompleta(){
				if(vm.selec.anio!=0 && vm.selec.nivel.length>0){
					return true;
				}
				return false;
			}
			
			function selAnio(id){
				vm.selec.anio=id;
				vm.selec.nivel=[];
			}

			function anioSel(id){
				return vm.selec.anio==id;
			}

			function selNivel(id){
				var temp;
				var existe=false;
				for (var i = 0; i < vm.selec.nivel.length; i++) {
					if (vm.selec.nivel[i]==id) {
						//console.log('Index: '+i);
						vm.selec.nivel[i]=vm.selec.nivel[vm.selec.nivel.length-1];
						vm.selec.nivel.pop();
						existe=true;
					}
				}
				if (!existe) {
					vm.selec.nivel.push(id);
				}

			}

			function nivelSel(id){
				for (var i = 0; i < vm.selec.nivel.length; i++) {
					if(vm.selec.nivel[i]==id){
						return true;
					}
				}
				return false;
			}

			function guardarRel(){
				for (var i = 0; i < vm.selec.nivel.length; i++) {
					grabador({niveles_id: vm.selec.nivel[i],anios_id: vm.selec.anio}).then(verificaStatus);
				}
			}

			function verificaStatus(){
				if (vm.contadorOps==vm.selec.nivel.length) {
					error.setAlerta('Se han guardado '+vm.contadorOps+' registros');
					vm.buscarDatos();
					vm.contadorOps=0;
					vm.selec={
						anio:0,
						nivel:[]
					};
				}
			}

			function grabador(data){
				//console.log(data);
				return nha.aDt(data).then(function(res){
						vm.contadorOps++;
					},function(res){
						vm.contadorOps++;
					});
			}

			function nivelExiste(id){
				for (var i = 0; i < vm.anios.data.length; i++) {
					if(vm.anios.data[i].id==vm.selec.anio){
						for (var j = 0; j < vm.anios.data[i].niveles_has_anios.length; j++) {
							if (vm.anios.data[i].niveles_has_anios[j].niveles_id==id) {
								return true;
							}
						}
					}
				}
				return false;
			}

			function delData(id){
				if (!$window.confirm('¿ Seguro que desea eliminar el elemento. Afectará alumnos, notas y asistencias asociadas. ?')) {
					return false;
				}
				return nha.dDt(id).then(function(res){
					error.setAlerta(res.data.msj);
					vm.buscarDatos();
				})
			}
    	}
	}
})();