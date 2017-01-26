(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('alumnoInfoCtrl',controller);
	function controller(animPage,$stateParams){
		var vm=this;

		// Variables
		vm.id=$stateParams.id;
	
		// Funciones

		// Lanzamiento Automático
		animPage.show('alumno',function(){});

		///////////////////////////
	}
})();