<?php

namespace WebService\Model;

use WebService\Util\QueryBuilder;
use WebService\Util\Retorno;

class ConfigSistema extends AppModel
{

    public function salvarConfiguracao($strConfiguracao, $intValor)
    {
        $params = [$strConfiguracao => $intValor];

        $this->execute(QueryBuilder::update('cfg_sistema', $params, []), $params);

        return Retorno::sucesso('Configuração alterada com sucesso.');
    }

    public function recuperarConfiguracao($arrConfiguracao = [])
    {
        return Retorno::sucesso($this->select(QueryBuilder::select('cfg_sistema', $arrConfiguracao)));
    }
}
