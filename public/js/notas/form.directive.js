(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formNotas',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/notas/form.html',
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
    		AniosFactory, 
    		IndicadoresFactory,
    		TipoNotaFactory, 
    		NotasFactory,
    		ngDialog,
    		error,
    		$window
    		){
    		var vm=this;

			// Variables básicas
			vm.porcentajeMaximo=100;    // Maximo porcentaje de indicador
            vm.ventanaSeleccionada=0;   // Ventana seleccionada para mostrar notas.
            vm.cargando={               // Estado de carga de la aplicación
                indicadores:false,
                notas:false,
            };
            vm.indicadores={};          // Recoge los indicadores
            vm.notasRaw={};             // Recoge a los alumnos y a las notas de la API
            vm.nuevoIndic={};           // Almacena el nuevo indicador 
            vm.promedios={};            // Almacena los totalizados de los periodos.
            vm.mensajeTipo='';          // Tiene las respuestas de las actualizaciones de tipos de notas, en el modal
            vm.tempoAlumno=[];          // Matriz de definitivas de alumnos e indicadores.

			// Variables adicionales
			vm.anios={};
			vm.sel={
				anio:0,
				nivel:0,
				mat:0,
				per:0,
                tnota:0
			};
	
			// Funciones basicas
			vm.buscarIndicadores=buscarIndicadores	// Busca indicadores
			vm.actIndicador=actIndicador;           // Actualiza el indicador según el $index.
            vm.borrarIndicador=borrarIndicador;     // Borra el indicador según el ID
            vm.addIndicador=addIndicador;           // Añade indicador.
            vm.actTipo=actTipo;                     // Actualiza los tipos de notas
            vm.crearTipo=crearTipo;                 // Crea los tipos de notas para un indicador.
            vm.eliminarTipo=eliminarTipo;           // Elimina un tipo de nota
            vm.actNota=actNota;                     // actualiza una nota.
            vm.esSeleccionada=esSeleccionada;   	// Verifica que una ventana es seleccionada
            vm.selecciona=selecciona;           	// Selecciona una ventana a mostrar.
            vm.modalTipo=modalTipo;					// Abre ngDialog con el modal del tipo de notas
            vm.selModal=selModal;                   // Verifica el tipo de nota seleccionado

			// Funciones adicionales
			vm.datos=datos;
			vm.getAnios=getAnios;
			vm.cambio=cambio;
            
			// Lanzamiento Automático

			// Lanzamiento obligatorio
			vm.getAnios();

			/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////
			
			/* Funcion que devuelve los tados para los option, según lo solicitado */
			function datos(elem){
				if(typeof(vm.anios.data)!='undefined'){
				for (var i = 0; i < vm.anios.data.length; i++) {
					if (vm.anios.data[i].id==vm.sel.anio && elem=='nivel') {
						return vm.anios.data[i].niveles;
					}
					for (var j = 0; j < vm.anios.data[i].niveles.length; j++) {
						if (vm.anios.data[i].niveles[j].id==vm.sel.nivel && elem=='mat') {
							return vm.anios.data[i].niveles[j].materias;
						}
						for (var k = 0; k < vm.anios.data[i].niveles[j].materias.length; k++) {
							if (vm.anios.data[i].niveles[j].materias[k].id==vm.sel.mat && elem=='per') {
								return vm.anios.data[i].niveles[j].materias[k].periodos;
							}
						}
					}
				}}
				return [];
			}

			/* Busca en el API los años guardados que pueden ser visualizados. */
			function getAnios(){
				return AniosFactory.gADts().then(function(res){
					vm.anios=res;
				});
			}

			function cambio(cam){
				switch(cam){
					case 'anio':
					vm.sel.nivel=0;
					case 'nivel':
					vm.sel.mat=0;
					case 'mat':
					vm.sel.per=0;
                    vm.sel.tnota=0;
					return true;
					break;
				}
				return false;
			}

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			// Funcion que recoge los indicadores, tipos de nota, notas y alumnos
            function buscarIndicadores(){
                vm.indicadores={};
                vm.cargando.indicadores=true;
                return IndicadoresFactory.gSDt(vm.sel.per).then(
				    function(response){
				        vm.indicadores=response.data;
                        vm.cargando.indicadores=false;
                        porcentajeMax();
                        actPromedio();
				});
            }
            
            // Calcula el promedio de los indicadores
            function promIndicadores(){
                // Calculamos el promedio de tipos de nota en cada alumno
                for(var i=0; i<vm.indicadores.length; i++){
                    for(var j=0; j<vm.indicadores[i].alumnos.length; j++){
                        var defin =0;
                        for(var k=0; k<vm.indicadores[i].alumnos[j].tipo_nota.length; k++){
                            var tempo=parseFloat(vm.indicadores[i].alumnos[j].tipo_nota[k].cal);
                            vm.indicadores[i].alumnos[j].tipo_nota[k].cal=tempo;
                            defin+=parseFloat(vm.indicadores[i].alumnos[j].tipo_nota[k].cal)
                        }
                        defin=defin/vm.indicadores[i].alumnos[j].tipo_nota.length;
                        vm.indicadores[i].alumnos[j].prom=defin;
                    }
                }
            }
            
            // Calcula el promedio restante de indicador nuevo
            function porcentajeMax(){
                var sumador=0;
                try {
				    for (var i = vm.indicadores.length - 1; i >= 0; i--) {
					   vm.indicadores[i].porcentaje=parseFloat(vm.indicadores[i].porcentaje);
					   sumador=sumador+vm.indicadores[i].porcentaje;
				    }
                }
                catch(err){
                    vm.porcentajeMaximo=100;
                }
                if (sumador>0) {
				    if (sumador<100) {
					   vm.porcentajeMaximo=100-sumador;
				    }else{
					   vm.porcentajeMaximo=0;
				    }
                }else{
				    vm.porcentajeMaximo=100;
                }
            }

            // Actualizar indicador
            function actIndicador(index){
                return IndicadoresFactory.mDt(vm.indicadores[index].id,vm.indicadores[index]).then(
				function(response){
					vm.indicadores[index].visible=false;
                    actPromedio();
				}
                );
            }
            
            // Eliminar indicador
            function borrarIndicador(id){
                vm.cargando.indicadores=true;
                if ($window.confirm('¿Desea eliminar el indicador? Se eliminarán los tipos de nota y las notas asociadas.')) {
				    return IndicadoresFactory.dDt(id).then(
					   function(response){                         
                           error.setAlerta(response.data.msj);
                           vm.buscarIndicadores();
                       });
                }
            }
            
            // Añadir un indicador
            function addIndicador(){
            	vm.nuevoIndic.materias_has_periodos_id=vm.sel.per;
                return IndicadoresFactory.aDt(vm.nuevoIndic).then(
                    function(response){
                        vm.nuevoIndic={};
                        vm.buscarIndicadores();
				});
            }
            
            // Actualiza la tabla de promedios
            function actPromedio(){
                vm.promedios={};
                // Calculamos los promedios de los indicadores
                promIndicadores();
                vm.promedios.indicadores=new Array;
                // Se asignan los indicadores disponibles al promedio
                for (var x = 0; x < vm.indicadores.length; x++) {
                    vm.promedios.indicadores[x]={};
                    vm.promedios.indicadores[x].id=vm.indicadores[x].id;
                    vm.promedios.indicadores[x].nombre=vm.indicadores[x].nombre;
                    vm.promedios.indicadores[x].porcentaje=vm.indicadores[x].porcentaje;
                }
                vm.promedios.alumnos=new Array;
                // Se agregan los alumnos disponibles al promedio
                for (var x=0; x<vm.indicadores[0].alumnos.length; x++){
                    vm.promedios.alumnos[x]={};
                    vm.promedios.alumnos[x].id=vm.indicadores[0].alumnos[x].id;
                    vm.promedios.alumnos[x].users_id=vm.indicadores[0].alumnos[x].users_id;
                    vm.promedios.alumnos[x].name=vm.indicadores[0].alumnos[x].name;
                    vm.promedios.alumnos[x].lastname=vm.indicadores[0].alumnos[x].lastname;
                    vm.promedios.alumnos[x].indicadores=vm.promedios.indicadores; // Asignar los indicadores existentes en promedio a cada alumno.
                    vm.promedios.alumnos[x].def=0;
                }
                // Calculando las definitivas de cada indicador por alumno e indicador.
                vm.tempoAlumno=[];
                angular.forEach(vm.promedios.alumnos,function(alumnoP){
                    angular.forEach(alumnoP.indicadores, function(indicadorP){
                        angular.forEach(vm.indicadores, function(indicador){
                            if(indicador.id===indicadorP.id){
                                angular.forEach(indicador.alumnos, function(alumnoI){
                                    // En caso de que el código de alumnos sea igual
                                    if(alumnoI.id===alumnoP.id){
                                        // Creando arreglo temporal de promedios
                                        vm.tempoAlumno.push({
                                            alumnos_id: alumnoI.id, 
                                            indicador_id: indicador.id,
                                            indicador_def: alumnoI.prom,
                                            indicador_porcentaje: indicador.porcentaje
                                        });
                                    }
                                });
                                
                            }
                        });
                    });
                });
                //Revisar de nuevo la tabla de promedios
                angular.forEach(vm.promedios.alumnos, function(alumno){
                    // Retorna un arreglo, que remplaza la referencia anterior de indicadores.
                    alumno.indicadores=buscarPromIndic(alumno.id);
                });
                // Cálculo de definitivas del periodo
                for(var i=0; i<vm.promedios.alumnos.length; i++){
                    for(var j=0; j<vm.promedios.alumnos[i].indicadores.length; j++){
                        var def = parseFloat(vm.promedios.alumnos[i].indicadores[j].porcentaje) * parseFloat(vm.promedios.alumnos[i].indicadores[j].def)/100;
                        vm.promedios.alumnos[i].def+=def;
                    }
                }
			}
            
            //Buscador de alumno, indicador y notas, basado en array de salida
            function buscarPromIndic(alumnoId){
                var temArray=[];
                for(var r=0;r<vm.tempoAlumno.length; r++){
                    // Si encuentra al alumno, lo acumula en un arreglo del alumno
                    if(vm.tempoAlumno[r].alumnos_id==alumnoId){
                        temArray.push({
                            id:vm.tempoAlumno[r].indicador_id, 
                            def:vm.tempoAlumno[r].indicador_def,
                            porcentaje: vm.tempoAlumno[r].indicador_porcentaje
                        });
                    }
                }
                // Retorna lo engontrado del alumno.
                return temArray;
            }
            
            /* Fin de funciones sobre indicadores, siguen cálculo de notas */
            
            // Crear tipo de nota por indicador
            function crearTipo(indId){
                vm.cargando.indicadores=true;
                var data={
                	indicadores_id:indId
                };
                return TipoNotaFactory.aDt(data).then(function(res){
                    error.setAlerta(res.data.msj);
                    vm.buscarIndicadores();
                });
            }
            
            //Actualizar los tipos de notas
            function actTipo(indIndex,tipoIndex){
                return TipoNotaFactory.mDt(
                		vm.indicadores[indIndex].tipo_nota[tipoIndex].id,
                		vm.indicadores[indIndex].tipo_nota[tipoIndex]
                		)
                    .then(function(res){
                    error.setAlerta(res.data.msj);
                });
            }
            
            // Elimina un tipo de nota por Id.
            function eliminarTipo(tipoId){
                if($window.confirm('¿Desea eliminar el tipo de nota? Se eliminarán las notas asociadas.')){
                    vm.cargando.indicadores=true;
                    return TipoNotaFactory.dDt(tipoId).then(function(res){
                        error.setAlerta(res.data.msj);
                        vm.buscarIndicadores();
                    });
                }
            }
            
            // Actualiza una nota
            function actNota(notaId,cal){
            	var data={
            		calificacion: cal
            	};
                return NotasFactory.mDt(notaId,data).then(function(res){
                    actPromedio();
                });
            }
            
            /****** Control de ventanas *******/
            
            // Verifica la seleccion de ventana
            function esSeleccionada(ventana){
                return vm.ventanaSeleccionada===ventana
            }
            
            // Selecciona ventana
            function selecciona(id){
                vm.ventanaSeleccionada=id;
            }

            // Sobre modales con ngDialog
            function modalTipo(id){
                if (id==0) {
                    buscarIndicadores(vm.sel.per);
                }
                vm.sel.tnota=id;
            }

            // Ver modal
            function selModal(id){
                return vm.sel.tnota==id;
            }
			
    	}
	}
})();