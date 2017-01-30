(function(){
	'use strict';
	angular
		.module('escuela')
		.controller('LiqCajaCtrl',controller);

	function controller(animPage,PensionFactory,OtrosFactory,MatriculasFactory, Saver, $window, $timeout){
		var vm=this;

		// Variables básicas
		vm.fechaAhora=0;
		vm.pen={
			sum:0,
			data:[]
		};
		vm.mat={
			sum:0,
			data:[]
		};
		vm.otr={
			sum:0,
			data:[]
		};
	
		// Funciones basicas
		vm.verificarFechas=verificarFechas;
		vm.imprimetirilla=imprimetirilla;

		// Lanzamiento Automático
		animPage.show('liqcaja',function(){});
		obtenerFechaAhora();

		/////////////////////////// FUNCIONES BASICAS //////////////////////////////
		function sumador(arreglo){
			var resultado=0;
			for (var i = arreglo.length - 1; i >= 0; i--) {
				resultado+=parseInt(arreglo[i].valor);
			};
			return resultado;
		}
		function obtenerFechaAhora(){
			var objFecha=new Date();
			vm.fechaAhora=new Date(objFecha.getTime()-objFecha.getTimezoneOffset()*60000);
			vm.verificarFechas();
		}
		function verificarFechas(){
			buscarInfoFecha(PensionFactory,vm.pen);
			buscarInfoFecha(MatriculasFactory,vm.mat);
			buscarInfoFecha(OtrosFactory,vm.otr);
		}
		function buscarInfoFecha(fc,obj){
			fc.gFDts(vm.fechaAhora).then(function(res){
				obj.sum=sumador(res.data);
				obj.data=res.data;
			},function(res){
				obj.sum=0;
				console.log('No se resuelve la facturación por fecha.');
			});
		}
		function imprimetirilla(){
			var obj={
				fechaAhora: vm.fechaAhora,
				pen: vm.pen,
				mat: vm.mat,
				otr: vm.otr
			};
			Saver.setData('liqcaja',obj);
			$timeout(function(){
				$window.open('/#/tirilladia');
			},500)
		}
	}
})();