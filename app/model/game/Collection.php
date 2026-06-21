<?php

namespace app\model\game;

use core\base\BaseModel;

class Collection extends BaseModel
{
    protected $table = 'speed_collection';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public static function getAllCollections()
    {
        return self::select();
    }
}