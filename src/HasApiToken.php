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

    /**
     * Set custom fields into token
     * @param $payloads
     * @return HasApiToken
     */
    public function setPayloads($payloads): self {
        $this->payloads = $payloads;
        return $this;
    }

    /**
     * Set custom fields into token.
     * Different from setPayloads, this method will merge payloads.
     * @param $payloads
     * @return HasApiToken
     */
    public function withPayloads($payloads): self
    {
        $this->payloads = array_merge($this->payloads, $payloads);
        return $this;
    }

    public function createToken(): string
    {
        // TODO: 过期时间
        // TODO: 自动过期等特性
        $this->payloads = array_merge($this->payloads, ['sub' => $this->getAttribute("id")]);
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
