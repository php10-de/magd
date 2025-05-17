<?php
$hroseVersion = '1.0.5';

$sql[] = 'ALTER TABLE `ai` ADD `briefing` TEXT NULL AFTER `init_cmd`';
$sql[] = 'ALTER TABLE `ai` ADD `active` tinyint(1) UNSIGNED NOT NULL AFTER `briefing`';
$sql[] = 'ALTER TABLE `ai` ADD `dupdate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() AFTER `active`';
$sql[] = 'ALTER TABLE `chat_history` ADD `cdate` DATETIME NOT NULL AFTER `action`';
