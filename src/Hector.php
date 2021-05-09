<?php
/*
 * dezsidog
 *
 */

namespace Cola\Hector;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Hector
{
    /**
     * Set the current user for the application with the given abilities.
     *
     * @param HasApiToken|Authenticatable|Model $user
     * @param array $payloads
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public static function actingAs($user, array $payloads = [])
    {
        if (!empty($payloads)) {
            $user->withPayloads($payloads);
        }
        if ($user->wasRecentlyCreated) {
            $user->wasRecentlyCreated = false;
        }
        app('auth')->guard('hector')->setUser($user);

        app('auth')->shouldUse('hector');

        return $user;
    }
}
