<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\RankingService;

class Ranking extends BaseController
{
    private $rankingService;

    public function __construct()
    {
        parent::__construct();
        $this->rankingService = new RankingService();
    }

    public function list()
    {
        $limit = $this->request->param('limit', 100);
        $data = $this->rankingService->getRankingList($limit);
        $this->success($data);
    }

    public function myRank()
    {
        $user_id = request()->uid();
        $data = $this->rankingService->getUserRank($user_id);
        $this->success($data);
    }
}