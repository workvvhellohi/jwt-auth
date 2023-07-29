<?php


namespace workvvhellohi\jwt\claim;

use workvvhellohi\jwt\exception\TokenExpiredException;

class Expiration extends Claim
{
    protected $name = 'exp';

    public function validatePayload()
    {
        if (time() >= (int)$this->getValue()) {
            throw new TokenExpiredException('The token is expired.');
        }
    }
}
