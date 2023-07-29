<?php


namespace workvvhellohi\jwt;

use workvvhellohi\jwt\claim\Factory;
use workvvhellohi\jwt\claim\Issuer;
use workvvhellohi\jwt\claim\Audience;
use workvvhellohi\jwt\claim\Expiration;
use workvvhellohi\jwt\claim\IssuedAt;
use workvvhellohi\jwt\claim\JwtId;
use workvvhellohi\jwt\claim\NotBefore;
use workvvhellohi\jwt\claim\Subject;

class Payload
{
    protected $factory;

    protected $classMap
        = [
            'aud' => Audience::class,
            'exp' => Expiration::class,
            'iat' => IssuedAt::class,
            'iss' => Issuer::class,
            'jti' => JwtId::class,
            'nbf' => NotBefore::class,
            'sub' => Subject::class,
        ];

    protected $claims;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function customer(array $claim = [])
    {
        foreach ($claim as $key => $value) {
            $this->factory->customer(
                $key,
                is_object($claim) && method_exists($claim, 'getValue') ? $value->getValue() : $value
            );
        }

        return $this;
    }

    public function get()
    {
        $claim = $this->factory->builder()->getClaims();

        return $claim;
    }

    public function check($refresh = false)
    {
        $this->factory->validate($refresh);

        return $this;
    }
}
