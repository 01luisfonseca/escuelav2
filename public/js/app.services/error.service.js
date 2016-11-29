(function(){
	'use strict';
	angular.module('escuela')
		.service('error',service);

	function service(){
		var vm=this;

		// Variables
		vm.error=[];
		vm.alerta=[];

		/*
		vm.timeout=2000;
		vm.error={
			existe:false,
			msj:'',
		};
		vm.alerta={
			existe:false,
			msj:'',
		};*/

		// Funciones
		vm.setAlerta=setAlerta;
		vm.setError=setError;
		vm.getErrorList=getErrorList;
		vm.getAlertaList=getAlertaList;
		vm.cleanAlerta=cleanAlerta;
		vm.cleanError=cleanError;

		// Automáticas

		/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		
		// Lanza alerta.
		function setAlerta(msj){
			vm.alerta.push(msj);
			/*console.log('Alerta: '+msj);
			vm.alerta={
				existe:true,
				msj:msj,
			};
			ventanaMsj('alerta',function(){
				vm.alerta.existe=false;
			});*/
		}

		// Lanza errores
		function setError(msj){
			vm.error.push(msj);
			/*console.log('Error: '+msj);
			vm.error={
				existe:true,
				msj:msj
			};
			ventanaMsj('error',function(){
				vm.error.existe=false;
			});*/
		}

		// Funciones para mensajes
		function getErrorList(){
			return vm.error;
		}

		function getAlertaList(){
			return vm.alerta;
		}

		function cleanError(){
			vm.error=[];
		}

		function cleanAlerta(){
			vm.alerta=[];
		}

		
	}
})();