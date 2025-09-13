<?php

namespace App\Modules\User\Facade;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user-service';
    }
}
