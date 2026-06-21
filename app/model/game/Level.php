<?php

namespace app\model\game;

use core\base\BaseModel;

class Level extends BaseModel
{
    protected $table = 'speed_level';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public static function getActiveLevels()
    {
        return self::where('status', 1)->order('id', 'asc')->select();
    }
}