<?php


namespace workvvhellohi\jwt\contract;

use think\Request;

interface Parser
{
    public function parse(Request $request);
}
