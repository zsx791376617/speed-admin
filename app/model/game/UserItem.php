<?php

namespace app\model\game;

use core\base\BaseModel;

class UserItem extends BaseModel
{
    protected $table = 'speed_user_item';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public static function getByUserId($user_id)
    {
        return self::where('user_id', $user_id)->with('item')->select();
    }

    public static function getByUserItem($user_id, $item_id)
    {
        return self::where('user_id', $user_id)->where('item_id', $item_id)->find();
    }
}