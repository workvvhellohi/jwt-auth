<?php


namespace workvvhellohi\jwt\parser;

use workvvhellohi\jwt\contract\Parser as ParserContract;
use think\Request;

class Param implements ParserContract
{
    use KeyTrait;

    public function parse(Request $request)
    {
        return $request->param($this->key);
    }
}
