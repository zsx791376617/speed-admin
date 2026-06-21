<?php

namespace app\model\game;

use core\base\BaseModel;

class Item extends BaseModel
{
    protected $table = 'speed_item';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public static function getAllItems()
    {
        return self::select();
    }
}