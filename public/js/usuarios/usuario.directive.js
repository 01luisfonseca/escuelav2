(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('usuario',directive);
	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/usuarios/usuario.html',
        	restrict: 'EA',
        	scope:{
        		nuevo:'=',
        		existente:'='
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

    	function controller(UsersFactory,TipoFactory,animMsj,$timeout){
    		var vm=this;

    		// Variables
    		vm.user={};
    		vm.tipo_usuario={};
    		vm.error={
    			existe:false,
    			msj:''
    		};
    		vm.alerta={
    			existe:false,
    			msj:''
    		};

    		// Funciones
    		vm.autoIni=autoIni;
    		vm.obtenerTipos=obtenerTipos;
    		vm.gestionUser=gestionUser;
    		vm.validaPass=validaPass;

    		// Lanzamiento Automático
			vm.autoIni();

			//////////////////////// 
			function autoIni(){
				vm.obtenerTipos();
				if (typeof(vm.nuevo)=='undefined') {
					if (typeof(vm.existente)=='undefined') {
						console.log('No se hace ninguna acción en el inicio del controlador de Usuario');
						return false;
					}
				}
			}

			function obtenerTipos(){
				return getTipos().then(function(){
					//console.log(vm.tipo_usuario);
				});
			}
			function getTipos(){
				return TipoFactory.getTipos().then(function(res){
					vm.tipo_usuario=res;
					return vm.tipo_usuario;
				});
			}

			function gestionUser(){
				console.log(vm.user);
				return newUser(vm.user);
			}
			function newUser(data){
				console.log(data);
				if (!vm.validaPass()) {
					console.log('No pasa password.');
					return false;
				}
				return UsersFactory.addUser(data).then(function(res){
					console.log(res);
				},function(res){
					lanzaError('Falta información para almacenar el usuario');
				});
			}

			
			// Validación de contraseñas
			function validaPass(){
				if (vm.user.password!=vm.user.repassword) {
					lanzaError('Las contraseñas no coinciden.');
					return false;
				}else{
					return true;
				}
			}

			// Lanza errores
			function lanzaError(msj){
				vm.error={
					existe:true,
					msj:msj
				};
				errorMsj('error',function(){
					vm.error.existe=false;
				});
			}

			// Lanza alertas
			function lanzaAlerta(msj){
				vm.alerta={
					existe:true,
					msj:msj
				};
				errorMsj('alerta',function(){
					vm.alerta.existe=false;
				});
			}

			// Funciones para mensajes
			function errorMsj(clase, callback){
				animMsj.show(clase,function(){
					$timeout(function(){
						noError(clase,callback);
					},2000);
				});
			}
			function noError(elem,callback){
				animMsj.hide(elem,function(){
					callback();
				});
			}
    	}
	}
})();