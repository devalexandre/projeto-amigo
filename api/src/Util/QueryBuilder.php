<?php

namespace WebService\Util;

final class QueryBuilder
{

    private static function obtemClausulaWhere(array $arrWhere)
    {
        //pega a primeira chave do array
        $strChave = implode('', array_keys(array_slice($arrWhere, 0, 1)));
        //monta o where usando um array map
        $arrWhere = array_map(function ($chave) use ($strChave) {
            if ($chave === $strChave) {
                return "WHERE $chave = :$chave ";
            }
            return "AND $chave = :$chave ";
        }, array_keys($arrWhere));

        return implode(' ', $arrWhere);
    }

    public static function select($strTabela, $arrCampos = null, $arrWhere = null)
    {
        $strSql = 'SELECT ';
        $strSql.= (empty($arrCampos)) ? ' * ' : implode(', ', $arrCampos);
        $strSql.= ' FROM '.$strTabela.' ';

        if (!empty($arrWhere)) {
            $strSql.= self::obtemClausulaWhere($arrWhere);
        }

        return $strSql;
    }

    public static function insert($strTabela, $arrCamposValores)
    {
        $arrColunas = array_keys($arrCamposValores);
        $arrParametros = array_map(function ($valor) {
            return ':'.$valor;
        }, $arrColunas);

        return "INSERT INTO $strTabela (".implode(', ', $arrColunas).") VALUES (".implode(', ', $arrParametros).")";
    }

    public static function update($strTabela, $arrCamposValores, $arrWhere)
    {
        $strSql = "UPDATE $strTabela SET ";

        $arrCampos = array_map(function ($valor) {
            return $valor.' = :'.$valor;
        }, array_keys($arrCamposValores));

        $strSql .= implode(', ', $arrCampos). ' ';

        if (!empty($arrWhere)) {
            $strSql .= self::obtemClausulaWhere($arrWhere);
        }

        return $strSql;
    }

    public static function delete($strTabela, $arrWhere)
    {
        return "DELETE FROM $strTabela ".self::obtemClausulaWhere($arrWhere);
    }
}
