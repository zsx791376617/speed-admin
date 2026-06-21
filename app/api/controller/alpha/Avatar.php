<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\UserService;

class Avatar extends BaseController
{
    private $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function list()
    {
        $user_id = request()->uid();
        $data = $this->userService->getAvatarList($user_id);
        $this->success($data);
    }

    public function switch()
    {
        $user_id = request()->uid();
        $avatar_id = $this->request->param('avatar_id', 0);

        if (!$avatar_id) {
            $this->error('参数缺失', 1002);
        }

        $result = $this->userService->switchAvatar($user_id, $avatar_id);
        if (!$result) {
            $this->error('切换失败，未拥有该头像');
        }
        $this->success('切换成功');
    }
}