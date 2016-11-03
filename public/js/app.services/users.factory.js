(function(){
	'use strict';
	angular.module('escuela')
	.factory('UsersFactory',factory);

	function factory($http){
		var fc={
            url: '/api/users',
			getUser: getUser,
			getUsers: getUsers,
            getSearchUsers:getSearchUsers,
            getRangeUsers: getRangeUsers,
            addUser: addUser,
			modUser: modUser,
            delUser: delUser
		};

		return fc;

		/////////////////
        function getUser(id){
            return $http.get(fc.url+'/'+id);
        }
        function getUsers(){
            return $http.get(fc.url);
        }
        function getSearchUsers(texto){
            return $http.get(fc.url+'/search/'+texto);
        }
        function getRangeUsers(first){
            return $http.get(fc.url+'/range/'+first);
        }
        function addUser(data){
            return $http.post(fc.url,data);
        }
        function modUser(id,data){
            return $http.put(fc.url+'/'+id,data);
        }
        function delUser(id){
            return $http.delete(fc.url+'/'+id);
        }
	}
})();