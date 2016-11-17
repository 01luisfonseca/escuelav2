(function(){
	'use strict';
	angular
		.module('escuela',[
			// Compartidos
			'ngAnimate',
      'escuela.config',
			'escuela.core',
			'ui.router',
      'ui.router.middleware',
      'ngStorage',

			// De aplicacion
			'escuela.router',
			'escuela.presentacion',
		])
		.config(config)
		.run(run);

    ////////////////
    function config($stateProvider, $urlRouterProvider, $middlewareProvider){

        $middlewareProvider.map({
            'nobody':[function nobodyMiddleware(){
                            // Bloqueado
                    }],
            'everyone':[function everyoneMiddleware(){
                          this.next();
                    }],
            'autorizado':[function autorizadoMiddleware(){
                          this.next();
                    }],
            'redirautenticado':[function redirautenticadoMiddleware(){
                          this.next();
                    }],
          });

          $urlRouterProvider.otherwise("/authhome");

          $stateProvider
            .state('login', {
                url: "/login",
                 middleware: 'redirautenticado',
                views:{
                    'body':{
                        templateUrl: "/js/login/login.html",
                        controller: 'loginCtrl as vm',
                    }
                }               
            })
            .state('authhome', {
                url: "/authhome",
                middleware: 'autorizado',
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/authhome/authhome.html",
                        controller: 'authHomeCtrl as vm',
                    }
                }
            })
            .state('usuarios', {
                url: "/usuarios",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/usuarios/usuarios.html",
                        controller: 'usuariosCtrl as vm',
                    }
                }
            })
            .state('profile', {
                url: "/profile",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/usuarios/profile.html",
                        controller: 'profileCtrl as vm',
                    }
                }
            })
            .state('usuarioinfo', {
                url: "/usuarios/{id}",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/usuarios/usuarioinfo.html",
                        controller: 'usuarioInfoCtrl as vm',
                    }
                }
            })
            .state('opciones', {
                url: "/opciones",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/generales/generales.html",
                        controller: 'GenCtrl as vm',
                    }
                }
            })
            .state('empleados', {
                url: "/empleados",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/empleados/empleados.index.html",
                        controller: 'EmpleadosCtrl as vm',
                    }
                }
            });
    }

    function run($rootScope, $http, $location, $localStorage) {
        // keep user logged in after page refresh
        if ($localStorage.currentUser) {
            $http.defaults.headers.common.Authorization = 'Bearer ' + $localStorage.currentUser.access_token;
        }

        // redirect to login page if not logged in and trying to access a restricted page
        $rootScope.$on('$locationChangeStart', function (event, next, current) {
            var publicPages = ['/login'];
            var restrictedPage = publicPages.indexOf($location.path()) === -1;
            if (restrictedPage && !$localStorage.currentUser) {
                $location.path('/login');
            }
        });
    }
})();