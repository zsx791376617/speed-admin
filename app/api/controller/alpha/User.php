<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\UserService;

class User extends BaseController
{
    private $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function login()
    {
        $platform = $this->request->param('platform', 1);
        $union_id = $this->request->param('union_id', '');
        $nick_name = $this->request->param('nick_name', '');
        $wx_avatar = $this->request->param('wx_avatar', '');

        if (!$union_id || !$nick_name) {
            $this->error('参数缺失', 1002);
        }

        $result = $this->userService->login($platform, $union_id, $nick_name, $wx_avatar);
        $this->success($result);
    }

    public function info()
    {
        $user_id = request()->uid();
        $data = $this->userService->getUserInfo($user_id);
        if (!$data) {
            $this->error('用户不存在');
        }
        $this->success($data);
    }
}