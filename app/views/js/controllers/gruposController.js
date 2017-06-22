app.controller('gruposController', function ($scope, $rootScope, BASEURL, $window, $http, $timeout, $filter, toastr) {

    var dataAtual = moment();
    var mesAtual = dataAtual.month();
    var anoAtual = dataAtual.year();
    var diaAtual = dataAtual.date();
    diaAtual = (String(diaAtual).length > 1) ? diaAtual : '0'+diaAtual;
    mesAtual = (String(mesAtual+1).length > 1) ? (mesAtual+1) : '0'+ (mesAtual+1);

    $scope.showDiv = false;

    $scope.carregaGrupos = function(){
        var strUrl = BASEURL + 'grupo/retornarTodosGrupos';
        var config = {
            headers: {
                'Content-Type':  'application/json;charset=utf-8;',
                'Authorization': 'Bearer ' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
            }
        };
        $http.post(strUrl, config).success(function(response){
            if (response.code == 1) {
                $scope.dadosGrupos = angular.copy(response.usuarios);
                angular.forEach($scope.dadosGrupos, function(key, value){
                    key.nivelAcesso_grupo = key.nivelAcesso_grupo;
                    key.nivelExibicao = $scope.retornarDescricaoNivel(key.nivelAcesso_grupo);
                    key.status = key.ativo_grupo;
                    key.ativoExibicao = $scope.retornarDescricaoStatus(key.ativo_grupo);
                });

                $scope.gridOptions1.data = $scope.dadosGrupos;
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
        rowTemplate:'<div ng-class="{ inativo : row.entity.ativo_grupo==0 }"> <div ng-repeat="col in colContainer.renderedColumns track by col.colDef.name" class="ui-grid-cell" ui-grid-cell></div></div>',
        columnDefs: [
            { name: ' ', enableColumnMenu: false, cellTemplate:'<a role="button" data-toggle="modal" data-target="#modalInserirEditar" class="table-icon" data-tipo="Editar" ng-click="grid.appScope.modalAlterarGrupo(row.entity);"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>', width: 30 },
            { field: 'nome_grupo', displayName: 'Nome'},
            { field: 'tipo_grupo', displayName: 'Tipo' },
            { field: 'dataSorteio_grupo', displayName: 'Data', width:200 },
            { field: 'faixaPreco_grupo', displayName: 'Preço Max',cellFilter: 'currency', width:110 },
            { field: 'ativo_grupo', displayName: 'Ativo', width:110 },
            { name: ' ', enableColumnMenu: false, cellTemplate:'<a ng-if="row.entity.admin_grupo == intIdUsuario" role="button" class="table-icon" data-tipo="Editar" ng-click="grid.appScope.entrarGrupo(row.entity.id_grupo);">Entrar</a>', width: 50 }
        ]
    };

    $scope.pesquisarGrupos = function() {
        $scope.showDiv = true;
        if (($scope.opcaoBusca === undefined) || ($scope.opcaoBusca === '')){
            $scope.carregaGrupos();
        }else if(($scope.opcaoBusca !== undefined || $scope.opcaoBusca !== '') && ($scope.gridOptions1.data === '' || $scope.gridOptions1.data === undefined)){


            var strUrl = BASEURL + 'grupo/retornarTodosGrupos';
            var config = {
                headers: {
                    'Content-Type':  'application/json;charset=utf-8;',
                    'Authorization': 'Bearer ' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                }
            };
            $http.post(strUrl, config).success(function(response){
                if (response.code == 1) {
                    $scope.dadosGrupos = angular.copy(response.usuarios);
                    angular.forEach($scope.dadosGrupos, function(key, value){
                        key.nivelAcesso_grupo = key.nivelAcesso_grupo;
                        key.nivelExibicao = $scope.retornarDescricaoNivel(key.nivelAcesso_grupo);
                        key.status = key.ativo_grupo;
                        key.ativoExibicao = $scope.retornarDescricaoStatus(key.ativo_grupo);
                    });

                    $scope.dadosGruposCopy = angular.copy($scope.dadosGrupos);
                    $scope.opcaoBuscaCopy = angular.copy($scope.opcaoBusca);
                    $scope.dadosGruposCopy = $filter('filter')($scope.dadosGruposCopy, $scope.opcaoBuscaCopy);
                    $scope.gridOptions1.data = $scope.dadosGruposCopy;

                } else {
                    toastr.error('Não foi possível carregar os dados, tente novamente mais tarde!');
                }
            }).error(function(error){
                toastr.error('Não foi possível realizar a pesquisa!');
            });


        }else{
            $scope.dadosGruposCopy = angular.copy($scope.dadosGrupos);
            $scope.opcaoBuscaCopy = angular.copy($scope.opcaoBusca);
            $scope.dadosGruposCopy = $filter('filter')($scope.dadosGruposCopy, $scope.opcaoBuscaCopy);
            $scope.gridOptions1.data = $scope.dadosGruposCopy;
        }
    };

    $scope.cadastrarGrupo = function(){
        console.log($scope.cadastro);
        if ($scope.cadastro.strNome.length > 50){
            toastr.error('O numero maximo de caracteres para o nome é de 50.');
        }else if ($scope.cadastro.strDescricao.length > 100){
            toastr.error('O numero maximo de caracteres para o nome é de 50.');
        }else if ($scope.cadastro.intTipo  === ''){
            toastr.error('Para continuar é necessario selecionar um tipo.');
        }else if ($scope.cadastro.dateSorteio  === ''){
            toastr.error('Para continuar é necessario selecionar uma data.');
        }else{
            // $scope.cadastro.dateSorteio = parseDate($scope.cadastro.dateSorteio, 1);
            var strUrl = BASEURL + 'grupo/grupos';
            var config = {
                headers: {
                    'Content-Type':  'application/json;charset=utf-8;',
                    'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                }
            };
            var data = $scope.cadastro;
            data.dateSorteio = parseDate(data.dateSorteio , 1);
            console.log(data);
            $http.post(strUrl, data, config).success(function(response){
                console.log(response);
              if (response.code == 1) {
                toastr.success('Grupo criado com sucesso!');
                $scope.carregaGrupos();
                $('#modalInserirEditar').modal('toggle');
              } else{
                toastr.error(response.message);
              }
            }).error(function(error){
              toastr.error('Erro ao criar o grupo!');
            });
        }
    };

    $scope.modalAlterarGrupo = function(intLinha){
        $scope.cadastro =  {
            'intGrupo': intLinha.id_grupo,
            'blnAtivo': intLinha.ativo_grupo,
            'strNome': intLinha.nome_grupo,
            'strDescricao': intLinha.descricao_grupo,
            'intTipo': intLinha.tipo_grupo,
            'fltPreco': intLinha.faixaPreco_grupo,
            'dateSorteio': parseDate(intLinha.dataSorteio_grupo, 3),
            'intOpcao': '1'
        };
    };

    $scope.opcaoGrupo = function(intOpcao){
        if (intOpcao === "1"){
            $scope.alterarGrupo();
        }else{
            $scope.cadastrarGrupo();
        }
    };

    $scope.opcaoGrupoCadastro = function(intOpcao){
        $scope.cadastro =  {
            'blnAtivo': '0',
            'intGrupo': '',
            'strNome': '',
            'strDescricao': '',
            'intTipo': '',
            'fltPreco': '',
            'dateSorteio': '',
            'intOpcao': '0'
        };
    };

    $scope.alterarGrupo = function(){
        if ($scope.cadastro.strNome.length > 50){
            toastr.error('O numero maximo de caracteres para o nome é de 50.');
        }else if ($scope.cadastro.strDescricao.length > 100){
            toastr.error('O numero maximo de caracteres para o nome é de 50.');
        }else if ($scope.cadastro.intTipo  === ''){
            toastr.error('Para continuar é necessario selecionar um tipo.');
        }else if ($scope.cadastro.dateSorteio  === ''){
            toastr.error('Para continuar é necessario selecionar uma data.');
        }else{
            var strUrl = BASEURL + 'grupo/atualizarGrupo';
            var config = {
                headers: {
                    'Content-Type':  'application/json;charset=utf-8;',
                    'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                }
            };
            var data = $scope.cadastro;
            console.log(data);
            data.dateSorteio = parseDate(data.dateSorteio , 1);
            $http.post(strUrl, data, config).success(function(response){
                console.log(response);
                if (response.code == 1) {
                    toastr.success(response.message);
                    $scope.carregaGrupos();
                    $('#modalInserirEditar').modal('toggle');
                } else {
                    toastr.error(response.message);
                }
            }).error(function(error){
                toastr.error('Não foi possível alterar o grupo!');
            });
        }
    };

    $scope.entrarGrupo = function(idGrupo){
        var strUrl = BASEURL + 'grupo/entrarGrupo';
        var config = {
            headers: {
                'Content-Type':  'application/json;charset=utf-8;',
                'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
            }
        };
        var data = {
            'idGrupo': idGrupo
        }
        console.log(data);
        $http.post(strUrl, data, config).success(function(response){
            console.log(response);
            if (response.code == 1) {
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        }).error(function(error){
            toastr.error('Não foi possível entrar no grupo!');
        });
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

    function parseDate(input, tipo) {
        var parts = input;
        if (tipo === 1 ){
            parts = parts.split('/');
            return (parts[2]+'-'+parts[1]+'-'+parts[0]);
        }else if(tipo === 2){
            parts = parts.split('-');
            return (parts[2]+'/'+parts[1]+'/'+parts[0]);
        }else{
            parts = parts.split(' ');
            parts = parts[0].split('-');
            return (parts[2]+'/'+parts[1]+'/'+parts[0]);
        }
    }

});
