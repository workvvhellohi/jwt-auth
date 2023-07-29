<?php

use workvvhellohi\jwt\command\SecretCommand;
use workvvhellohi\jwt\provider\JWT as JWTProvider;
use think\Console;
use think\App;

if (strpos(App::VERSION, '6.') !== 0) {
    Console::addDefaultCommands([
        SecretCommand::class
    ]);
    (new JWTProvider(app('request')))->init();
}
