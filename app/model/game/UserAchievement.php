<?php

namespace app\model\game;

use core\base\BaseModel;

class UserAchievement extends BaseModel
{
    protected $table = 'speed_user_achievement';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public function achievement()
    {
        return $this->belongsTo(Achievement::class, 'achievement_id', 'id');
    }

    public static function getByUserId($user_id)
    {
        return self::where('user_id', $user_id)->with('achievement')->select();
    }

    public static function isUnlocked($user_id, $achievement_id)
    {
        return self::where('user_id', $user_id)->where('achievement_id', $achievement_id)->find() !== null;
    }
}