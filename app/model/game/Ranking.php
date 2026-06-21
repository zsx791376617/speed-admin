<?php

namespace app\model\game;

use core\base\BaseModel;

class Ranking extends BaseModel
{
    protected $table = 'speed_ranking';
    protected $pk = 'user_id';
    protected $autoWriteTimestamp = false;

    public static function getTopList($limit = 100)
    {
        return self::order('score', 'desc')->limit($limit)->select();
    }

    public static function getRankByScore($score)
    {
        return self::where('score', '>', $score)->count() + 1;
    }
}