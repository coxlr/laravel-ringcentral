<?php

namespace Coxlr\RingCentral\Facades;

use Illuminate\Support\Facades\Facade;

class RingCentral extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ringcentral';
    }
}
