(function(){
	'use strict';
	angular.module('escuela')
	.factory('AuthenticationFactory',factory);

	function factory($http, $localStorage,OAUTHINFO){
		var fc={
			Login: Login,
			Logout: Logout,
			RefreshToken: RefreshToken
		};

		return fc;

		/////////////////
		function Login(user,callback){
			pLogin(user).then(function(res){
				if(res.data.access_token){

					// store username and token in local storage to keep user logged in between page refreshes
                    $localStorage.currentUser = {};
                    $localStorage.currentUser = res.data;

                    // add jwt token to auth header for all requests made by the $http service
                    $http.defaults.headers.common['Authorization'] = 'Bearer ' + res.data.access_token;

                    // execute callback with true to indicate successful login
                    if (typeof(callback=='function')) {
                        callback(true);
                    }
                
                } else {
                
                    // execute callback with false to indicate failed login
                    if (typeof(callback=='function')) {
                        callback(false);
                    }
                }
			},function(res){
                callback(false);
            });
		}
		function pLogin(user){
			var data={
				username: user.username,
				password: user.password,
				grant_type: OAUTHINFO.GRANTTYPEREQUEST,
				client_id: OAUTHINFO.CLIENTID,
				client_secret: OAUTHINFO.CLIENTSECRET
			};
			return $http.post(OAUTHINFO.URLVERIFY,data);
		}

		function Logout() {
            // remove user from local storage and clear http auth header
            delete $localStorage.currentUser;
            $http.defaults.headers.common.Authorization = '';
        }

        function RefreshToken(callback){
        	if($localStorage.currentUser.refresh_token){
        		console.log('Refrescando token.');
        		rToken($localStorage.currentUser.refresh_token).then(function(res){
        			if(res.data.access_token){

						// store username and token in local storage to keep user logged in between page refreshes
                    	$localStorage.currentUser = {};
                    	$localStorage.currentUser = res.data;

                    	// add jwt token to auth header for all requests made by the $http service
                    	$http.defaults.headers.common['Authorization'] = 'Bearer ' + res.data.access_token;

                    	// execute callback with true to indicate successful login
                        if (typeof(callback=='function')) {
                            callback(true);
                        }
                
                	} else {
                
                    	Logout();
                    	
                    	// execute callback with false to indicate failed login
                        if (typeof(callback=='function')) {
                    	   callback(false);
                        }
                	}
        		});
        	}else{
        		Logout();
                if (typeof(callback==='function')) {
        		  callback(false);
                }
        	}
        }
        function rToken(refTok){
			var data={
				refresh_token: refTok,
				grant_type: OAUTHINFO.GRANTTYPEREFRESH,
				client_id: OAUTHINFO.CLIENTID,
				client_secret: OAUTHINFO.CLIENTSECRET
			};
			return $http.post(OAUTHINFO.URLREFRESH,data);
        }
	}
})();