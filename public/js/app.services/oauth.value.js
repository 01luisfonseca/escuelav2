(function(){
	'use strict';
	angular.module('escuela')
	.constant('OAUTHINFO',{
		URLVERIFY:'oauth/token',
		URLREFRESH:'oauth/token',
		CLIENTID:3,
		CLIENTSECRET:'PdH2ikQTQPaXF8ilFs6CsZbmYyUBaReZfNXujVqT',//'I8dVQ8umBnjfkrutVB6suAeHMbjr2nVUGRmNjGOn',
		GRANTTYPEREQUEST:'password',
		GRANTTYPEREFRESH:'refresh_token'
	});
})();