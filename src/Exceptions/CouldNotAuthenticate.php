<?php

namespace Coxlr\RingCentral\Exceptions;

use Exception;

class CouldNotAuthenticate extends Exception
{
    public static function operatorLoginFailed()
    {
        return new static('Failed to log in operator extension');
    }

    public static function adminLoginFailed()
    {
        return new static('Failed to log in admin extension');
    }
}
