<?php

namespace Coxlr\RingCentral;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Coxlr\RingCentral\RingCentral
 */
class RingCentralFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ringcentral';
    }
}
