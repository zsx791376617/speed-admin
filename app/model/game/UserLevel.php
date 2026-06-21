<?php

namespace app\model\game;

use core\base\BaseModel;

class UserLevel extends BaseModel
{
    protected $table = 'speed_user_level';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    public static function getByUserId($user_id)
    {
        return self::where('user_id', $user_id)->with('level')->select();
    }

    public static function getByUserLevel($user_id, $level_id)
    {
        return self::where('user_id', $user_id)->where('level_id', $level_id)->find();
    }
}