<?php


namespace workvvhellohi\jwt\provider\storage;


use workvvhellohi\jwt\contract\Storage;
use think\facade\Cache;

class Tp6 implements Storage
{
    public function delete($key)
    {
        return Cache::delete($key);
    }

    public function get($key)
    {
        return Cache::get($key);
    }

    public function set($key, $val, $time = 0)
    {
        return Cache::set($key, $val, $time);
    }
}