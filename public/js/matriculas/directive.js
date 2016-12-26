(function(){
	'use strict';
	angular
		.module('escuela')
		.directive('matriculas',directive);

	function directive(){
		var directive = {
        	link: link,
        	templateUrl: '/js/matriculas/index.html',
        	restrict: 'EA',
        	scope:{
        		reg:'='
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

    	function controller(MatriculasFactory,error,$timeout,$window){
    		var vm=this;

			// Variables básicas
			var basicFactory=MatriculasFactory;
			vm.id=0;
			vm.dts={}; // Datos del periodo
			vm.form={}; // Para formulario
			vm.sel={
				panel:0,
				menu:0
			};
			vm.buscado='';
			vm.buscando=false;
			vm.yaBuscado=false;

			/* Variables adicionales */
			// var promise;
	
			// Funciones basicas
			vm.getData=getData;
			vm.getDatas=getDatas;
			vm.newData=newData;
			vm.actData=actData;
			vm.delData=delData;
			vm.buscarData=buscarData;
			vm.hayExistente=hayExistente;
			vm.accion=accion;
			vm.panelSel=panelSel;
			vm.selPanel=selPanel;
			vm.selElem=selElem;
			vm.toInicio=toInicio;

			// Funciones adicionales

			// Lanzamiento Automático
			if(typeof(vm.reg)!='undefined'){
				vm.reg=vm.id;
			}
			startData();

			/////////////////////////// FUNCIONES ADICIONALES///////////////////////////

			/////////////////////////// FUNCIONES BASICAS //////////////////////////////

			function startData(){
				stopData();
				if (vm.hayExistente()) {
					vm.getData(vm.id);
				}else{
					vm.getDatas();
				}
				//promise=$interval(vm.getDatas,5000);
			}

			function stopData(){
				//$interval.cancel(promise);
			}

			function getData(id){
				return basicFactory.gDt(id).then(function(res){
					//console.log(res);
					vm.form=res.data;
					vm.form.cancelado_at=new Date(vm.form.cancelado_at);
					vm.form.valor=parseFloat(vm.form.valor);
					vm.form.faltante=parseFloat(vm.form.faltante);
				});
			}

			function getDatas(){
				return basicFactory.gDts().then(function(res){
					vm.dts=res.data;
				});
			}
		
			function actData(id){
				return basicFactory.mDt(id,vm.form).then(function(res){
					startData();
					error.setAlerta('Se ha actualizado el registro.');
					if (vm.sel.menu) {
						vm.sel.menu=0;
						toInicio();
					}
				},function(res){
					error.setError('Se ha presentado un error. No se actualiza el registro. ');
				});
			}

			function newData(){
				return basicFactory.aDt(vm.form).then(function(res){
					error.setAlerta('Se ha creado el registro.');
					vm.form={};
				},function(res){
					error.setError('Se ha presentado un error. No se crea el registro.');
				});
			}

			function accion(){
				if (typeof(vm.id)==0) {
					vm.newData();
				}
				if (vm.id>0) {
					vm.actData(vm.id);
				}
				console.log('No se realiza acción porque no hay registro. reg='+vm.id);
			}

			function hayExistente(){
				if (vm.id!=0) {
					return true;
				}
				return false;
			}

			function buscarData(){
				stopData();
				if(vm.buscado.length>2){
					vm.buscando=true;
					if (!vm.yaBuscado) {
						vm.yaBuscado=true;
						$timeout(searchData,1000);
					}
				}
				if(vm.buscado==''){
					return vm.getDatas();
				}
				return false;
			}

			function searchData(){
				vm.yaBuscado=false;
				return basicFactory.gSDt(vm.buscado).then(function(res){
					vm.dts=res.data;
					vm.buscando=false;
				});
			}

			function delData(id){
				if (!$window.confirm('¿ Seguro que desea eliminar el elemento ?')) {
					return false;
				}
				return basicFactory.dDt(id).then(function(res){
					error.setAlerta(res.data.msj);
					if (vm.sel.menu) {
						vm.sel.menu=0;
						toInicio();
					}
					startData();
				});
			}
			function panelSel(id){
				return vm.sel.panel==id;
			}
			function selPanel(id){
				vm.sel.panel=id;
			}
			function selElem(id){
				vm.sel.panel=1;
				vm.sel.menu=1;
				vm.id=id;
				vm.getData(id);
			}
			function toInicio(){
				vm.sel.panel=0;
				vm.getDatas();
			}
    	}
	}
})();