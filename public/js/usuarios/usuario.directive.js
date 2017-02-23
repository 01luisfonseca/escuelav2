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

    	function controller(UsersFactory,TipoFactory,animMsj,$timeout,perfil,$window, $location){
    		var vm=this;

    		// Variables
    		vm.user={};
    		vm.tipo_usuario={};
    		vm.perfil={};
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
    		vm.esAdmin=esAdmin;
    		vm.esElMismo=esElMismo;
    		vm.delData=delData;

    		// Lanzamiento Automático
			vm.autoIni();

			//////////////////////// 
			function autoIni(){
				vm.obtenerTipos();
				buscarInfo();
				if (typeof(vm.nuevo)=='undefined') {
					if (typeof(vm.existente)=='undefined') {
						console.log('No se hace ninguna acción en el inicio del controlador de Usuario');
						return false;
					}else{
						getInfoUser(vm.existente);
					}
				}
			}

			function buscarInfo(){
				vm.perfil= perfil.getInfo();
			}

			function getInfoUser(id){
				if (vm.perfil.id==id) {
					vm.user=perfil.getInfo();
					vm.user.birday=new Date(vm.user.birday);
					vm.user.estado=parseInt(vm.user.estado);
					return true;
				}else{
					return UsersFactory.getUser(id).then(function(res){
						vm.user=res.data;
						vm.user.birday=new Date(vm.user.birday);
						vm.user.estado=parseInt(vm.user.estado);
					});
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
				if(vm.nuevo){
					return newUser(vm.user);
				}
				if(vm.existente){
					if (vm.existente==vm.perfil.id) {
						return actPerfil(vm.user);
					}
					return actUser(vm.user);
				}
				
			}
			function newUser(data){
				//console.log(data);
				if (!vm.validaPass()) {
					console.log('No pasa password.');
					return false;
				}
				return UsersFactory.addUser(data).then(function(res){
					//console.log(res);
					vm.user={};
					lanzaAlerta(res.data.msj);
				},function(res){
					lanzaError('Falta información para almacenar el usuario, o el número de identificación ya existe');
				});
			}

			function actUser(data){
				return UsersFactory.modUser(vm.existente,data).then(function(res){
					lanzaAlerta(res.data.msj);
                    $location.path('/usuarios');
				},function(res){
					lanzaError('Falta información para almacenar el usuario');
				});				
			}

			function actPerfil(data){
				return perfil.setInfo(data).then(function(res){
					lanzaAlerta(res.data.msj);
                    $location.path('/authhome');
				},function(res){
					lanzaError('Falta información para actualizar el perfil');
				});
			}

			function esAdmin(){
				return perfil.esAdmin();
			}

			function esElMismo(){
				if (vm.perfil.id==vm.existente) {
					return true;
				}
				return false;
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
			function delData(){
				if ($window.confirm('¿Seguro que desea eliminar el usuario? Se borrarán los registros asociados.')) {
					return UsersFactory.delUser(vm.existente).then(function(res){
						$window.alert('Se ha borrado el usuario correctamente');
						$window.history.back();
					});
				}
			}
    	}
	}
})();