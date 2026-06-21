<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\{Level, UserLevel, UserStamina, UserCollection, Collection};

class LevelService extends BaseService
{
    public function getLevelList($user_id)
    {
        $levels = Level::getActiveLevels();
        $userLevels = UserLevel::getByUserId($user_id);
        $userLevelMap = [];

        foreach ($userLevels as $ul) {
            $userLevelMap[$ul['level_id']] = $ul;
        }

        $result = [];
        foreach ($levels as $level) {
            $userLevel = isset($userLevelMap[$level['id']]) ? $userLevelMap[$level['id']] : null;
            $unlocked = $this->isLevelUnlocked($level['id'], $userLevelMap);

            $result[] = [
                'id' => $level['id'],
                'level_name' => $level['level_name'],
                'map_img' => $level['map_img'],
                'difficulty' => $level['difficulty'],
                'game_time' => $level['game_time'],
                'unlocked' => $unlocked,
                'star' => $userLevel ? $userLevel['star'] : 0,
                'pass_status' => $userLevel ? $userLevel['pass_status'] : 0,
                'best_score' => $userLevel ? $userLevel['best_score'] : 0,
                'play_times' => $userLevel ? $userLevel['play_times'] : 0
            ];
        }
        return $result;
    }

    private function isLevelUnlocked($level_id, $userLevelMap)
    {
        $level = Level::find($level_id);
        if (!$level) {
            return false;
        }

        if ($level['unlock_condition'] == 0) {
            return true;
        }

        return isset($userLevelMap[$level['unlock_condition']]) && 
               $userLevelMap[$level['unlock_condition']]['pass_status'] == 1;
    }

    public function checkLevelUnlock($user_id, $level_id)
    {
        $userLevels = UserLevel::getByUserId($user_id);
        $userLevelMap = [];
        foreach ($userLevels as $ul) {
            $userLevelMap[$ul['level_id']] = $ul;
        }
        return $this->isLevelUnlocked($level_id, $userLevelMap);
    }

    public function settle($user_id, $level_id, $score, $star, $use_item_num = 0)
    {
        if (!$this->checkLevelUnlock($user_id, $level_id)) {
            return ['success' => false, 'code' => 1006, 'msg' => '关卡未解锁'];
        }

        $staminaService = new StaminaService();
        $staminaResult = $staminaService->consumeStamina($user_id, 1);
        if (!$staminaResult['success']) {
            return $staminaResult;
        }

        if ($use_item_num > 0) {
            $itemService = new ItemService();
            for ($i = 0; $i < $use_item_num; $i++) {
                $itemService->useItem($user_id, 1);
            }
        }

        $userLevel = UserLevel::getByUserLevel($user_id, $level_id);
        $now = time();

        if (!$userLevel) {
            $userLevel = new UserLevel();
            $userLevel->user_id = $user_id;
            $userLevel->level_id = $level_id;
        }

        $userLevel->play_times += 1;
        $userLevel->last_play_time = $now;

        if ($score > $userLevel->best_score) {
            $userLevel->best_score = $score;
        }

        if ($star > $userLevel->star) {
            $userLevel->star = $star;
        }

        if ($star >= 1 && $userLevel->pass_status == 0) {
            $userLevel->pass_status = 1;
            $this->awardCollection($user_id, $level_id);
        }

        $userLevel->save();

        $userService = new UserService();
        $userService->updateScore($user_id, $score);

        $this->checkAchievements($user_id);

        return ['success' => true, 'data' => $userLevel];
    }

    private function awardCollection($user_id, $level_id)
    {
        $collection = Collection::where('level_id', $level_id)->find();
        if ($collection && !UserCollection::isCollected($user_id, $collection['id'])) {
            $userCollection = new UserCollection();
            $userCollection->user_id = $user_id;
            $userCollection->collection_id = $collection['id'];
            $userCollection->get_time = time();
            $userCollection->save();
        }
    }

    private function checkAchievements($user_id)
    {
        $achievementService = new AchievementService();
        $achievementService->checkAndUnlock($user_id);
    }
}