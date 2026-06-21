<?php

namespace app\model\game;

use core\base\BaseModel;

class UserCollection extends BaseModel
{
    protected $table = 'speed_user_collection';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id', 'id');
    }

    public static function getByUserId($user_id)
    {
        return self::where('user_id', $user_id)->with('collection')->select();
    }

    public static function isCollected($user_id, $collection_id)
    {
        return self::where('user_id', $user_id)->where('collection_id', $collection_id)->find() !== null;
    }
}