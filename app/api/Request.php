<?php

namespace app\api;

use think\Request as BaseRequest;

class Request extends BaseRequest
{
    protected $middleware = [
        \app\middleware\Cors::class,
    ];
}