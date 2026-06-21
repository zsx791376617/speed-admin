<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\StaminaService;

class Stamina extends BaseController
{
    private $staminaService;

    public function __construct()
    {
        parent::__construct();
        $this->staminaService = new StaminaService();
    }

    public function get()
    {
        $user_id = request()->uid();
        $data = $this->staminaService->getStamina($user_id);
        $this->success($data);
    }
}