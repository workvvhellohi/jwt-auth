<?php

namespace thans\jwt;

use thans\jwt\exception\TokenBlacklistException;
use thans\jwt\provider\JWT\Provider;

class Manager
{
    protected $blacklist;

    protected $payload;

    protected $refresh;

    public function __construct(
        Blacklist $blacklist,
        Payload $payload,
        Provider $provider
    ) {
        $this->blacklist = $blacklist;
        $this->payload   = $payload;
        $this->provider  = $provider;
    }

    public function encode($customerClaim)
    {
        $payload = $this->payload->customer($customerClaim);
        $token   = $this->provider->encode($payload->get());

        return new Token($token);
    }

    public function decode(Token $token)
    {
        $payload = $this->provider->decode($token->get());
        $this->payload->customer($payload)->check($this->refresh);

        //blacklist verify
        if ($this->validate($payload)) {
            throw new TokenBlacklistException('The token is in blacklist.');
        }

        return $payload;
    }

    public function refresh(Token $token)
    {
        $this->setRefresh();
        $payload = $this->decode($token);

        $this->invalidate($token);

        $this->payload->customer($payload)
            ->check(true);

        return $this->encode($payload);
    }

    public function invalidate(Token $token)
    {
        return $this->blacklist->add($this->decode($token, true));
    }

    public function validate($payload)
    {
        return $this->blacklist->has($payload);
    }

    public function setRefresh($refresh = true)
    {
        $this->refresh = true;

        return $this;
    }
}
