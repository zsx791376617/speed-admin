<?php

namespace app\model\game;

use core\base\BaseModel;

class UserVip extends BaseModel
{
    protected $table = 'speed_user_vip';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public static function getByUserId($user_id)
    {
        return self::where('user_id', $user_id)->find();
    }

    public function isVipValid()
    {
        return $this->expire_time > time();
    }
}