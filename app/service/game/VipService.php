<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\{UserVip, UserAvatar, User, UserStamina};

class VipService extends BaseService
{
    public function getVipInfo($user_id)
    {
        $vip = UserVip::getByUserId($user_id);
        if (!$vip) {
            return [
                'vip_status' => 0,
                'expire_time' => 0,
                'vip_days' => 0,
                'daily_gift_get' => 0
            ];
        }

        $isValid = $vip->isVipValid();
        $today = strtotime(date('Y-m-d'));
        $canGetGift = $isValid && ($vip['last_get_time'] < $today);

        return [
            'vip_status' => $isValid ? 1 : 0,
            'expire_time' => $vip['expire_time'],
            'vip_days' => $isValid ? (int)(($vip['expire_time'] - time()) / 86400) + 1 : 0,
            'daily_gift_get' => $canGetGift ? 0 : 1
        ];
    }

    public function getDailyGift($user_id)
    {
        $vip = UserVip::getByUserId($user_id);
        if (!$vip || !$vip->isVipValid()) {
            return ['success' => false, 'code' => 1005, 'msg' => '月卡未开通'];
        }

        $today = strtotime(date('Y-m-d'));
        if ($vip['last_get_time'] >= $today) {
            return ['success' => false, 'code' => 1005, 'msg' => '今日礼包已领取'];
        }

        $vip->daily_gift_get = 1;
        $vip->last_get_time = $today;
        $vip->save();

        $itemService = new ItemService();
        $itemService->addItem($user_id, 1, 2);
        $itemService->addItem($user_id, 2, 1);

        $staminaService = new StaminaService();
        $staminaService->addStamina($user_id, 2);

        return ['success' => true, 'gifts' => [
            ['item_id' => 1, 'num' => 2],
            ['item_id' => 2, 'num' => 1],
            ['type' => 'stamina', 'num' => 2]
        ]];
    }

    public function activateVip($user_id, $days = 30)
    {
        $vip = UserVip::getByUserId($user_id);
        $now = time();

        if (!$vip) {
            $vip = new UserVip();
            $vip->user_id = $user_id;
            $vip->expire_time = $now + $days * 86400;
            $vip->vip_days = $days;
        } else {
            if ($vip->expire_time > $now) {
                $vip->expire_time += $days * 86400;
                $vip->vip_days += $days;
            } else {
                $vip->expire_time = $now + $days * 86400;
                $vip->vip_days = $days;
            }
        }
        $vip->save();

        $user = User::find($user_id);
        $user->vip_status = 1;
        $user->vip_expire_time = $vip->expire_time;
        $user->update_time = $now;
        $user->save();

        $stamina = UserStamina::getByUserId($user_id);
        if ($stamina) {
            $stamina->vip_unlimited = 1;
            $stamina->save();
        }

        $this->awardVipAvatar($user_id);

        return ['success' => true, 'expire_time' => $vip->expire_time];
    }

    private function awardVipAvatar($user_id)
    {
        $vipAvatarId = 3;
        if (!UserAvatar::where('user_id', $user_id)->where('avatar_id', $vipAvatarId)->find()) {
            $userAvatar = new UserAvatar();
            $userAvatar->user_id = $user_id;
            $userAvatar->avatar_id = $vipAvatarId;
            $userAvatar->get_time = time();
            $userAvatar->source = 2;
            $userAvatar->save();
        }
    }
}