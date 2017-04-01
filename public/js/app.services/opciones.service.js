(function(){
	'use strict';
	angular.module('escuela')
		.service('opcion',service);

	function service(GeneralesFactory,$localStorage,$interval){
		var vm=this;
		var info;

		// Variables

		// Funciones
		vm.buscarInfo=buscarInfo;
		vm.get=get;

		// Automáticas
		vm.buscarInfo();
		$interval(buscarInfo,5000);

		/////////
		function buscarInfo(){
			if( typeof($localStorage.currentUser)!=='object' ) return false;
			return GeneralesFactory.gDts().then(function(res){
				info=res.data;
			});
		}

		function get(nombre){
			if (typeof(info)!='undefined' && typeof(nombre)=='string') {
				for (var i = 0; i < info.length; i++) {
					if(info[i].nombre.toUpperCase()==nombre.toUpperCase()){
						return info[i].valor;
					}
				}
			}			
			return '';
		}
	}
})();