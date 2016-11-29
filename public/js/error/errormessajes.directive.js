(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('mensajes',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/error/error.html',
        	restrict: 'EA',
        	scope:{
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

    	function controller(error, animMsj,$timeout){
    		var vm=this;

			// Variables básicas
			vm.timeout=2000;
			vm.existe={
				alerta:false,
				error:false,
			};

			// Variables adicionales
	
			// Funciones basicas
			vm.listError=listError;
			vm.listAlerta=listAlerta;

			// Funciones adicionales
			
			// Lanzamiento Automático
			
			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			function listAlerta(){
				var res=error.getAlertaList();
				//console.log('Se lista alerta.');
				if(!vm.existe.alerta && res.length){
					vm.existe.alerta=true;
					ventanaMsj('alerta',function(){
						error.cleanAlerta();
						vm.existe.alerta=false;
					});
				}
				return res;
			}

			function listError(){
				var res=error.getErrorList();
				//console.log('Se lista error');
				if(!vm.existe.error && res.length){
					vm.existe.error=true;
					ventanaMsj('error',function(){
						error.cleanError();
						vm.existe.error=false;
					});
				}
				return res;
			}

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


			/////////////////////////// FUNCIONES BASICAS //////////////////////////////
			
    	}
	}
})();