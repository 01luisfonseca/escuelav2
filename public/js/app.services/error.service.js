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

		// Automáticas

		/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		
		// Lanza alerta.
		function setAlerta(msj){
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

		
	}
})();