<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\{User, Avatar, UserAvatar, UserStamina, UserVip};
use core\service\jwt\Factory;

class UserService extends BaseService
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login($platform, $union_id, $nick_name, $wx_avatar = null)
    {
        $user = User::getByUnionId($union_id, $platform);
        $now = time();

        if (!$user) {
            $user = $this->createUser($platform, $union_id, $nick_name, $wx_avatar);
        } else {
            $user->nick_name = $nick_name;
            if ($wx_avatar) {
                $user->wx_avatar = $wx_avatar;
            }
            $user->last_login_time = $now;
            $user->update_time = $now;
            $user->save();
        }

        $this->initUserData($user['id']);

        $jwt = Factory::getInstance('api');
        $token = $jwt->generateToken(['id' => $user['id']]);

        return [
            'token' => $token,
            'user' => $this->getUserInfo($user['id'])
        ];
    }

    private function createUser($platform, $union_id, $nick_name, $wx_avatar)
    {
        $now = time();
        $user = new User();
        $user->union_id = $union_id;
        $user->platform = $platform;
        $user->nick_name = $nick_name;
        $user->wx_avatar = $wx_avatar;
        $user->avatar_id = 1;
        $user->create_time = $now;
        $user->update_time = $now;
        $user->save();
        return $user;
    }

    private function initUserData($user_id)
    {
        if (!UserStamina::getByUserId($user_id)) {
            $stamina = new UserStamina();
            $stamina->user_id = $user_id;
            $stamina->stamina = 5;
            $stamina->max_stamina = 5;
            $stamina->recover_time = time() + 300;
            $stamina->save();
        }

        if (!UserAvatar::where('user_id', $user_id)->where('avatar_id', 1)->find()) {
            $userAvatar = new UserAvatar();
            $userAvatar->user_id = $user_id;
            $userAvatar->avatar_id = 1;
            $userAvatar->get_time = time();
            $userAvatar->source = 1;
            $userAvatar->save();
        }

        if (!UserVip::getByUserId($user_id)) {
            $vip = new UserVip();
            $vip->user_id = $user_id;
            $vip->save();
        }
    }

    public function getUserInfo($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return null;
        }

        $avatar = Avatar::find($user['avatar_id']);
        $stamina = UserStamina::getByUserId($user_id);
        $vip = UserVip::getByUserId($user_id);

        $avatarUrl = $avatar ? $avatar['img_url'] : $user['wx_avatar'];

        return [
            'id' => $user['id'],
            'nick_name' => $user['nick_name'],
            'avatar_url' => $avatarUrl,
            'total_score' => $user['total_score'],
            'max_score' => $user['max_score'],
            'stamina' => $stamina ? $stamina['stamina'] : 5,
            'max_stamina' => $stamina ? $stamina['max_stamina'] : 5,
            'vip_status' => $vip ? ($vip->isVipValid() ? 1 : 0) : 0,
            'vip_expire_time' => $vip ? $vip['expire_time'] : 0,
            'platform' => $user['platform']
        ];
    }

    public function getAvatarList($user_id)
    {
        $avatars = Avatar::where('status', 1)->order('sort', 'asc')->select();
        $userAvatars = UserAvatar::where('user_id', $user_id)->column('avatar_id');

        $result = [];
        foreach ($avatars as $avatar) {
            $result[] = [
                'id' => $avatar['id'],
                'name' => $avatar['name'],
                'img_url' => $avatar['img_url'],
                'type' => $avatar['type'],
                'owned' => in_array($avatar['id'], $userAvatars)
            ];
        }
        return $result;
    }

    public function switchAvatar($user_id, $avatar_id)
    {
        $userAvatar = UserAvatar::where('user_id', $user_id)->where('avatar_id', $avatar_id)->find();
        if (!$userAvatar) {
            return false;
        }

        $user = User::find($user_id);
        $user->avatar_id = $avatar_id;
        $user->update_time = time();
        $user->save();

        return true;
    }

    public function updateScore($user_id, $score)
    {
        $user = User::find($user_id);
        $user->total_score += $score;
        if ($score > $user->max_score) {
            $user->max_score = $score;
        }
        $user->update_time = time();
        $user->save();

        $this->updateRanking($user_id, $user->total_score, $user->nick_name);

        return $user;
    }

    private function updateRanking($user_id, $score, $nick_name)
    {
        $avatar = Avatar::find(User::find($user_id)->avatar_id);
        $avatarUrl = $avatar ? $avatar['img_url'] : '';

        $ranking = \app\model\game\Ranking::where('user_id', $user_id)->find();
        if ($ranking) {
            $ranking->score = $score;
            $ranking->nick_name = $nick_name;
            $ranking->avatar_url = $avatarUrl;
            $ranking->update_time = time();
            $ranking->save();
        } else {
            $ranking = new \app\model\game\Ranking();
            $ranking->user_id = $user_id;
            $ranking->score = $score;
            $ranking->nick_name = $nick_name;
            $ranking->avatar_url = $avatarUrl;
            $ranking->update_time = time();
            $ranking->save();
        }
    }
}