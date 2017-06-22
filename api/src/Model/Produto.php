<?php

namespace WebService\Model;

class Produto extends AppModel
{
    public function cadastrarProduto($objProduto)
    {
        $this->setConnection('mysql');
        $sql = 'INSERT INTO tab_produto
                        (nome_produto,
                        preço_produto,
                        link_produto,
                        dataCadastro_produto)
                 VALUES (:strNome,
                         :fltPreco,
                         :strLink,
                         :dateProduto)';
        $arrParametros = [
            ':strNome' => $objUsuario['strNome'],
            ':fltPreco' => $objUsuario['fltPreco'],
            ':strLink' => $objUsuario['strLink'],
            ':dateProduto' => $objUsuario['dateProduto'],
        ];
        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Não foi possivel criar o grupo!'];
        } else {
            return ['code' => 1, 'message' => 'Grupo criado com sucesso!'];
        }
    }

    public function editarPRoduto($objProduto)
    {
        $this->setConnection('mysql');
        $sql = 'UPDATE INTO tab_produto
                        (nome_produto,
                        preço_produto,
                        link_produto,
                        dataCadastro_produto)
                 VALUES (:strNome,
                         :fltPreco,
                         :strLink,
                         :dateProduto)';
        $arrParametros = [
            ':strNome' => $objUsuario['strNome'],
            ':fltPreco' => $objUsuario['fltPreco'],
            ':strLink' => $objUsuario['strLink'],
            ':dateProduto' => $objUsuario['dateProduto'],
        ];
        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Não foi possivel criar o grupo!'];
        } else {
            return ['code' => 1, 'message' => 'Grupo criado com sucesso!'];
        }
    }

    public function listaDesejos($objProduto)
    {
        $this->setConnection('mysql');

        //VERIFICA SE JA TEM O PRODUTO PARA O USUARIO CASO CONSTRARIO SOBE
        $sql = 'INSERT INTO tab_produto
                        (nome_produto,
                        preço_produto,
                        link_produto,
                        dataCadastro_produto)
                 VALUES (:strNome,
                         :fltPreco,
                         :strLink,
                         :dateProduto)';
        $arrParametros = [
            ':strNome' => $objUsuario['strNome'],
            ':fltPreco' => $objUsuario['fltPreco'],
            ':strLink' => $objUsuario['strLink'],
            ':dateProduto' => $objUsuario['dateProduto'],
        ];
        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Não foi possivel criar o grupo!'];
        } else {
            return ['code' => 1, 'message' => 'Grupo criado com sucesso!'];
        }
    }

}
