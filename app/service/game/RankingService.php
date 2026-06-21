<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\Ranking;

class RankingService extends BaseService
{
    public function getRankingList($limit = 100)
    {
        $list = Ranking::getTopList($limit);
        $result = [];
        $rank = 1;
        foreach ($list as $item) {
            $result[] = [
                'rank' => $rank++,
                'user_id' => $item['user_id'],
                'nick_name' => $item['nick_name'],
                'avatar_url' => $item['avatar_url'],
                'score' => $item['score']
            ];
        }
        return $result;
    }

    public function getUserRank($user_id)
    {
        $user = Ranking::where('user_id', $user_id)->find();
        if (!$user) {
            return ['rank' => null, 'score' => 0];
        }

        $rank = Ranking::where('score', '>', $user['score'])->count() + 1;

        return [
            'rank' => $rank,
            'score' => $user['score'],
            'nick_name' => $user['nick_name'],
            'avatar_url' => $user['avatar_url']
        ];
    }
}