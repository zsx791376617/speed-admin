<?php

namespace app\model\game;

use core\base\BaseModel;

class Avatar extends BaseModel
{
    protected $table = 'speed_avatar';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;
}