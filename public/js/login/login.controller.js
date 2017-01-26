(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('loginCtrl',controller);

		function controller($location, $localStorage, AuthenticationFactory,animPage,perfil,$window){
			var vm=this;
			vm.login={
				username:'',
				password:'',
			};
				// Funciones
			vm.loginUser=loginUser;
			vm.logoutUser=logoutUser;

			// Lanzamiento automático
			animPage.show('login',function(){});
			vm.logoutUser();
			
			///////////////
			function loginUser(){
				console.log('Iniciando sesión...');
				vm.loading=true;
				AuthenticationFactory.Login(vm.login, respuesta);
			}

			function respuesta(result){
				//console.log(typeof($localStorage.currentUser));
                if (result === true) {
                    $location.path('/authhome');
                    perfil.buscarInfo();
                    console.log('Sesión iniciada.');
                } else {
                    vm.error = 'Usuario o contraseña incorrectos';
                    vm.loading = false;
                    $window.alert(vm.error);
                }
			}

			function logoutUser(){
				console.log('Sesión cerrada.');
				AuthenticationFactory.Logout();
				//console.log(typeof($localStorage.currentUser));
			}
		}
})();