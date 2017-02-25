/*

Se pone la etiqueta 
*********************************
<body ng-app="...">
    <lf-charge></lf-charge>
    ...
</body>
*********************************

La imagen esta en Base64, por lo que no se necesita nada mas
Requiere insertar la siguiente clase a la hoja de estilos:

*********************************
.cargaInfo {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    background: rgba(188, 192, 229, 0.5) url(data:image/gif;base64,R0lGODlhLAEsAee+AAAAAOdSOedYP+daQ+hiTOlqVOlwXOp4ZeuAbuyHduyMfO2Sg/+WHO2Xif6aJQSc2/6cK++ekAuf3P6fMBCg3P6hNBij3e+kl/2kPSCm3v2mQimp3/2pR/Crn/yrTPyuVPGwpfyyXPKzqfy1ZFG45PK4rvu5bPK7sly95fu9dUG+zvzAe2jB5vTCuVTE0vvFhl3G1HjI6WXJ1WvL1/vLk4LMM4LM6v/MJ/vNmHTO2YfOO4nOPvbOx4vPQ4vP633Q247Q64/QSPvQnv7QPZDRTP3RQYTS3JXSU/vSov3SSZXT7fbTzYvV3pvVXpzW7p/WZPzWWpDX4PzXq6TYbPzYrpfZ4aTZ76fZcfzZaJra4qradvja1fvbc6zc8K7cffzct6Pd5fvefqjf5rXfiKzg5/zghLnh8/vhjvzhwr7il/ri3rXj6c3jW/vjlsLknvvkmsHl88Xlovrl4f3lyrzm68jmpvzmL8nnqtXnfPvnpMfo9czordjoh/roTMTp7s7psfrpWvrp5vzpqv3p0s3q9tzqk/rqZfzqs8zr79LruN7rm/rrdPrsfeLtp/rthPztvP3t29Tu9tnuw/rujeXvs/rvlfzvxPzv7Nrw9d/wzPDw8PrwnOHxz+jxvPrxpfrxqP3xy/3x4+vyw+Xz1vvztP3z1OL09uP0+u30zP308u/10vD11fv1v/716uj2+er23fv2x/322+334/P33e34+/z41PP56vX54/352/354vX68PX7/P377P388////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggR0lNUCBvbiBhIE1hYwAh+QQFCgD/ACwAAAAALAEsAQAI/gD/CRxIsKDBgwgTKlzIsKHDhxAjSpxIsaLFixgzatzIsaPHjyBDihxJsqTJkyhTqlzJ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) center no-repeat;
    z-index: 100;
}
*********************************

En caso de tener angular animation, se agrega las siguientes clases para ajustar la visualización.

*********************************
.cargaInfo.ng-enter {
  transition: 0.4s linear all;
  opacity: 0;
}

.cargaInfo-init .fade-element-in.ng-enter {
  opacity: 1;
}

.cargaInfo.ng-enter.ng-enter-active {
  opacity: 1;
}

.cargaInfo.ng-leave {
  transition: 0.4s linear all;
  opacity: 1;
}
.cargaInfo.ng-leave.ng-leave-active {
  opacity: 0;
}
*********************************

Se activa la carga al emitir un evento 'cargando' con $rootScope.$broadcast('cargando') o $rootScope.$broadcast('cargando',true).
En caso de usarlo sin true o false, se intercambia el estado de la carga.

El módulo escucha los eventos 'cargando'.

*/
(function(){
	'use strict';
	angular
		.module('lfmod',[
		])
		.directive('lfCharge',()=>{
			var directive = {
        		link: link,
        		template: '<div ng-if="vm.cargando" class="cargaInfo"></div>',
        		restrict: 'EA',
        		scope:{
        		},
        		controller: controller,
        		controllerAs: 'vm',
        		bindToController: true // because the scope is isolated
    		};
    		return directive;

    		function link(scope, element, attrs) {
    		}
    		function controller($scope){
    			var vm=this;
    			vm.cargando=false;
    			$scope.$on('cargando',(ev, attr)=>{
                    if(typeof(attr) == 'undefined'){
                        vm.cargando = !vm.cargando;
                    }else{
                        vm.cargando = attr;
                    }
      			});
    		}
		});
})();