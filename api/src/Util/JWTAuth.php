<?php

namespace WebService\Util;

use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use WebService\Configuration\Configuration;

class JWTAuth
{

    public static function encode(array $arrayToken)
    {
        // return JWT::encode($arrayToken, self::CHAVE_JWT, self::ALGORITHM);
        return JWT::encode($arrayToken, Configuration::read('authentication')['jwtKey'], Configuration::read('authentication')['algorithm']);
    }

}
