<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\VipService;

class Vip extends BaseController
{
    private $vipService;

    public function __construct()
    {
        parent::__construct();
        $this->vipService = new VipService();
    }

    public function info()
    {
        $user_id = request()->uid();
        $data = $this->vipService->getVipInfo($user_id);
        $this->success($data);
    }

    public function dailyGift()
    {
        $user_id = request()->uid();
        $result = $this->vipService->getDailyGift($user_id);
        if (!$result['success']) {
            $this->error($result['msg'], $result['code'] ?? 400);
        }
        $this->success($result['gifts'], '领取成功');
    }
}