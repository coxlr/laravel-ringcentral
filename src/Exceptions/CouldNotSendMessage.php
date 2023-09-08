<?php

namespace Coxlr\RingCentral\Exceptions;

use Exception;

class CouldNotSendMessage extends Exception
{
    public static function toNumberNotProvided(): static
    {
        return new static('To number not provided');
    }

    public static function textNotProvided(): static
    {
        return new static('Message text not provided');
    }
}
