<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\ShareService;

class Share extends BaseController
{
    private $shareService;

    public function __construct()
    {
        parent::__construct();
        $this->shareService = new ShareService();
    }

    public function create()
    {
        $user_id = request()->uid();
        $level_id = $this->request->param('level_id', 0);

        $result = $this->shareService->createShare($user_id, $level_id);
        $this->success($result);
    }

    public function parse()
    {
        $share_code = $this->request->param('share_code', '');

        if (!$share_code) {
            $this->error('参数缺失', 1002);
        }

        $result = $this->shareService->parseShare($share_code);
        if (!$result['success']) {
            $this->error($result['msg']);
        }
        $this->success($result['data']);
    }

    public function help()
    {
        $user_id = request()->uid();
        $share_code = $this->request->param('share_code', '');

        if (!$share_code) {
            $this->error('参数缺失', 1002);
        }

        $result = $this->shareService->helpShare($user_id, $share_code);
        if (!$result['success']) {
            $this->error($result['msg']);
        }
        $this->success($result['msg']);
    }
}