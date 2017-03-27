/* menu.directive.js */

/**
* @desc Presentación de menu de angular ui-router
* @example <div menu-dir></div>
*/
(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('menuDir',directive);
	function directive(){
		var directive = {
        	link: link,
        	templateUrl: 'js/layout/menu.html',
        	restrict: 'EA',
        	controller: controller,
        	controllerAs:'vm'
    	};
    	return directive;

    	function link(scope, element, attrs) {
      	/* */
    	}
    	function controller($http,$localStorage,perfil,$interval,GeneralesFactory){
    		var vm=this;

            // Variables
            vm.usuario={};
            vm.logo={};

            // Functiones
            vm.existeStorage=existeStorage;
            vm.nombreUser=nombreUser;
            vm.esAdmin=esAdmin;
            vm.esCoord=esCoord;
            vm.esProfe=esProfe;
            vm.esSoloAlumno=esSoloAlumno;
            vm.getLogo=getLogo

            // Automaticas
            vm.getLogo();

            /////////////
            function existeStorage(){
                if(typeof($localStorage.currentUser)=='object'){
                    return true;
                }
                return false;
            }
            function nombreUser(){
                return perfil.getInfo().name;
            }
            function esAdmin(){
                return perfil.getInfo().tipo_usuario_id==6;
            }
            function esCoord(){
                return perfil.getInfo().tipo_usuario_id>=5;
            }
            function esProfe(){
                return perfil.getInfo().tipo_usuario_id>=4;
            }
            function esSoloAlumno(){
                return perfil.getInfo().tipo_usuario_id==2;
            }
            function getLogo(){
                return GeneralesFactory.gLogo().then(function(res){
                    vm.logo=res.data;
                });
            }
    	}
	}
})();