<?php

namespace Coxlr\RingCentral\Exceptions;

use Exception;

class CouldNotSendMessage extends Exception
{
    public static function toNumberNotProvided()
    {
        return new static('To number not provided');
    }

    public static function textNotProvided()
    {
        return new static('Message text not provided');
    }
}
