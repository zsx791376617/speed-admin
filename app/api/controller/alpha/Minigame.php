<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\MinigameService;

class Minigame extends BaseController
{
    private $minigameService;

    public function __construct()
    {
        parent::__construct();
        $this->minigameService = new MinigameService();
    }

    public function stackSettle()
    {
        $user_id = request()->uid();
        $score = $this->request->param('score', 0);

        if ($score < 0) {
            $this->error('参数错误', 1002);
        }

        $result = $this->minigameService->stackSettle($user_id, $score);
        if (!$result['success']) {
            $this->error('结算失败');
        }
        $this->success($result);
    }

    public function puzzleSave()
    {
        $user_id = request()->uid();
        $difficulty = $this->request->param('difficulty', 1);
        $progress = $this->request->param('progress', '');

        if ($difficulty < 1 || $difficulty > 3) {
            $this->error('难度参数错误', 1002);
        }

        $result = $this->minigameService->puzzleSave($user_id, $difficulty, $progress);
        if (!$result['success']) {
            $this->error('保存失败');
        }
        $this->success('保存成功');
    }

    public function puzzleSettle()
    {
        $user_id = request()->uid();
        $difficulty = $this->request->param('difficulty', 1);
        $score = $this->request->param('score', 0);

        if ($difficulty < 1 || $difficulty > 3 || $score < 0) {
            $this->error('参数错误', 1002);
        }

        $result = $this->minigameService->puzzleSettle($user_id, $difficulty, $score);
        if (!$result['success']) {
            $this->error('结算失败');
        }
        $this->success($result);
    }

    public function data()
    {
        $user_id = request()->uid();
        $data = $this->minigameService->getMinigameData($user_id);
        $this->success($data);
    }
}