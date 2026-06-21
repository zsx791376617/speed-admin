<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\LevelService;

class Level extends BaseController
{
    private $levelService;

    public function __construct()
    {
        parent::__construct();
        $this->levelService = new LevelService();
    }

    public function list()
    {
        $user_id = request()->uid();
        $data = $this->levelService->getLevelList($user_id);
        $this->success($data);
    }

    public function settle()
    {
        $user_id = request()->uid();
        $level_id = $this->request->param('level_id', 0);
        $score = $this->request->param('score', 0);
        $star = $this->request->param('star', 0);
        $use_item_num = $this->request->param('use_item_num', 0);

        if (!$level_id || $score < 0 || $star < 0) {
            $this->error('参数缺失', 1002);
        }

        $result = $this->levelService->settle($user_id, $level_id, $score, $star, $use_item_num);
        if (!$result['success']) {
            $this->error($result['msg'], $result['code'] ?? 400);
        }
        $this->success($result['data']);
    }
}