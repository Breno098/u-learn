<?php

namespace App\Helpers\Acess;

use Illuminate\Support\Facades\Gate;

class Authorize
{
    /**
     * @param string|array $keys
     * @return bool
     */
    public static function any(string|array $keys): bool
    {
        return Gate::any($keys);
    }

    /**
     * @param string|array $keys
     * @param integer $code
     * @param string $message
     * @return void
     */
    public static function abortIfNot(string|array $keys, int $code = 403, string $message = 'Ação não autorizada')
    {
        abort_if(Gate::none($keys), $code, $message);
    }
}
