(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('relMateria',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/materias/rel.html',
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

    	function controller(AniosFactory,MateriasFactory, MateriasHasNivelesFactory, error, $window){
    		var vm=this;
    		var nha=MateriasHasNivelesFactory;

			// Variables básicas
			vm.anios={};
			vm.niveles={};
			vm.selec={
				anio:0,
				nivel:0,
				mat:0
			};

			// Variables adicionales
	
			// Funciones basicas
			vm.buscarDatos=buscarDatos;
			vm.infoCompleta=infoCompleta;
			vm.selAnio=selAnio;
			vm.anioSel=anioSel;
			vm.selNivel=selNivel;
			vm.nivelSel=nivelSel;
			vm.selMat=selMat;
			vm.matSel=matSel;
			vm.guardarRel=guardarRel;
			vm.matExiste=matExiste;
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
				return MateriasFactory.gDts().then(function(res){
					vm.materias=res;
					return vm.materias;
				});
			}

			function infoCompleta(){
				if(vm.selec.anio!=0 && vm.selec.nivel!=0 && vm.selec.mat!=0){
					return true;
				}
				return false;
			}
			
			function selAnio(id){
				vm.selec.anio=id;
				vm.selec.nivel=0;
				vm.selec.mat=0;
				//console.log(vm.selec);
				vm.niveles=buscarNivelEnAnio(id);
				//console.log(vm.niveles);

			}

			function buscarNivelEnAnio(id){
				for (var i = vm.anios.data.length - 1; i >= 0; i--) {
					if (vm.anios.data[i].id==id) {
						//console.log(vm.anios.data[i].niveles_has_anios);
						return vm.anios.data[i].niveles_has_anios;
					}
				}
				return {};
			}

			function anioSel(id){
				return vm.selec.anio==id;
			}

			function selNivel(id){
				vm.selec.nivel=id;
				vm.selec.mat=0;
			}

			function nivelSel(id){
				return vm.selec.nivel==id;
			}

			function selMat(id){
				vm.selec.mat=id;
			}

			function matSel(id){
				return vm.selec.mat==id;
			}

			function guardarRel(){
				var data={
					niveles_has_anios_id: vm.selec.nivel,
					materias_id: vm.selec.mat
				};
				return nha.aDt(data).then(function(res){
					error.setAlerta(res.data.msj);
					vm.buscarDatos();
				},function(res){
					error.setError('No se puede guardar la relación');
				})
			}

			function matExiste(id){
				for (var i = 0; i < vm.anios.data.length; i++) {
					if(vm.anios.data[i].id==vm.selec.anio){
						//console.log('Adentro Años');
						for (var j = 0; j < vm.anios.data[i].niveles_has_anios.length; j++) {
							if (vm.anios.data[i].niveles_has_anios[j].id==vm.selec.nivel) {
								//console.log('Adentro Nivel');
								for (var k = 0; k < vm.anios.data[i].niveles_has_anios[j].materias_has_niveles.length; k++) {
									if(vm.anios.data[i].niveles_has_anios[j].materias_has_niveles[k].materias_id==id){
										//console.log('Adentro Materias');
										return true;
									}
								}
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