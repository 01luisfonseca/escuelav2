(function(){
    'use strict';
    angular.module('escuela')
    .factory('PensionFactory',factory);

    function factory($http){
        var url= '/api/pension';
        var fc={
            gDt: gDt,
            gDts: gDts,
            gSDt:gSDt,
            gRDts: gRDts,
            aDt: aDt,
            mDt: mDt,
            dDt: dDt,
            gEFac:gEFac,
            gAl:gAl,
            gFDts:gFDts
        };

        return fc;

        /////////////////
        function gDt(id){
            return $http.get(url+'/'+id);
        }
        function gDts(){
            return $http.get(url);
        }
        function gSDt(texto){
            return $http.get(url+'/search/'+texto);
        }
        function gRDts(first){
            return $http.get(url+'/range/'+first);
        }
        function aDt(data){
            return $http.post(url,data);
        }
        function mDt(id,data){
            return $http.put(url+'/'+id,data);
        }
        function dDt(id){
            return $http.delete(url+'/'+id);
        }
        function gEFac(fac){
            return $http.get(url+'/fac/'+fac);
        }
        function gAl(id){
            return $http.get(url+'/alumno/'+id);
        }
        function gFDts(fecha){
            var fec={};
            fec.fecha=fecha;
            return $http.post(url+'/valor/porfecha',fec);
        }      
    }
})();