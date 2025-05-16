<?php
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/command.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/dict.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/nav.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/gr.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/user2gr.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/user.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/red_button.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/db_content/red_button_conf.sql';
$sql[] = 'file: version/' . HROSE_VERSION . '/scripts/999_auto_increment.sql';
$sql[] = 'ALTER TABLE `user` DROP `autologin`;';
$sql[] = 'ALTER TABLE `user` DROP `uuid`;';
$sql[] = 'ALTER TABLE `user` DROP `email_active`;';
