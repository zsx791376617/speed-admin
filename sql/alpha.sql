CREATE TABLE IF NOT EXISTS `speed_member_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '用户唯一ID',
  `union_id` varchar(64) NOT NULL COMMENT '多端统一账号标识',
  `platform` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '登录平台：1=微信小程序 2=Web 3=App',
  `nick_name` varchar(32) NOT NULL COMMENT '用户昵称',
  `avatar_id` int unsigned NOT NULL DEFAULT '0' COMMENT '当前使用头像皮肤ID',
  `wx_avatar` varchar(255) DEFAULT NULL COMMENT '微信端原生头像地址',
  `total_score` int unsigned NOT NULL DEFAULT '0' COMMENT '历史总积分',
  `max_score` int unsigned NOT NULL DEFAULT '0' COMMENT '单局最高分',
  `vip_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '月卡状态：0=未开通 1=已开通',
  `vip_expire_time` int unsigned NOT NULL DEFAULT '0' COMMENT '月卡过期时间戳',
  `last_login_time` int unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间戳',
  `create_time` int unsigned NOT NULL COMMENT '创建时间戳',
  `update_time` int unsigned NOT NULL COMMENT '更新时间戳',
  `delete_time` int unsigned NOT NULL DEFAULT '0' COMMENT '软删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `union_id` (`union_id`),
  KEY `platform` (`platform`),
  KEY `vip_status` (`vip_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户主表';

CREATE TABLE IF NOT EXISTS `speed_avatar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '皮肤ID',
  `name` varchar(32) NOT NULL COMMENT '皮肤名称',
  `img_url` varchar(255) NOT NULL COMMENT '素材地址（COS链接）',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1=基础 2=限定(月卡) 3=成就奖励',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序权重',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '上下架：0=下架 1=上架',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='头像皮肤表';

CREATE TABLE IF NOT EXISTS `speed_user_avatar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned NOT NULL COMMENT '关联用户ID',
  `avatar_id` int unsigned NOT NULL COMMENT '关联皮肤ID',
  `get_time` int unsigned NOT NULL COMMENT '获取时间戳',
  `source` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '获取来源：1=初始 2=月卡 3=成就 4=活动',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_avatar` (`user_id`,`avatar_id`),
  KEY `user_id` (`user_id`),
  KEY `avatar_id` (`avatar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户头像持有表';

CREATE TABLE IF NOT EXISTS `speed_level` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '关卡ID',
  `level_name` varchar(32) NOT NULL COMMENT '关卡名称',
  `map_img` varchar(255) NOT NULL COMMENT '地图背景图COS地址',
  `difficulty` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '难度：1-5级',
  `unlock_condition` int unsigned NOT NULL DEFAULT '0' COMMENT '解锁前置关卡ID',
  `game_time` int unsigned NOT NULL DEFAULT '120' COMMENT '单局限时(秒)',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '关卡状态：0=禁用 1=正常',
  PRIMARY KEY (`id`),
  KEY `difficulty` (`difficulty`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='游戏关卡表';

CREATE TABLE IF NOT EXISTS `speed_user_level` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `level_id` int unsigned NOT NULL COMMENT '关卡ID',
  `star` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '星级：0-3星',
  `pass_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0=未通关 1=已通关',
  `best_score` int unsigned NOT NULL DEFAULT '0' COMMENT '该关卡最高分',
  `play_times` int unsigned NOT NULL DEFAULT '0' COMMENT '游玩次数',
  `last_play_time` int unsigned NOT NULL DEFAULT '0' COMMENT '最后游玩时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_level` (`user_id`,`level_id`),
  KEY `user_id` (`user_id`),
  KEY `level_id` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户关卡进度表';

CREATE TABLE IF NOT EXISTS `speed_item` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '道具ID',
  `item_name` varchar(32) NOT NULL COMMENT '道具名称',
  `icon_url` varchar(255) NOT NULL COMMENT '道具图标',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1=提示 2=放大镜 3=时长延长',
  `describe` varchar(128) DEFAULT NULL COMMENT '道具描述',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='道具配置表';

CREATE TABLE IF NOT EXISTS `speed_user_item` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `item_id` int unsigned NOT NULL COMMENT '道具ID',
  `num` int unsigned NOT NULL DEFAULT '0' COMMENT '剩余数量',
  `update_time` int unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_item` (`user_id`,`item_id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户道具库存表';

CREATE TABLE IF NOT EXISTS `speed_user_stamina` (
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `stamina` int unsigned NOT NULL DEFAULT '5' COMMENT '当前体力值',
  `max_stamina` int unsigned NOT NULL DEFAULT '5' COMMENT '体力上限',
  `recover_time` int unsigned NOT NULL DEFAULT '0' COMMENT '下次体力恢复时间戳',
  `vip_unlimited` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '月卡无限体力：0=关闭 1=开启',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='体力数据表';

CREATE TABLE IF NOT EXISTS `speed_user_vip` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `vip_days` int unsigned NOT NULL DEFAULT '0' COMMENT '剩余天数',
  `expire_time` int unsigned NOT NULL DEFAULT '0' COMMENT '过期时间戳',
  `daily_gift_get` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '当日礼包是否领取：0=未领 1=已领',
  `last_get_time` int unsigned NOT NULL DEFAULT '0' COMMENT '上次领礼包日期戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `expire_time` (`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='月卡记录表';

CREATE TABLE IF NOT EXISTS `speed_user_minigame` (
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `game_type` tinyint unsigned NOT NULL COMMENT '1=叠叠乐 2=拼图',
  `difficulty` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '拼图难度：1=3×3 2=4×4 3=5×5',
  `best_score` int unsigned NOT NULL DEFAULT '0' COMMENT '最高分',
  `play_times` int unsigned NOT NULL DEFAULT '0' COMMENT '游玩次数',
  `progress` varchar(512) DEFAULT NULL COMMENT '拼图进度缓存（字符串）',
  PRIMARY KEY (`user_id`,`game_type`),
  KEY `game_type` (`game_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='迷你游戏数据表';

CREATE TABLE IF NOT EXISTS `speed_achievement` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '成就ID',
  `name` varchar(32) NOT NULL COMMENT '成就名称',
  `icon` varchar(255) NOT NULL COMMENT '成就图标',
  `condition` varchar(255) NOT NULL COMMENT '解锁条件（规则描述）',
  `reward_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '奖励类型：1=道具 2=头像 3=积分',
  `reward_id` int unsigned NOT NULL DEFAULT '0' COMMENT '奖励关联ID',
  `reward_num` int unsigned NOT NULL DEFAULT '0' COMMENT '奖励数量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='成就表';

CREATE TABLE IF NOT EXISTS `speed_user_achievement` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `achievement_id` int unsigned NOT NULL COMMENT '成就ID',
  `get_time` int unsigned NOT NULL COMMENT '解锁时间',
  `reward_get` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '奖励是否领取',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_achievement` (`user_id`,`achievement_id`),
  KEY `user_id` (`user_id`),
  KEY `achievement_id` (`achievement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户成就表';

CREATE TABLE IF NOT EXISTS `speed_collection` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '藏品ID',
  `name` varchar(32) NOT NULL COMMENT '藏品名称',
  `icon` varchar(255) NOT NULL COMMENT '藏品图标',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1=形象 2=场景',
  `level_id` int unsigned NOT NULL DEFAULT '0' COMMENT '关联关卡ID',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `level_id` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='藏品表';

CREATE TABLE IF NOT EXISTS `speed_user_collection` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `collection_id` int unsigned NOT NULL COMMENT '藏品ID',
  `get_time` int unsigned NOT NULL COMMENT '获取时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_collection` (`user_id`,`collection_id`),
  KEY `user_id` (`user_id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户藏品表';

CREATE TABLE IF NOT EXISTS `speed_share` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `share_code` varchar(32) NOT NULL COMMENT '唯一分享短码',
  `share_url` varchar(255) NOT NULL COMMENT '全局分享链接',
  `user_id` bigint unsigned NOT NULL COMMENT '分享人ID',
  `level_id` int unsigned NOT NULL DEFAULT '0' COMMENT '分享关卡ID',
  `create_time` int unsigned NOT NULL COMMENT '生成时间',
  `expire_time` int unsigned NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `share_code` (`share_code`),
  KEY `user_id` (`user_id`),
  KEY `level_id` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分享记录表';

CREATE TABLE IF NOT EXISTS `speed_ranking` (
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `nick_name` varchar(32) NOT NULL COMMENT '昵称',
  `avatar_url` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `score` int unsigned NOT NULL DEFAULT '0' COMMENT '排行分数',
  `update_time` int unsigned NOT NULL DEFAULT '0' COMMENT '数据更新时间',
  PRIMARY KEY (`user_id`),
  KEY `score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='排行榜临时表';

INSERT INTO `speed_avatar` (`id`, `name`, `img_url`, `type`, `sort`, `status`) VALUES
(1, '默认头像', 'https://example.com/avatar/default.png', 1, 1, 1),
(2, '探险家', 'https://example.com/avatar/explorer.png', 1, 2, 1),
(3, '月卡专属', 'https://example.com/avatar/vip.png', 2, 3, 1),
(4, '成就大师', 'https://example.com/avatar/achievement.png', 3, 4, 1);

INSERT INTO `speed_item` (`id`, `item_name`, `icon_url`, `type`, `describe`) VALUES
(1, '提示', 'https://example.com/item/hint.png', 1, '显示一个物品位置'),
(2, '放大镜', 'https://example.com/item/magnifier.png', 2, '放大当前区域'),
(3, '时长延长', 'https://example.com/item/time.png', 3, '增加游戏时间');

INSERT INTO `speed_level` (`id`, `level_name`, `map_img`, `difficulty`, `unlock_condition`, `game_time`, `status`) VALUES
(1, '新手村', 'https://example.com/map/1.png', 1, 0, 120, 1),
(2, '森林', 'https://example.com/map/2.png', 2, 1, 120, 1),
(3, '沙漠', 'https://example.com/map/3.png', 3, 2, 150, 1),
(4, '雪山', 'https://example.com/map/4.png', 4, 3, 180, 1),
(5, '城堡', 'https://example.com/map/5.png', 5, 4, 210, 1);

INSERT INTO `speed_achievement` (`id`, `name`, `icon`, `condition`, `reward_type`, `reward_id`, `reward_num`) VALUES
(1, '初出茅庐', 'https://example.com/achievement/1.png', '完成第一关', 1, 1, 3),
(2, '寻物达人', 'https://example.com/achievement/2.png', '通关所有关卡', 3, 0, 100),
(3, '拼图高手', 'https://example.com/achievement/3.png', '拼图达到500分', 2, 4, 1),
(4, '叠叠王者', 'https://example.com/achievement/4.png', '叠叠乐达到1000分', 1, 2, 5);

INSERT INTO `speed_collection` (`id`, `name`, `icon`, `type`, `level_id`) VALUES
(1, '小精灵', 'https://example.com/collection/1.png', 1, 1),
(2, '森林小屋', 'https://example.com/collection/2.png', 2, 2),
(3, '沙漠骆驼', 'https://example.com/collection/3.png', 1, 3),
(4, '雪山鹰', 'https://example.com/collection/4.png', 1, 4),
(5, '城堡守卫', 'https://example.com/collection/5.png', 1, 5);