<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\{UserStamina, UserVip};

class StaminaService extends BaseService
{
    const RECOVER_INTERVAL = 300;
    const STAMINA_PER_RECOVER = 1;

    public function getStamina($user_id)
    {
        $stamina = UserStamina::getByUserId($user_id);
        if (!$stamina) {
            return ['stamina' => 5, 'max_stamina' => 5, 'recover_time' => time() + self::RECOVER_INTERVAL];
        }

        $vip = UserVip::getByUserId($user_id);
        if ($vip && $vip->isVipValid()) {
            return [
                'stamina' => $stamina['max_stamina'],
                'max_stamina' => $stamina['max_stamina'],
                'recover_time' => 0,
                'vip_unlimited' => 1
            ];
        }

        $now = time();
        $staminaData = $this->calculateStamina($stamina, $now);

        return [
            'stamina' => $staminaData['stamina'],
            'max_stamina' => $stamina['max_stamina'],
            'recover_time' => $staminaData['recover_time'],
            'vip_unlimited' => 0
        ];
    }

    private function calculateStamina($stamina, $now)
    {
        if ($stamina['recover_time'] <= $now && $stamina['stamina'] < $stamina['max_stamina']) {
            $recoverCount = (int)(($now - $stamina['recover_time']) / self::RECOVER_INTERVAL) + 1;
            $newStamina = min($stamina['stamina'] + $recoverCount * self::STAMINA_PER_RECOVER, $stamina['max_stamina']);
            $newRecoverTime = $stamina['recover_time'] + $recoverCount * self::RECOVER_INTERVAL;

            $stamina->stamina = $newStamina;
            $stamina->recover_time = $newRecoverTime;
            $stamina->save();

            return ['stamina' => $newStamina, 'recover_time' => $newRecoverTime];
        }

        return ['stamina' => $stamina['stamina'], 'recover_time' => $stamina['recover_time']];
    }

    public function consumeStamina($user_id, $amount)
    {
        $vip = UserVip::getByUserId($user_id);
        if ($vip && $vip->isVipValid()) {
            return ['success' => true];
        }

        $stamina = UserStamina::getByUserId($user_id);
        if (!$stamina) {
            return ['success' => false, 'code' => 1003, 'msg' => '体力不足'];
        }

        $now = time();
        $staminaData = $this->calculateStamina($stamina, $now);

        if ($staminaData['stamina'] < $amount) {
            return ['success' => false, 'code' => 1003, 'msg' => '体力不足'];
        }

        $stamina->stamina = $staminaData['stamina'] - $amount;
        if ($stamina->stamina < $stamina->max_stamina && $stamina->recover_time <= $now) {
            $stamina->recover_time = $now + self::RECOVER_INTERVAL;
        }
        $stamina->save();

        return ['success' => true, 'stamina' => $stamina->stamina];
    }

    public function addStamina($user_id, $amount)
    {
        $stamina = UserStamina::getByUserId($user_id);
        if (!$stamina) {
            return false;
        }

        $stamina->stamina = min($stamina->stamina + $amount, $stamina->max_stamina);
        $stamina->save();

        return true;
    }
}