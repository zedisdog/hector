<?php
/*
 * dezsidog
 *
 */

namespace Cola\Hector;


use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasApiToken
 * @package Cola\Hector
 * @mixin Model
 */
trait HasApiToken
{
    public array $payloads = [];

    public function withPayloads($payloads): self
    {
        $this->payloads = $payloads;
        return $this;
    }

    public function createToken(): string
    {
        // todo: 过期时间
        $this->payloads = array_merge($this->payloads, ['jti' => $this->id]);
        return JWT::encode($this->payloads, config('hector.key'), config('hector.algorithm'));
    }

    public function getPayload(string $key = '', $default = null)
    {
        if (!$key) {
            return $this->payloads;
        }

        return $this->payloads[$key] ?? $default;
    }
}
