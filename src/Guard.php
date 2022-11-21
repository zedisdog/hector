<?php
/*
 * dezsidog
 *
 */

namespace Cola\Hector;


use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use UnexpectedValueException;

class Guard
{
    protected Key $key;

    /**
     * Create a new authentication guard.
     *
     * @param string $key
     */
    public function __construct(string $key, string $algorithm)
    {
        $this->key = new Key($key, $algorithm);
    }

    public function __invoke(Request $request, UserProvider $provider)
    {
        if ($request->user()) {
            return $request->user();
        }
        $token = $request->bearerToken();
        if (!$token) {
            $token = $request->query('token');
        }
        if (!$token) {
            throw new UnauthorizedHttpException('无效的token');
        }
        try {
            $payload = (array)JWT::decode($token, $this->key);
        } catch (ExpiredException | UnexpectedValueException $e) {
            throw new UnauthorizedHttpException('无效的token');
        }

        /** @var Authenticatable|HasApiToken $user */
        $user = $provider->retrieveById($payload['sub']);
        if (!$user) {
            return null;
        }
        return $user->withPayloads($payload);
    }
}
