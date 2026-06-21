<?php

namespace app\model\game;

use core\base\BaseModel;

class UserStamina extends BaseModel
{
    protected $table = 'speed_user_stamina';
    protected $pk = 'user_id';
    protected $autoWriteTimestamp = false;

    public static function getByUserId($user_id)
    {
        return self::where('user_id', $user_id)->find();
    }
}