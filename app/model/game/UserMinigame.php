<?php

namespace app\model\game;

use core\base\BaseModel;

class UserMinigame extends BaseModel
{
    protected $table = 'speed_user_minigame';
    protected $pk = ['user_id', 'game_type'];
    protected $autoWriteTimestamp = false;

    const GAME_STACK = 1;
    const GAME_PUZZLE = 2;

    public static function getByUserId($user_id, $game_type)
    {
        return self::where('user_id', $user_id)->where('game_type', $game_type)->find();
    }
}