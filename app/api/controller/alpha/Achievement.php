<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\AchievementService;

class Achievement extends BaseController
{
    private $achievementService;

    public function __construct()
    {
        parent::__construct();
        $this->achievementService = new AchievementService();
    }

    public function list()
    {
        $user_id = request()->uid();
        $data = $this->achievementService->getAchievementList($user_id);
        $this->success($data);
    }

    public function reward()
    {
        $user_id = request()->uid();
        $achievement_id = $this->request->param('achievement_id', 0);

        if (!$achievement_id) {
            $this->error('参数缺失', 1002);
        }

        $result = $this->achievementService->getReward($user_id, $achievement_id);
        if (!$result['success']) {
            $this->error($result['msg']);
        }
        $this->success('领取成功');
    }
}