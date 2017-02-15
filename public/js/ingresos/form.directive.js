(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('formIngreso',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/ingresos/form.html',
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
    		MesesFactory, 
    		AlumnosFactory, 
    		PensionFactory, 
    		MatriculasFactory, 
    		OtrosFactory,
    		error,
    		$window,
    		$timeout,
    		$http)
		{
    		var vm=this;

			// Variables básicas
			vm.ufac={};
			vm.yaBuscado=false;
			vm.alumnos={};
			vm.historial={};
			vm.meses={};
			vm.pago={
				alumnoTxt:'',
				valorPensionInicial:0,
				valorMatriculaInicial:0,
				tipo:'',
				mes_id:0,
				valor:0,
				faltante:0,
				alumnos_id:0
			};

			// Variables adicionales
	
			// Funciones basicas
			vm.verificarRestante=verificarRestante;
			vm.actualizarHistorial=actualizarHistorial;	// Busca el historial de facturas segun seleccion.
			vm.verificarFactura=verificarFactura;		// Verifica si una factura ya existe.
			vm.buscarMeses=buscarMeses;					// Busca meses en la base de datos.
			vm.verificarPago=verificarPago;				// Ajusta el valor del saldo de diferencia
			vm.esPension=esPension;						// Verifica si es pension.
			vm.esSeleccion=esSeleccion;					// Verifica si el alumno esta seleccionado
			vm.seleccionarAlumno=seleccionarAlumno;		// Funcion que selecciona al alumno y sus valores
			vm.verificarPago=verificarPago;				// Verifica el tipo de pago y ajusta el valor
			vm.buscarAlumnos=buscarAlumnos;				// Busca alumno por el número
			vm.accion=accion;							// Guarda el registro.
			vm.imprimirtirilla=imprimirtirilla;			// Imprime tirilla de pago.
			
			// Funciones adicionales
			
			// Lanzamiento Automático

			// Lanzamiento obligatorio
			vm.buscarMeses();
			buscarUFac();

			///////////////////////// FUNCIONES ADICIONALES /////////////////////////////
			

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			function buscarUFac(){
				$http.get('/api/ultimafac').then(function(res){
					vm.ufac=res.data;
					ajustaFactura();
				},function(res){
					$window.alert('Se han presentado problemas de conexión. Por favor revise la conexión y actualice la página.');
					vm.ufac.numero_factura='Desconocido / Error';
				}
				);
				////////////
				function ajustaFactura(){
					var num=parseInt(vm.ufac.numero_factura.replace(/[^0-9]/i,''));
					var cab=vm.ufac.numero_factura.replace(/\d+/i,'');
					vm.pago.numero_factura=''+cab+(num+1);
				}
			}
			function actualizarHistorial(id){
				var factory=obtenerFactory();
				factory.gAl(id).then(
					function(response){
						vm.historial=response.data;
					}
				);
			}

			function obtenerFactory(){
				switch(vm.pago.tipo){
					case 'pension':
						return PensionFactory;
						break;
					case 'matricula':
						return MatriculasFactory;
						break;
					case 'otros':
						return OtrosFactory;
						break;
				}
			}

			function verificarFactura(num){
				PensionFactory.gEFac(num).then(function(res){
					return lanzaErrorFac(true);
				},function(res){
					MatriculasFactory.gEFac(num).then(function(res){
						return lanzaErrorFac(true);
					},function(res){
						OtrosFactory.gEFac(num).then(function(res){
							return lanzaErrorFac(true);
						},function(res){
							return lanzaErrorFac(false);
						});
					});
				});
			}

			function lanzaErrorFac(bol){
				if (bol) {
					$window.alert('El número de factura ya existe. Por favor cámbielo o verifique la facturación.');
				}
				return bol;
			}

			function buscarMeses(){
				return MesesFactory.gDts().then(function(res){
					vm.meses=res.data;
				});
			}

			function verificarRestante(){
				switch(vm.pago.tipo) {
    				case 'pension':
        				vm.pago.faltante=parseFloat(vm.pago.valorPensionInicial)-vm.pago.valor;
        				break;
    				case 'matricula':
        				vm.pago.faltante=parseFloat(vm.pago.valorMatriculaInicial)-vm.pago.valor;
        				break;
				}
			}

			function esPension(){
				if (vm.pago.tipo=='pension') {
					return true;
				}else{
					return false;
				}
			}

			function esSeleccion(id){
				return vm.pago.alumnos_id===id;
			}

			function seleccionarAlumno(id,pension,matricula){
				vm.pago.alumnos_id=id;
				vm.pago.valorPensionInicial=pension;
				vm.pago.valorMatriculaInicial=matricula;
				vm.verificarPago();
			}

			function verificarPago(){
				switch(vm.pago.tipo) {
    				case 'pension':
        				vm.pago.valor=parseFloat(vm.pago.valorPensionInicial);
        				break;
    				case 'matricula':
        				vm.pago.valor=parseFloat(vm.pago.valorMatriculaInicial);
        				break;
        			case 'otros':
        				break;
				}
				vm.verificarRestante();
				if (vm.pago.alumnos_id!=0 && vm.pago.alumnoTxt!='') {
					vm.actualizarHistorial(vm.pago.alumnos_id);
				}
			}

			function buscarAlumnos(){
				if(vm.pago.alumnoTxt.length>2){
					if (!vm.yaBuscado) {
						vm.yaBuscado=true;
						$timeout(searchData,1000);
					}
				}
				if(vm.pago.alumnoTxt==''){
					vm.alumnos={};
				}
				return false;
			}
			function searchData(){
				vm.yaBuscado=false;
				return AlumnosFactory.gSDt(vm.pago.alumnoTxt).then(function(res){
					vm.alumnos=res.data;
				});
			}

			function accion(){
				var factura=obtenerFactory();
				return factura.aDt(vm.pago).then(
					function(response){
						error.setAlerta(response.data.msj);
						vm.imprimirtirilla();
						vm.alumnos={};
						vm.historial={};
						vm.pago={
							alumnoTxt:'',
							valorPensionInicial:0,
							valorMatriculaInicial:0,
							tipo:'',
							mes_id:0,
							valor:0,
							faltante:0,
							alumnos_id:0
						};
						buscarUFac();
					},
					function(response){
						error.setError('El registro no se almacena. Por favor verifique la información.');
					}
				);
			}

			function imprimirtirilla(){
				$window.open('/#/facturacobro/'+vm.pago.tipo+'/'+vm.pago.numero_factura);
			}
    	}
	}
})();