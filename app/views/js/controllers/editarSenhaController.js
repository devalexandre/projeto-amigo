app.controller('editarSenhaController', function ($scope, $http, BASEURL, $rootScope, $window, toastr) {
    $scope.request = {
        operacao: "edit_password",
        oldPasswd: '',
        confirmPasswd: '',
        newPasswd: ''
    };

    $scope.changePassword = function() {
        if ($scope.request.newPasswd.length < 6){
            toastr.error('A senha digitada deve conter ao menos 6 caracteres!');
        }
        else if ($scope.request.newPasswd !== $scope.request.confirmPasswd){
            toastr.error('As senhas digitadas devem ser iguais!');
        }else{
            var url = BASEURL + 'usuario/alterarSenha';
            var config = {
                headers: {
                    'Content-Type': 'application/json;charset=utf-8;',
                    'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                }
            };
            $http.post(url, $scope.request, config).success(function(data){
                if(data.code === 1) {
                    toastr.success(data.message);
                } else if (data.code === 0) {
                    toastr.error(data.message);
                }
            }).error(function(data){
                toastr.error(data);
            });
        }
    };

});
