(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formAsistencia',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/asistencia/form.html',
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

    	function controller(NewasistenciaFactory, MatasistenciaFactory, AlumnosFactory, error, $timeout){
    		var vm=this;

			// Variables básicas

			// Variables adicionales
			vm.alumnos={};
			vm.newasistencias={};
			vm.matasistencias={};
			vm.sel={
				alumno:0,
				periodo:0,
				fecha: new Date(),
				hora: 0
			};
			vm.buscado='';
			vm.buscando=false;
	
			// Funciones basicas
			vm.newDataNA=newDataNA;
			vm.buscarData=buscarData
			vm.accion=accion;
			vm.selAlumno=selAlumno;
			vm.alumnoSel=alumnoSel;
			vm.selPeriodo=selPeriodo;
			vm.periodoSel=periodoSel;
			vm.minDate=minDate;
			vm.maxDate=maxDate;
			vm.getPeriodos=getPeriodos;

			// Funciones adicionales
			
			// Lanzamiento Automático

			// Lanzamiento obligatorio


			/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////


			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function newDataNA(data){
				return NewasistenciaFactory.aDt(data).then(function(res){
					//console.log(res);
					error.setAlerta('Se ha creado el registro.');
					vm.sel={
						alumno:0,
						periodo:0,
						fecha: new Date(),
						hora:0
					};
				},function(res){
					error.setError('Se ha presentado un error. No se crea el registro.');
				});
			}

			function accion(){
				
				var year=vm.sel.fecha.getFullYear();
				var month=vm.sel.fecha.getMonth();
				var day=vm.sel.fecha.getDate();
				var hours=vm.sel.hora.getHours();
				var minutes=vm.sel.hora.getMinutes();
				var fecha= new Date(year, month, day, hours, minutes, 0, 0);
				var data={
					alumnos_id:vm.alumnos.data[vm.sel.alumno-1].id,
					periodos_id:vm.alumnos.data[vm.sel.alumno-1].niveles_has_anios.anios.periodos[vm.sel.periodo-1].id,
					fecha: fecha
				};
				//console.log(data);
				return newDataNA(data); 
			}

			function buscarData(){
				if(vm.buscado.length>2){
					if (!vm.buscando) {
						vm.buscando=true;
						$timeout(searchData,1500);
					}
				}
				if(vm.buscado==''){
					vm.alumnos={};
				}
				return false;
			}

			function searchData(){
				return AlumnosFactory.gSDt(vm.buscado).then(function(res){
					//console.log(vm.users);
					vm.alumnos=res;
					vm.buscando=false;
				},function(res){
					vm.buscando=false;
					error.setError('Se ha presentado un error. No se busca el registro.');
				});
			}

			function selAlumno(index){
				vm.sel.alumno=index+1;
				//console.log(vm.sel);
			}

			function alumnoSel(index){
				return vm.sel.alumno==index+1;
			}

			function selPeriodo(index){
				vm.sel.periodo=index+1;
				//console.log(vm.sel);
			}

			function periodoSel(index){
				return vm.sel.periodo==index+1;
			}

			function minDate(){
				try{
					var fecha_ini=vm.alumnos.data[vm.sel.alumno-1].niveles_has_anios.anios.periodos[vm.sel.periodo-1].fecha_ini;
					return new Date(fecha_ini);
				}catch(err){
					return new Date();
				}
			}

			function maxDate(){
				try{
					var fecha_fin=vm.alumnos.data[vm.sel.alumno-1].niveles_has_anios.anios.periodos[vm.sel.periodo-1].fecha_fin;
					return new Date(fecha_fin);
				}catch(err){
					return new Date();
				}
			}

			function getPeriodos(){
				if(vm.sel.alumno>0){
					//console.log();
					return vm.alumnos.data[vm.sel.alumno-1].niveles_has_anios.anios.periodos;
				}else{
					return [];
				}
			}
    	}
	}
})();