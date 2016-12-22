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
            'ngDialog',

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
            })
            .state('plan', {
                url: "/plan",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/plan/index.html",
                        controller: 'PlanCtrl as vm',
                    }
                }
            })
            .state('anios', {
                url: "/anios",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/anios/index.html",
                        controller: 'AniosCtrl as vm',
                    }
                }
            })
            .state('periodos', {
                url: "/periodos",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/periodos/index.html",
                        controller: 'PeriodosCtrl as vm',
                    }
                }
            })
            .state('niveles', {
                url: "/niveles",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/niveles/index.html",
                        controller: 'NivelesCtrl as vm',
                    }
                }
            })
            .state('materias', {
                url: "/materias",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/materias/index.html",
                        controller: 'MateriasCtrl as vm',
                    }
                }
            })
            .state('profesores', {
                url: "/profesores",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/profesores/index.html",
                        controller: 'ProfesorCtrl as vm',
                    }
                }
            })
            .state('alumnos', {
                url: "/alumnos",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/alumnos/index.html",
                        controller: 'AlumnosCtrl as vm',
                    }
                }
            })
            .state('asistencia', {
                url: "/asistencia",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/asistencia/index.html",
                        controller: 'AsistenciasCtrl as vm',
                    }
                }
            })
            .state('authdevice', {
                url: "/authdevice",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/authdevice/index.html",
                        controller: 'AuthdeviceCtrl as vm',
                    }
                }
            })
            .state('notas', {
                url: "/notas",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/notas/index.html",
                        controller: 'NotasCtrl as vm',
                    }
                }
            })
            .state('ingresos', {
                url: "/ingresos",
                middleware: 'autorizado',   
                views:{
                    'menu':{
                        template:'<div menu-dir></div>'
                    },
                    'body':{
                        templateUrl: "/js/ingresos/index.html",
                        controller: 'IngresosCtrl as vm',
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