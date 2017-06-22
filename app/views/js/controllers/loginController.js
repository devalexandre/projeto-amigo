app.controller('loginController', function($scope, $rootScope, toastr, BASEURL, $window, $location, $http, AuthService) {

    $scope.usuario = {
        'strLogin': '',
        'strSenha': ''
    };

    if (AuthService.isAuthed()) {
        $location.path('/');
    }

    $scope.verificarLogin = function() {
        var strUrl = BASEURL + 'usuario/login';
        var config = {
            headers: {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };
        $http.post(strUrl, $scope.usuario, config).success(function(response) {
            console.log(response);
            if ($scope.verificaToken(response.token)) {
                AuthService.saveToken(response.token);
                $scope.salvaUsuario(response.strNome, response.intNivel, response.intIdUsuario, response.convites);
                $location.path('/');
            } else if (response.code === 0) {
                toastr.error(response.message);
            }
        }).error(function(error) {
            toastr.error('Ocorreu um erro ao fazer o login, tente novamente!');
        });
    };

    $scope.cadastraUsuario = function() {
        if ($scope.usuario.strSenha.length < 6){
          toastr.error('Erro ao cadastrar o usuário: A senha digitada deve conter ao menos 6 caracteres!');
          }else if ($scope.usuario.strSenha !== $scope.usuario.strSenhaConfirma){
          toastr.error('Erro ao cadastrar o usuário: As senhas digitadas devem ser iguais!');
          }else if ($scope.usuario.strNome.length > 50){
          toastr.error('O numero maximo de caracteres para o nome é de 50.');
          }else if ($scope.usuario.strEmail.length > 50){
          toastr.error('O numero maximo de caracteres para o nome é de 50.');
          }else if ($scope.usuario.strLogin.length > 30){
          toastr.error('O numero maximo de caracteres para o login é de 30.');
          }else if ($scope.usuario.strLogin.indexOf(" ") >= 1){
          toastr.error('O login não pode conter espaços.');
          }else{
          var strUrl = BASEURL + 'usuario/registrarUsuario';
          var config = {
              headers: {
                  'Content-Type':  'application/json;charset=utf-8;'
              }
          };
          var data = $scope.usuario;
          data.intNivel = '0';
          $http.post(strUrl, data, config).success(function(response){
            if (response.code == 1) {
                toastr.success('Usuário cadastrado com sucesso!');
                $window.location.reload();
            } else{
                toastr.error(response.message);
            }
          }).error(function(error){
            toastr.error('Erro ao cadastrar o usuário!');
          });
          }
    };

    // $scope.recuperarSenha = function() {
    //     var strUrl = BASEURL + 'usuario/recuperarSenha';
    //     var config = {
    //         headers: {
    //             'Content-Type': 'application/json;charset=utf-8;'
    //         }
    //     };
    //     $http.post(strUrl, $scope.Email, config).success(function(response) {
    //         console.log(response);
    //
    //     }).error(function(error) {
    //         toastr.error('Ocorreu um erro durante o processo de recuperação, tente novamente!');
    //     });
    // };

    $scope.verificaToken = function(res) {
        //var token = res ? res : false;
        if (typeof res === "string") {
            if (JSON.parse(localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo !== null) {
                return true;
            } else {
                AuthService.logout();
                return false;
            }
        } else {
            return false;
        }
    };

    $scope.exibeLogin = '1';
    $scope.opcaoMenu = function(opt) {
        console.log(opt);
        if(opt === '1'){
            $scope.exibeLogin = '1';
        }else if(opt === '2'){
            $scope.exibeLogin = '2';
        }else if(opt === '3'){
            $scope.exibeLogin = '3';
        }

    };

    $scope.salvaUsuario = function(strNome, intNivel, idUser, strConvites) {
        var projetoAmigo = [];
        projetoAmigo = {
            'strNomeUsuarioprojetoAmigo': strNome,
            'intNivelprojetoAmigo': intNivel,
            'intIdUsuarioprojetoAmigo': idUser,
            'strConvitesprojetoAmigo': strConvites,
            'jwtTokenprojetoAmigo': JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
        };
        $window.localStorage.setItem('projetoAmigo', JSON.stringify(projetoAmigo));
    };

});
