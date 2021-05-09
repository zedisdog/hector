<?php
/*
 * dezsidog
 *
 */

namespace Cola\Hector;


use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use UnexpectedValueException;

class Guard
{
    protected string $key;

    /**
     * Create a new authentication guard.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __invoke(Request $request, UserProvider $provider)
    {
        $token = $request->bearerToken();
        if (!$token) {
            $token = $request->query('token');
        }
        if (!$token) {
            throw new UnauthorizedHttpException('invalid token');
        }
        try {
            $payload = (array)JWT::decode($token, $this->key, ['HS256']);
        } catch (ExpiredException | UnexpectedValueException $e) {
            throw new UnauthorizedHttpException($e->getMessage());
        }

        /** @var Authenticatable|HasApiToken $user */
        $user = $provider->retrieveById($payload['jti']);
        if (!$user) {
            return null;
        }
        return $user->withPayloads($payload);
    }
}
