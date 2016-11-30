(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('ProfesorCtrl',controller);

	function controller(error, animPage, AniosFactory, ProfesorFactory, $window){
		var vm=this;

		// Variables básicas
		vm.anios={};
		vm.empl={};
		vm.selec={
			anio:0,
			nivel:0,
			mat:0,
			empl:0
		};

		// Variables adicionales
	
		// Funciones basicas
		vm.buscarDatos=buscarDatos;
		vm.getAnios=getAnios;
		vm.getEmpleados=getEmpleados;
		vm.selecNivel=selecNivel;
		vm.selecMat=selecMat;
		vm.changeAnio=changeAnio;
		vm.changeNivel=changeNivel;
		vm.changeMat=changeMat;
		vm.selEmpl=selEmpl;
		vm.emplSel=emplSel;
		vm.infoCompleta=infoCompleta;
		vm.guardarRel=guardarRel;
		vm.delData=delData;

		// Funciones adicionales

		// Lanzamiento Automático
		animPage.show('profesores',function(){});
		vm.buscarDatos();

		/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function buscarDatos(){
			vm.getAnios();
			vm.getEmpleados();
		}

		function getAnios(){
			return AniosFactory.gDts().then(function(res){
				vm.anios=res;
			});
		}

		function getEmpleados(){
			return ProfesorFactory.gDts().then(function(res){
				vm.empl=res;
			})
		}

		function selecNivel(){
			if (vm.selec.anio==0) {
				return [];
			}
			//console.log(vm.selec);
			for (var i = 0; i < vm.anios.data.length; i++) {
				if (vm.anios.data[i].id==vm.selec.anio) {
					//console.log(vm.anios.data[i].niveles_has_anios);
					return vm.anios.data[i].niveles_has_anios;
				}
			}
			return [];
		}
		function selecMat(){
			if (vm.selec.anio==0 || vm.selec.nivel==0) {
				return [];
			}
			//console.log(vm.selec);
			for (var i = 0; i < vm.anios.data.length; i++) {
				if (vm.anios.data[i].id==vm.selec.anio) {
					for (var j = 0; j < vm.anios.data[i].niveles_has_anios.length; j++) {
						if (vm.anios.data[i].niveles_has_anios[j].id==vm.selec.nivel){
							//console.log(vm.anios.data[i].niveles_has_anios[j].materias_has_niveles);
							return matDispo(vm.anios.data[i].niveles_has_anios[j].materias_has_niveles);
						}
					}
				}
			}
			return [];
		}

		function matDispo(mats){
			var selec=[];
			for (var i = 0; i < mats.length; i++) {
				if(mats[i].empleados_id==0){
					selec.push(mats[i]);
				}
			}
			return selec;
		}

		function changeAnio(){
			vm.selec.nivel=0;
			vm.selec.mat=0;
			vm.selec.empl=0;
		}

		function changeNivel(){
			vm.selec.mat=0;
			vm.selec.empl=0;
		}

		function changeMat(){
			vm.selec.empl=0;
		}

		function selEmpl(id){
			vm.selec.empl=id;
		}

		function emplSel(){
			return vm.selec.empl;
		}

		function infoCompleta(){
			if(vm.selec.mat!=0 && vm.selec.empl!=0){
				return true;
			}
			return false;
		}

		function guardarRel(){
				var data={
					empleados_id: vm.selec.empl
				};
				return ProfesorFactory.mDt(vm.selec.mat,data).then(function(res){
					error.setAlerta(res.data.msj);
					vm.buscarDatos();
				},function(res){
					error.setError('No se puede guardar la relación');
				})
			}

		function delData(id){
			if (!$window.confirm('¿ Seguro que desea eliminar el elemento?. Afectará al profesor.')) {
				return false;
			}
			return ProfesorFactory.dDt(id).then(function(res){
				error.setAlerta(res.data.msj);
				vm.buscarDatos();
			})
		}
	}
})();