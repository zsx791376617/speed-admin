<?php

namespace app\service\game;

use core\base\BaseService;
use app\model\game\Share;

class ShareService extends BaseService
{
    const SHARE_EXPIRE = 86400;

    public function createShare($user_id, $level_id = 0)
    {
        $shareCode = $this->generateUniqueCode();
        $shareUrl = $this->buildShareUrl($shareCode);
        $now = time();

        $share = new Share();
        $share->share_code = $shareCode;
        $share->share_url = $shareUrl;
        $share->user_id = $user_id;
        $share->level_id = $level_id;
        $share->create_time = $now;
        $share->expire_time = $now + self::SHARE_EXPIRE;
        $share->save();

        return [
            'share_code' => $shareCode,
            'share_url' => $shareUrl,
            'expire_time' => $share->expire_time
        ];
    }

    private function generateUniqueCode()
    {
        do {
            $code = Share::generateShareCode();
            $exists = Share::where('share_code', $code)->find();
        } while ($exists);
        return $code;
    }

    private function buildShareUrl($shareCode)
    {
        return "https://example.com/share/{$shareCode}";
    }

    public function parseShare($share_code)
    {
        $share = Share::getByShareCode($share_code);
        if (!$share) {
            return ['success' => false, 'msg' => '分享链接无效或已过期'];
        }

        return ['success' => true, 'data' => [
            'share_code' => $share['share_code'],
            'share_url' => $share['share_url'],
            'level_id' => $share['level_id'],
            'create_time' => $share['create_time']
        ]];
    }

    public function helpShare($user_id, $share_code)
    {
        $share = Share::getByShareCode($share_code);
        if (!$share) {
            return ['success' => false, 'msg' => '分享链接无效'];
        }

        $itemService = new ItemService();
        $itemService->addItem($share['user_id'], 1, 1);
        $itemService->addItem($user_id, 1, 1);

        return ['success' => true, 'msg' => '互助成功，双方各获得1个提示道具'];
    }
}