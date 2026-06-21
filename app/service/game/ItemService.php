<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\{UserItem, Item};

class ItemService extends BaseService
{
    public function getItemList($user_id)
    {
        $userItems = UserItem::getByUserId($user_id);
        $items = Item::getAllItems();

        $userItemMap = [];
        foreach ($userItems as $ui) {
            $userItemMap[$ui['item_id']] = $ui['num'];
        }

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'id' => $item['id'],
                'item_name' => $item['item_name'],
                'icon_url' => $item['icon_url'],
                'type' => $item['type'],
                'describe' => $item['describe'],
                'num' => isset($userItemMap[$item['id']]) ? $userItemMap[$item['id']] : 0
            ];
        }
        return $result;
    }

    public function useItem($user_id, $item_id, $num = 1)
    {
        $userItem = UserItem::getByUserItem($user_id, $item_id);
        if (!$userItem || $userItem['num'] < $num) {
            return ['success' => false, 'code' => 1004, 'msg' => '道具不足'];
        }

        $userItem->num -= $num;
        $userItem->update_time = time();
        $userItem->save();

        return ['success' => true];
    }

    public function addItem($user_id, $item_id, $num)
    {
        $userItem = UserItem::getByUserItem($user_id, $item_id);
        $now = time();

        if (!$userItem) {
            $userItem = new UserItem();
            $userItem->user_id = $user_id;
            $userItem->item_id = $item_id;
            $userItem->num = $num;
            $userItem->update_time = $now;
        } else {
            $userItem->num += $num;
            $userItem->update_time = $now;
        }

        $userItem->save();
        return true;
    }
}