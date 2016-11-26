(function(){
	'use strict';
	angular.module('escuela')
		.service('error',service);

	function service(animMsj,$timeout){
		var vm=this;

		// Variables
		vm.timeout=2000;
		vm.error={
			existe:false,
			msj:'',
		};
		vm.alerta={
			existe:false,
			msj:'',
		};

		// Funciones
		vm.setAlerta=setAlerta;
		vm.setError=setError;
		vm.getErrorStat=getErrorStat;
		vm.getAlertaStat=getAlertaStat;

		// Automáticas

		/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		
		// Lanza alerta.
		function setAlerta(msj){
			console.log('Alerta: '+msj);
			vm.alerta={
				existe:true,
				msj:msj,
			};
			ventanaMsj('alerta',function(){
				vm.alerta.existe=false;
			});
		}

		// Lanza errores
		function setError(msj){
			console.log('Error: '+msj);
			vm.error={
				existe:true,
				msj:msj
			};
			ventanaMsj('error',function(){
				vm.error.existe=false;
			});
		}

		// Funciones para mensajes
		function ventanaMsj(clase, callback){
			animMsj.show(clase,function(){
				$timeout(function(){
					volver(clase,callback);
				},vm.timeout);
			});
		}
		function volver(elem,callback){
			animMsj.hide(elem,function(){
				callback();
			});
		}

		function getErrorStat(){
			return vm.error.existe;
		}

		function getAlertaStat(){
			return vm.alerta.existe;
		}

		
	}
})();