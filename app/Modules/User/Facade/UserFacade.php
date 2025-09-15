<?php
/**
 * Author: Miko Tan See Qian
 * Date: 2025-09-15
 */

namespace App\Modules\User\Facade;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user-service';
    }
}
