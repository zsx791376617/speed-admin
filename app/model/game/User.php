<?php

namespace app\model\game;

use core\base\BaseModel;

class User extends BaseModel
{
    protected $table = 'speed_member_user';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    public function getAvatarUrlAttr($value, $data)
    {
        if ($data['avatar_id'] > 0) {
            $avatar = Avatar::find($data['avatar_id']);
            return $avatar ? $avatar['img_url'] : $data['wx_avatar'];
        }
        return $data['wx_avatar'];
    }

    public static function getByUnionId(string $union_id, int $platform)
    {
        return self::where('union_id', $union_id)->where('platform', $platform)->find();
    }
}