<?php

namespace app\api\controller\alpha;

use core\base\BaseController;
use app\service\game\CollectionService;

class Collection extends BaseController
{
    private $collectionService;

    public function __construct()
    {
        parent::__construct();
        $this->collectionService = new CollectionService();
    }

    public function list()
    {
        $user_id = request()->uid();
        $data = $this->collectionService->getCollectionList($user_id);
        $this->success($data);
    }
}