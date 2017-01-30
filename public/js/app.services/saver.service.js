(function(){
	'use strict';
	angular.module('escuela')
		.service('Saver',service);

	function service($localStorage){
		var vm=this;

		// Variables
		if(!$localStorage.cache){
			$localStorage.cache=[];
		}

		// Funciones
		vm.getData=getData;
		vm.setData=setData;
		vm.delData=delData;
		vm.existeData=existeData;

		function getData(name){
			var index=existeData(name);
			if (index!=null) {
				return $localStorage.cache[index].info;
			}
			return null;
		}
		function setData(name,info){
			if (typeof (name)=='string') {
				var index=existeData(name);
				if (index!=null) {
					$localStorage.cache[index].info=info;
				}else{
					$localStorage.cache.push(
						{
							name: name,
							info: info
						}
					);
				}
				return true;
			}
			return false;
		}
		function delData(name){
			var index=existeData(name);
			if (index!=null) {
				$localStorage.cache.splice(index);
				return true;
			}
			return false;
		}
		function existeData(nombre){
			if ($localStorage.cache) {
			for (var i = 0; i < $localStorage.cache.length; i++) {
				if($localStorage.cache[i].name==nombre){
					return i;
				}
			}
			}
			return null;
		}
	}
})();