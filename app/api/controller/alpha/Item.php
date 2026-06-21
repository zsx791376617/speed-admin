<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\ItemService;

class Item extends BaseController
{
    private $itemService;

    public function __construct()
    {
        parent::__construct();
        $this->itemService = new ItemService();
    }

    public function list()
    {
        $user_id = request()->uid();
        $data = $this->itemService->getItemList($user_id);
        $this->success($data);
    }

    public function use()
    {
        $user_id = request()->uid();
        $item_id = $this->request->param('item_id', 0);
        $num = $this->request->param('num', 1);

        if (!$item_id || $num <= 0) {
            $this->error('参数错误', 1002);
        }

        $result = $this->itemService->useItem($user_id, $item_id, $num);
        if (!$result['success']) {
            $this->error($result['msg'], $result['code'] ?? 400);
        }
        $this->success('使用成功');
    }
}