<?php

namespace WebService\Exception;

use Exception;

class ConfigurationNotFoundException extends Exception
{
    public function __construct(string $strMessage)
    {
        parent::__construct($strMessage);
    }
}
