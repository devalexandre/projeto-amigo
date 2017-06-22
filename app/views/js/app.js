var app = angular.module('app', ['ngRoute', 'ngMask', 'toastr', 'blockUI', 'ui.utils.masks', 'ui.grid', 'ui.grid.pagination', 'ui.grid.autoResize', 'ui.grid.grouping', 'ui.grid.selection', 'perfect_scrollbar', 'ui.grid.expandable', 'ui.grid.pinning', 'ui.grid.exporter']);

app.run(function($rootScope, $location, AuthService, toastr, $window) {
    $rootScope.$on('$routeChangeStart', function(event, next, current) {
        toastr.clear();

        if ($window.localStorage.getItem('projetoAmigo') !== null){
            $rootScope.strNomeUsuarioprojetoAmigo = JSON.parse($window.localStorage.getItem('projetoAmigo')).strNomeUsuarioprojetoAmigo;
            $rootScope.intNivelprojetoAmigo = JSON.parse($window.localStorage.getItem('projetoAmigo')).intNivelprojetoAmigo;
            $rootScope.intIdUsuarioprojetoAmigo = JSON.parse($window.localStorage.getItem('projetoAmigo')).intIdUsuarioprojetoAmigo;
            $rootScope.strConvitesprojetoAmigo = JSON.parse($window.localStorage.getItem('projetoAmigo')).strConvitesprojetoAmigo;
        }else{
            $location.path('/login');
        }

        if ($location.path() === '/usuarios') {
            if ($rootScope.intNivelprojetoAmigo !== '3') {
                $location.path('/');
            }
        }

        if (!AuthService.isAuthed()) {
            $location.path('/login');
        }
    });
});

// app.constant('BASEURL', 'http://localhost/projeto-amigo/api/');
app.constant('BASEURL', 'http://192.168.113.59/angular4/projeto-amigo/api/');

app.config(function($httpProvider) {
    $httpProvider.interceptors.push('AuthInterceptor');
});

app.config(function($routeProvider) {
    $routeProvider

        .when('/', {
            templateUrl: 'pages/dashboard.html',
            controller: 'dashboardController'
        })

        .when('/usuarios', {
            templateUrl: 'pages/cadastrar-usuario.html',
            controller: 'usuarioController'
        })

        .when('/grupos', {
            templateUrl: 'pages/grupos.html',
            controller: 'gruposController'
        })

        .when('/editar-senha', {
            templateUrl: 'pages/editar-senha.html',
            controller: 'editarSenhaController'
        })

        .when('/login', {
            templateUrl: 'pages/login.html',
            controller: 'loginController'
        })

        .otherwise({
            redirectTo: '/login'
        });

});

app.factory('AuthInterceptor', function(BASEURL, AuthService, $q) {
    return {
        request: function(config) {
            var token = AuthService.getToken();
            if (config.url.indexOf(BASEURL) === 0 && token) {
                config.headers.Authorization = 'Bearer ' + token;
            }
            return config;
        },
        response: function(res) {
            if (res.config.url.indexOf(BASEURL) === 0 && res.data.token) {
                AuthService.saveToken(res.data.token);
            }
            return res;
        },
        responseError: function(res) {
            // Caso ele receba o cabeçalho de 'Não Autorizado', o sistema faz o logout.
            if (res.status === 401) {
                AuthService.logout();
            }
            return $q.reject(res);
        }
    };
});

app.service('AuthService', function($window, $location, $rootScope) {
    // Metodos do JWT
    this.parseJwt = function(token) {
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse($window.atob(base64));
    };
    this.saveToken = function(token) {
        var projetoAmigo = [];
        projetoAmigo= {
            'jwtTokenprojetoAmigo': token
        };
        $window.localStorage.setItem('projetoAmigo', JSON.stringify(projetoAmigo));
    };
    this.getToken = function() {
        if ($window.localStorage.getItem('projetoAmigo') !== null){
            return JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo;
        }
    };
    this.isAuthed = function() {
        var token = this.getToken();
        if (token) {
            var params = this.parseJwt(token);
            return params;
        } else {
            return false;
        }
    };
    this.logout = function() {
        $window.localStorage.removeItem('projetoAmigo');
        delete $rootScope.strNomeUsuarioprojetoAmigo;
        delete $rootScope.intNivelprojetoAmigo;
        delete $rootScope.intIdUsuarioprojetoAmigo;
        $location.path('/login');
    };
});

app.config(function(toastrConfig) {
    angular.extend(toastrConfig, {
        "positionClass": "toast-center-center",
        "closeButton": true,
        "maxOpened": 1,
        "extendedTimeOut": 5000
    });
});

app.config(function(blockUIConfig) {

    blockUIConfig.message = '';
    blockUIConfig.template = '<div class="block-ui-overlay"></div><div class="block-ui-message-container" aria-live="assertive" aria-atomic="true"><div class="block-ui-message"><div class="loading text-center"><h6>Aguarde</h6><span></span><span></span><span></span></div></div></div>';

});
