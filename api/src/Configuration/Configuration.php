<?php
namespace WebService\Configuration;

use WebService\Exception\ConfigurationNotFoundException;

final class Configuration
{
    private static $arrayConfigurations = [];
    private static function loadConfig()
    {
        $strPath = dirname(dirname(dirname(__DIR__)));
        self::$arrayConfigurations = require $strPath.'/api/config/config.php';
    }

    public static function read(string $key)
    {
        self::loadConfig();
        if (array_key_exists($key, self::$arrayConfigurations)) {
            return self::$arrayConfigurations[$key];
        } else {
            throw new ConfigurationNotFoundException('Configuração não encontrada!');
        }
    }
}
