app.controller('usuarioController', function ($scope, $rootScope, BASEURL, $window, $http, $timeout, $filter, toastr) {

    $scope.showDiv = false;

    $scope.carregaUsuarios = function(){
        var strUrl = BASEURL + 'usuario/retornarTodosUsuarios';
        var config = {
            headers: {
                'Content-Type':  'application/json;charset=utf-8;',
                'Authorization': 'Bearer ' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
            }
        };
        $http.post(strUrl, config).success(function(response){
            if (response.code == 1) {
                $scope.dadosUsuarios = angular.copy(response.usuarios);
                angular.forEach($scope.dadosUsuarios, function(key, value){
                    key.nivelAcesso_usuario = key.nivelAcesso_usuario;
                    key.nivelExibicao = $scope.retornarDescricaoNivel(key.nivelAcesso_usuario);
                    key.status = key.ativo_usuario;
                    key.ativoExibicao = $scope.retornarDescricaoStatus(key.ativo_usuario);
                });

                $scope.gridOptions1.data = $scope.dadosUsuarios;
            } else {
                toastr.error('Não foi possível carregar os dados, tente novamente mais tarde!');
            }
        }).error(function(error){
            toastr.error('Não foi possível realizar a pesquisa!');
        });

    };

    $scope.gridOptions1 = {
        enableSorting: true,
        paginationPageSizes: [10, 50, 75],
        paginationPageSize: 10,
        enableVerticalScrollbar: 0,
        rowHeight:35,
        rowTemplate:'<div ng-class="{ inativo : row.entity.ativo_usuario==0 }"> <div ng-repeat="col in colContainer.renderedColumns track by col.colDef.name" class="ui-grid-cell" ui-grid-cell></div></div>',
        columnDefs: [
            { name: ' ', enableColumnMenu: false, cellTemplate:'<a role="button" data-toggle="modal" data-target="#modalInserirEditar" class="table-icon" data-tipo="Editar" ng-click="grid.appScope.modalAlterarUsuario(row.entity);"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>', width: 30 },
            { field: 'nome_usuario', displayName: 'Nome'},
            { field: 'login_usuario', displayName: 'Login' },
            { field: 'email_usuario', displayName: 'Email', width:200 },
            { field: 'nivelAcesso_usuario', displayName: 'Nível', width:110 },
            { field: 'ativo_usuario', displayName: 'Ativo', width:110 }
        ]
    };

    $scope.pesquisarUsuarios = function() {
        $scope.showDiv = true;
        if (($scope.opcaoBusca === undefined) || ($scope.opcaoBusca === '')){
            $scope.carregaUsuarios();
        }else if(($scope.opcaoBusca !== undefined || $scope.opcaoBusca !== '') && ($scope.gridOptions1.data === '' || $scope.gridOptions1.data === undefined)){


            var strUrl = BASEURL + 'usuario/retornarTodosUsuarios';
            var config = {
                headers: {
                    'Content-Type':  'application/json;charset=utf-8;',
                    'Authorization': 'Bearer ' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                }
            };
            $http.post(strUrl, config).success(function(response){
                if (response.code == 1) {
                    $scope.dadosUsuarios = angular.copy(response.usuarios);
                    angular.forEach($scope.dadosUsuarios, function(key, value){
                        key.nivelAcesso_usuario = key.nivelAcesso_usuario;
                        key.nivelExibicao = $scope.retornarDescricaoNivel(key.nivelAcesso_usuario);
                        key.status = key.ativo_usuario;
                        key.ativoExibicao = $scope.retornarDescricaoStatus(key.ativo_usuario);
                    });

                    $scope.dadosUsuariosCopy = angular.copy($scope.dadosUsuarios);
                    $scope.opcaoBuscaCopy = angular.copy($scope.opcaoBusca);
                    $scope.dadosUsuariosCopy = $filter('filter')($scope.dadosUsuariosCopy, $scope.opcaoBuscaCopy);
                    $scope.gridOptions1.data = $scope.dadosUsuariosCopy;

                } else {
                    toastr.error('Não foi possível carregar os dados, tente novamente mais tarde!');
                }
            }).error(function(error){
                toastr.error('Não foi possível realizar a pesquisa!');
            });


        }else{
            $scope.dadosUsuariosCopy = angular.copy($scope.dadosUsuarios);
            $scope.opcaoBuscaCopy = angular.copy($scope.opcaoBusca);
            $scope.dadosUsuariosCopy = $filter('filter')($scope.dadosUsuariosCopy, $scope.opcaoBuscaCopy);
            $scope.gridOptions1.data = $scope.dadosUsuariosCopy;
        }
    };

    $scope.cadastrarUsuario = function(){
        if ($scope.cadastro.strSenha.length < 6){
            toastr.error('Erro ao cadastrar o usuário: A senha digitada deve conter ao menos 6 caracteres!');
        }else if ($scope.cadastro.strSenha !== $scope.cadastro.strSenhaConfirma){
            toastr.error('Erro ao cadastrar o usuário: As senhas digitadas devem ser iguais!');
        }else if ($scope.cadastro.strNome.length > 50){
            toastr.error('O numero maximo de caracteres para o nome é de 50.');
        }else if ($scope.cadastro.strEmail.length > 50){
            toastr.error('O numero maximo de caracteres para o nome é de 50.');
        }else if ($scope.cadastro.strLogin.length > 30){
            toastr.error('O numero maximo de caracteres para o login é de 30.');
        }else if ($scope.cadastro.strLogin.indexOf(" ") >= 1){
            toastr.error('O login não pode conter espaços.');
        }else{
            var strUrl = BASEURL + 'usuario/registrarUsuario';
            var config = {
                headers: {
                    'Content-Type':  'application/json;charset=utf-8;',
                    'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                }
            };
            var data = $scope.cadastro;
            $http.post(strUrl, data, config).success(function(response){
              if (response.code == 1) {
                toastr.success('Usuário cadastrado com sucesso!');
                $scope.carregaUsuarios();
                $('#modalInserirEditar').modal('toggle');
              } else{
                toastr.error(response.message);
              }
            }).error(function(error){
              toastr.error('Erro ao cadastrar o usuário!');
            });
        }
    };

    $scope.modalAlterarUsuario = function(intLinha){
        $scope.cadastro =  {
            'intUsuario': intLinha.id_usuario,
            'blnAtivo': intLinha.ativo_usuario,
            'strSenha': '',
            'strSenhaConfirma': '',
            'strLogin': intLinha.login_usuario,
            'strNome': intLinha.nome_usuario,
            'strEmail': intLinha.email_usuario,
            'intNivel': intLinha.nivelAcesso_usuario,
            'intOpcao': '1'
        };
    };

    $scope.opcaoUsuario = function(intOpcao){
        if (intOpcao === "1"){
            $scope.alterarUsuario();
        }else{
            $scope.cadastrarUsuario();
        }
    };

    $scope.opcaoUsuarioCadastro = function(intOpcao){
        $scope.cadastro =  {
            'blnAtivo': '1',
            'strSenha': '',
            'strSenhaConfirma': '',
            'intNivel': '',
            'strLogin': '',
            'strNome': '',
            'strEmail': '',
            'intOpcao': '0'
        };
    };

    $scope.alterarUsuario = function(){
        if ($scope.cadastro.strSenha.length < 6){
            toastr.error('Erro ao alterar o usuário: A senha digitada deve conter ao menos 6 caracteres!');
        }
        else if ($scope.cadastro.strSenha !== $scope.cadastro.strSenhaConfirma){
            toastr.error('Erro ao alterar o usuário: As senhas digitadas devem ser iguais!');
        }else{
            var strUrl = BASEURL + 'usuario/atualizarUsuario';
            var config = {
                headers: {
                    'Content-Type':  'application/json;charset=utf-8;',
                    'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                }
            };
            var data = $scope.cadastro;
            console.log(data);
            $http.post(strUrl, data, config).success(function(response){
                console.log(response);
                if (response.code == 1) {
                    toastr.success(response.message);
                    $scope.carregaUsuarios();
                    $('#modalInserirEditar').modal('toggle');
                } else {
                    toastr.error(response.message);
                }
            }).error(function(error){
                toastr.error('Não foi possível alterar o usuário!');
            });
        }
    };

    $scope.retornarDescricaoNivel = function(intNivel){
        intNivel = parseInt(intNivel);
        switch (intNivel) {
            case 1:
                return 'Básico';
            case 2:
                return 'Intermediário';
            case 3:
                return 'Administrador';
            default:
                return 'undefined';
        }
    };

    $scope.retornarDescricaoStatus = function(intStatus){
        intStatus = parseInt(intStatus);
        if(intStatus === 0){
            return "Inativo";
        }else if(intStatus === 1){
            return "Ativo";
        }else{
            return 'undefined';
        }
    };

});
