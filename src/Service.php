<?php


namespace workvvhellohi\jwt;

use workvvhellohi\jwt\command\SecretCommand;
use workvvhellohi\jwt\middleware\InjectJwt;
use workvvhellohi\jwt\provider\JWT as JWTProvider;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(SecretCommand::class);
        $this->app->middleware->add(InjectJwt::class);
    }
}
