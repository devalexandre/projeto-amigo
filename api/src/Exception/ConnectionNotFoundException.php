<?php

namespace WebService\Exception;

use Exception;

class ConnectionNotFoundException extends Exception
{
    public function __construct(string $strMessage)
    {
        parent::__construct($strMessage);
    }
}
