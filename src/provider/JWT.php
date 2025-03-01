<?php


namespace workvvhellohi\jwt\provider;

use workvvhellohi\jwt\facade\JWTAuth;
use workvvhellohi\jwt\parser\AuthHeader;
use workvvhellohi\jwt\parser\Cookie;
use workvvhellohi\jwt\parser\Param;
use think\App;
use think\Container;
use think\facade\Config;
use think\Request;

class JWT
{
    private $request;

    private $config;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $config = require __DIR__ . '/../../config/config.php';
        if (strpos(App::VERSION, '6.') === 0) {
            $this->config = array_merge($config, Config::get('jwt') ?? []);
        } else {
            $this->config = array_merge($config, Config::get('jwt.') ?? []);
        }
    }

    protected function registerBlacklist()
    {
        Container::getInstance()->make('workvvhellohi\jwt\Blacklist', [
            new $this->config['blacklist_storage'],
        ])->setRefreshTTL($this->config['refresh_ttl'])->setGracePeriod($this->config['blacklist_grace_period']);
    }


    protected function registerProvider()
    {
        //builder asymmetric keys
        $keys = $this->config['secret']
            ? $this->config['secret']
            : [
                'public' => $this->config['public_key'],
                'private' => $this->config['private_key'],
                'password' => $this->config['password'],
            ];
        Container::getInstance()->make('workvvhellohi\jwt\provider\JWT\Lcobucci', [
            $this->config['algo'],
            $keys,
        ]);
    }

    protected function registerFactory()
    {
        Container::getInstance()->make('workvvhellohi\jwt\claim\Factory', [
            new Request(),
            $this->config['ttl'],
            $this->config['refresh_ttl'],
        ]);
    }

    protected function registerPayload()
    {
        Container::getInstance()->make('workvvhellohi\jwt\Payload', [
            Container::getInstance()->make('workvvhellohi\jwt\claim\Factory'),
        ]);
    }

    protected function registerManager()
    {
        Container::getInstance()->make('workvvhellohi\jwt\Manager', [
            Container::getInstance()->make('workvvhellohi\jwt\Blacklist'),
            Container::getInstance()->make('workvvhellohi\jwt\Payload'),
            Container::getInstance()->make('workvvhellohi\jwt\provider\JWT\Lcobucci'),
        ]);
    }

    protected function registerJWTAuth()
    {
        $chains = [
            'header' => new AuthHeader(),
            'cookie' => new Cookie(),
            'param' => new Param()
        ];

        $mode = $this->config['token_mode'];
        $setChain = [];

        foreach ($mode as $key => $chain) {
            if (isset($chains[$chain])) {
                $setChain[$key] = $chains[$chain];
            }
        }

        JWTAuth::parser()->setRequest($this->request)->setChain($setChain);
    }

    public function init()
    {
        $this->registerBlacklist();
        $this->registerProvider();
        $this->registerFactory();
        $this->registerPayload();
        $this->registerManager();
        $this->registerJWTAuth();
    }
}
