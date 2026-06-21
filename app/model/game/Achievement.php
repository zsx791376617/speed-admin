<?php

namespace app\model\game;

use core\base\BaseModel;

class Achievement extends BaseModel
{
    protected $table = 'speed_achievement';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public static function getAllAchievements()
    {
        return self::select();
    }
}