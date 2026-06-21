<?php
namespace app\middleware;

use Closure;
use think\Request;
use think\Response;

class Cors
{
    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // 获取请求的 Origin
        $origin = $request->header('origin');
        
        // 如果有 Origin 头，设置为具体域名；否则设置为空
        if ($origin) {
            $this->header['Access-Control-Allow-Origin'] = $origin;
        } else {
            $this->header['Access-Control-Allow-Origin'] = '';
        }

        // 处理 OPTIONS 请求
        if ($request->method() === 'OPTIONS') {
            return Response::create('', 'html', 200)->header($this->header);
        }

        return $next($request)->header($this->header);
    }
}
