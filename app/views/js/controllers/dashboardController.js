app.controller('dashboardController', function($scope, $rootScope, BASEURL, $window, $http, toastr, $location) {

    $scope.carregaGrupos = function(){
        var strUrl = BASEURL + 'grupo/retornarGrupos';
        var config = {
            headers: {
                'Content-Type':  'application/json;charset=utf-8;',
                'Authorization': 'Bearer ' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
            }
        };
        $http.post(strUrl, config).success(function(response){
            console.log(response);
            if (response.code == 1) {
                $scope.dadosGrupos = response.grupo;
            } else {
                toastr.error('Não foi possível carregar os dados, tente novamente mais tarde!');
            }
        }).error(function(error){
            toastr.error('Não foi possível realizar a pesquisa!');
        });

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
            var strUrl = BASEURL + 'grupo/criarGrupo';
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

    $scope.alterarGrupo = function(intOpcao){
        var strUrl = BASEURL + 'grupo/retornarGrupoId';
        var config = {
            headers: {
                'Content-Type':  'application/json;charset=utf-8;',
                'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
            }
        };
        var data = {
            'intIdGrupo': intOpcao
        };
        console.log(data);
        $http.post(strUrl, data, config).success(function(response){
            console.log(response);
          if (response.code == 1) {
            $scope.cadastro =  {
                'blnAtivo': response.grupo[0].ativo_grupo,
                'intGrupo': response.grupo[0].id_grupo,
                'strNome': response.grupo[0].nome_grupo,
                'strDescricao': response.grupo[0].descricao_grupo,
                'intTipo':  response.grupo[0].tipo_grupo,
                'fltPreco': response.grupo[0].faixaPreco_grupo,
                'dateSorteio': parseDate(response.grupo[0].dataSorteio_grupo, 3),
                'intOpcao': '0'
            };
                $('#modalInserirEditar').modal('toggle');
          } else{
            toastr.error(response.message);
          }
        }).error(function(error){
          toastr.error('Erro ao busca o grupo para alteração!');
        });
    };

    $scope.sortearGrupo = function(){
        //verifica se possui + de um usuario
        var strUrl = BASEURL + 'grupo/sortearGrupo';
        var config = {
            headers: {
                'Content-Type':  'application/json;charset=utf-8;',
                'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
            }
        };
        var data = {
            'intIdGrupo': $scope.idgrupo
        };
        console.log(data);
        $http.post(strUrl, data, config).success(function(response){
            console.log(response);
          if (response.code == 1) {

          } else{
            toastr.error(response.message);
          }
        }).error(function(error){
          toastr.error('Erro ao busca o grupo para alteração!');
        });
    };

    $scope.convidarGrupo = function(){
        console.log($scope.emailConvite.length);
        if ($scope.emailConvite.length == 0){
            toastr.error('É necessario pelo menos um email para enviar o convite.');
        }else{
              var strUrl = BASEURL + 'grupo/enviarConvite';
              var config = {
                  headers: {
                      'Content-Type':  'application/json;charset=utf-8;',
                      'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
                  }
              };
              var data = {
                  'listMail': $scope.emailConvite,
                  'idGrupo': $scope.idgrupo
              };
              console.log(data);
              $http.post(strUrl, data, config).success(function(response){
                  console.log(response);
                if (response.code == 1) {
                    toastr.success(response.message);
                    $scope.emailConvite = '';
                    $('#modalConvidar').modal('toggle');
                } else{
                  toastr.error(response.message);
                }
              }).error(function(error){
                toastr.error('Erro ao busca o grupo para alteração!');
              });
          }
    };

    $scope.setIdGrupo = function(intOpcao){
        $scope.idgrupo = intOpcao;
    };

    $scope.exibirResultado = function(idGrupo){
        var strUrl = BASEURL + 'grupo/resultado';
        var config = {
            headers: {
                'Content-Type':  'application/json;charset=utf-8;',
                'Authorization': 'Bearer' + JSON.parse($window.localStorage.getItem('projetoAmigo')).jwtTokenprojetoAmigo
            }
        };
        var data = {
            'intIdGrupo': idGrupo
        };
        console.log(data);
        $http.post(strUrl, data, config).success(function(response){
            console.log(response);
          if (response.code == 1) {

          } else{
            toastr.error(response.message);
          }
        }).error(function(error){
          toastr.error('Erro ao busca o grupo para alteração!');
        });

    };

    $scope.mudaRota = function(){
        $location.path('/criarGrupo');
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

    $scope.carregaGrupos();
    $scope.emailConvite = '';
});
