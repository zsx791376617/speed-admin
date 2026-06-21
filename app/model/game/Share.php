<?php

namespace app\model\game;

use core\base\BaseModel;

class Share extends BaseModel
{
    protected $table = 'speed_share';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public static function getByShareCode($share_code)
    {
        return self::where('share_code', $share_code)->where('expire_time', '>', time())->find();
    }

    public static function generateShareCode()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $code;
    }
}