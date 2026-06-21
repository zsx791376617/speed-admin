<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\UserMinigame;

class MinigameService extends BaseService
{
    public function stackSettle($user_id, $score)
    {
        $userMinigame = UserMinigame::getByUserId($user_id, UserMinigame::GAME_STACK);
        $now = time();

        if (!$userMinigame) {
            $userMinigame = new UserMinigame();
            $userMinigame->user_id = $user_id;
            $userMinigame->game_type = UserMinigame::GAME_STACK;
        }

        $userMinigame->play_times += 1;
        if ($score > $userMinigame->best_score) {
            $userMinigame->best_score = $score;
        }
        $userMinigame->save();

        $userService = new UserService();
        $userService->updateScore($user_id, $score);

        $achievementService = new AchievementService();
        $achievementService->checkAndUnlock($user_id);

        return ['success' => true, 'best_score' => $userMinigame->best_score];
    }

    public function puzzleSave($user_id, $difficulty, $progress)
    {
        $userMinigame = UserMinigame::getByUserId($user_id, UserMinigame::GAME_PUZZLE);

        if (!$userMinigame) {
            $userMinigame = new UserMinigame();
            $userMinigame->user_id = $user_id;
            $userMinigame->game_type = UserMinigame::GAME_PUZZLE;
        }

        $userMinigame->difficulty = $difficulty;
        $userMinigame->progress = $progress;
        $userMinigame->save();

        return ['success' => true];
    }

    public function puzzleSettle($user_id, $difficulty, $score)
    {
        $userMinigame = UserMinigame::getByUserId($user_id, UserMinigame::GAME_PUZZLE);
        $now = time();

        if (!$userMinigame) {
            $userMinigame = new UserMinigame();
            $userMinigame->user_id = $user_id;
            $userMinigame->game_type = UserMinigame::GAME_PUZZLE;
        }

        $userMinigame->difficulty = $difficulty;
        $userMinigame->play_times += 1;
        $userMinigame->progress = '';
        if ($score > $userMinigame->best_score) {
            $userMinigame->best_score = $score;
        }
        $userMinigame->save();

        $userService = new UserService();
        $userService->updateScore($user_id, $score);

        $achievementService = new AchievementService();
        $achievementService->checkAndUnlock($user_id);

        return ['success' => true, 'best_score' => $userMinigame->best_score];
    }

    public function getMinigameData($user_id)
    {
        $stack = UserMinigame::getByUserId($user_id, UserMinigame::GAME_STACK);
        $puzzle = UserMinigame::getByUserId($user_id, UserMinigame::GAME_PUZZLE);

        return [
            'stack' => $stack ? [
                'best_score' => $stack['best_score'],
                'play_times' => $stack['play_times']
            ] : ['best_score' => 0, 'play_times' => 0],
            'puzzle' => $puzzle ? [
                'best_score' => $puzzle['best_score'],
                'play_times' => $puzzle['play_times'],
                'difficulty' => $puzzle['difficulty'],
                'progress' => $puzzle['progress']
            ] : ['best_score' => 0, 'play_times' => 0, 'difficulty' => 1, 'progress' => '']
        ];
    }
}