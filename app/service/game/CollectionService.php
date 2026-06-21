<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\{Collection, UserCollection};

class CollectionService extends BaseService
{
    public function getCollectionList($user_id)
    {
        $collections = Collection::getAllCollections();
        $userCollections = UserCollection::getByUserId($user_id);
        $userCollectionMap = [];

        foreach ($userCollections as $uc) {
            $userCollectionMap[$uc['collection_id']] = $uc['get_time'];
        }

        $result = [];
        foreach ($collections as $collection) {
            $collected = isset($userCollectionMap[$collection['id']]);
            $result[] = [
                'id' => $collection['id'],
                'name' => $collection['name'],
                'icon' => $collection['icon'],
                'type' => $collection['type'],
                'level_id' => $collection['level_id'],
                'collected' => $collected,
                'get_time' => $collected ? $userCollectionMap[$collection['id']] : 0
            ];
        }

        return [
            'total' => count($collections),
            'collected' => count($userCollections),
            'list' => $result
        ];
    }
}