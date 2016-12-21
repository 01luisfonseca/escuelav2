(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('TipoNotaCtrl',controller);

	function controller(animPage,$timeout){
		var vm=this;

		// Variables básicas
		vm.tab=1;
		vm.panel=0;

		// Variables adicionales
	
		// Funciones basicas
		vm.selectTab=selectTab;
		vm.tabSelec=tabSelec;
		vm.selecPanel=selecPanel;
		vm.esPanelSelec=esPanelSelec;
		vm.cerrarPanel=cerrarPanel;

		// Funciones adicionales

		// Lanzamiento Automático
		animPage.show('tiponota',function(){});

		/////////////////////////// FUNCIONES ADICIONALES //////////////////////////////

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////

		function selectTab(index){
			vm.tab=index;
		}

		function tabSelec(index){
			return vm.tab==(index);
		}

		function selecPanel(index){
			vm.panel=index+1;
		}

		function esPanelSelec(index){
			return vm.panel==(index+1);
		}

		function cerrarPanel(){
			vm.panel=0;
		}
	}
})();