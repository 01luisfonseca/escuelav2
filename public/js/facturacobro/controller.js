(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('facturacobroCtrl',controller);

	function controller(
			PensionFactory, 
    		MatriculasFactory, 
    		OtrosFactory,
    		$stateParams)
	{
		var vm=this;

		// Variables básicas
		vm.data={};
		vm.tipo=$stateParams.tipo;
		vm.factura=$stateParams.factura;

		// Variables adicionales
	
		// Funciones basicas
		vm.getData=getData;
		vm.valor=valor;
		vm.mes=mes;

		// Funciones adicionales

		// Lanzamiento Automático
		vm.getData();
		console.log(vm);

		//////////////////////// FUNCIONES ADICIONALES //////////////////////////////
		function obtenerFactory(){
			switch(vm.tipo){
				case 'pension':
					return PensionFactory;
					break;
				case 'matricula':
					return MatriculasFactory;
					break;
				case 'otros':
					return OtrosFactory;
					break;
			}
		}

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function valor(tipo){
			if(vm.tipo==tipo){
				return vm.data.valor;
			}
			return 0;
		}
		function getData(){
			var fac=obtenerFactory();
			fac.gEFac(vm.factura).then(function(res){
				vm.data=res.data;
			});
		}
		function mes(){
			try{
			if (vm.tipo=='pension') {
				return vm.data.mes_id; // la linea vm.data.meses.nombre no funciona. La API no devuelve meses[]
			}
			return '';
		}catch(e){
			return '';
		}
		}
	}
})();