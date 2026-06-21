<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\{Achievement, UserAchievement, UserLevel, UserMinigame};

class AchievementService extends BaseService
{
    public function getAchievementList($user_id)
    {
        $achievements = Achievement::getAllAchievements();
        $userAchievements = UserAchievement::getByUserId($user_id);
        $userAchievementMap = [];

        foreach ($userAchievements as $ua) {
            $userAchievementMap[$ua['achievement_id']] = [
                'get_time' => $ua['get_time'],
                'reward_get' => $ua['reward_get']
            ];
        }

        $result = [];
        foreach ($achievements as $achievement) {
            $unlocked = isset($userAchievementMap[$achievement['id']]);
            $result[] = [
                'id' => $achievement['id'],
                'name' => $achievement['name'],
                'icon' => $achievement['icon'],
                'condition' => $achievement['condition'],
                'reward_type' => $achievement['reward_type'],
                'reward_id' => $achievement['reward_id'],
                'reward_num' => $achievement['reward_num'],
                'unlocked' => $unlocked,
                'get_time' => $unlocked ? $userAchievementMap[$achievement['id']]['get_time'] : 0,
                'reward_get' => $unlocked ? $userAchievementMap[$achievement['id']]['reward_get'] : 0
            ];
        }
        return $result;
    }

    public function checkAndUnlock($user_id)
    {
        $achievements = Achievement::getAllAchievements();
        foreach ($achievements as $achievement) {
            if (UserAchievement::isUnlocked($user_id, $achievement['id'])) {
                continue;
            }

            if ($this->checkCondition($user_id, $achievement['id'])) {
                $this->unlockAchievement($user_id, $achievement['id']);
            }
        }
    }

    private function checkCondition($user_id, $achievement_id)
    {
        switch ($achievement_id) {
            case 1:
                $userLevel = UserLevel::where('user_id', $user_id)->where('pass_status', 1)->find();
                return $userLevel !== null;
            case 2:
                $passedCount = UserLevel::where('user_id', $user_id)->where('pass_status', 1)->count();
                return $passedCount >= 5;
            case 3:
                $puzzle = UserMinigame::getByUserId($user_id, UserMinigame::GAME_PUZZLE);
                return $puzzle && $puzzle['best_score'] >= 500;
            case 4:
                $stack = UserMinigame::getByUserId($user_id, UserMinigame::GAME_STACK);
                return $stack && $stack['best_score'] >= 1000;
            default:
                return false;
        }
    }

    private function unlockAchievement($user_id, $achievement_id)
    {
        $userAchievement = new UserAchievement();
        $userAchievement->user_id = $user_id;
        $userAchievement->achievement_id = $achievement_id;
        $userAchievement->get_time = time();
        $userAchievement->reward_get = 0;
        $userAchievement->save();
    }

    public function getReward($user_id, $achievement_id)
    {
        $userAchievement = UserAchievement::where('user_id', $user_id)
            ->where('achievement_id', $achievement_id)
            ->find();

        if (!$userAchievement) {
            return ['success' => false, 'msg' => '成就未解锁'];
        }

        if ($userAchievement['reward_get'] == 1) {
            return ['success' => false, 'msg' => '奖励已领取'];
        }

        $achievement = Achievement::find($achievement_id);
        $this->giveReward($user_id, $achievement);

        $userAchievement->reward_get = 1;
        $userAchievement->save();

        return ['success' => true];
    }

    private function giveReward($user_id, $achievement)
    {
        switch ($achievement['reward_type']) {
            case 1:
                $itemService = new ItemService();
                $itemService->addItem($user_id, $achievement['reward_id'], $achievement['reward_num']);
                break;
            case 2:
                $this->awardAvatar($user_id, $achievement['reward_id']);
                break;
            case 3:
                $userService = new UserService();
                $userService->updateScore($user_id, $achievement['reward_num']);
                break;
        }
    }

    private function awardAvatar($user_id, $avatar_id)
    {
        if (!\app\model\game\UserAvatar::where('user_id', $user_id)->where('avatar_id', $avatar_id)->find()) {
            $userAvatar = new \app\model\game\UserAvatar();
            $userAvatar->user_id = $user_id;
            $userAvatar->avatar_id = $avatar_id;
            $userAvatar->get_time = time();
            $userAvatar->source = 3;
            $userAvatar->save();
        }
    }
}