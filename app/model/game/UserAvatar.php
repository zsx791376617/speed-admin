<?php

namespace app\model\game;

use core\base\BaseModel;

class UserAvatar extends BaseModel
{
    protected $table = 'speed_user_avatar';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public function avatar()
    {
        return $this->belongsTo(Avatar::class, 'avatar_id', 'id');
    }
}