SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `idt` int(11) DEFAULT NULL,
  `session` varchar(32) DEFAULT NULL,
  `message` varchar(2048) DEFAULT NULL,
  `time` varchar(32) DEFAULT NULL,
  `send` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `chat_sessions` (
  `id` int(11) NOT NULL,
  `sess` varchar(128) DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  `sex` int(11) DEFAULT NULL,
  `search` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `ping` int(11) NOT NULL DEFAULT 0,
  `enter` varchar(128) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `chat_talks` (
  `idt` int(11) NOT NULL,
  `one` varchar(32) DEFAULT NULL,
  `two` varchar(32) DEFAULT NULL,
  `start` varchar(128) DEFAULT NULL,
  `end` varchar(128) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `rid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `genre` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `log_ip` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `time` bigint(20) NOT NULL,
  `info` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `time` bigint(20) NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `info` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `mail` varchar(254) DEFAULT NULL,
  `passwd` varchar(255) NOT NULL,
  `avatar` varchar(32) DEFAULT NULL,
  `2fa` varchar(16) DEFAULT NULL,
  `access` int(1) NOT NULL DEFAULT 1,
  `user_values` varchar(1024) DEFAULT NULL,
  `register_date` bigint(20) DEFAULT NULL,
  `last_activity` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_config` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_files` (
  `fid` int(11) NOT NULL,
  `info_hash` binary(20) NOT NULL,
  `leechers` int(11) NOT NULL DEFAULT 0,
  `seeders` int(11) NOT NULL DEFAULT 0,
  `completed` int(11) NOT NULL DEFAULT 0,
  `flags` int(11) NOT NULL DEFAULT 0,
  `mtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `info` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_files_users` (
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `announced` int(11) NOT NULL,
  `completed` int(11) NOT NULL,
  `downloaded` bigint(20) UNSIGNED NOT NULL,
  `left` bigint(20) UNSIGNED NOT NULL,
  `uploaded` bigint(20) UNSIGNED NOT NULL,
  `mtime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xbt_users` (
  `uid` int(11) NOT NULL,
  `torrent_pass_version` int(11) NOT NULL DEFAULT 0,
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xrelease` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ename` varchar(255) NOT NULL,
  `year` int(11) NOT NULL DEFAULT 2018,
  `type` varchar(255) NOT NULL,
  `genre` varchar(255) NOT NULL,
  `voice` varchar(255) NOT NULL,
  `other` varchar(1024) NOT NULL,
  `announce` varchar(128) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 3,
  `search_status` varchar(16) NOT NULL,
  `moonplayer` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `last` bigint(20) NOT NULL DEFAULT 0,
  `day` int(1) NOT NULL DEFAULT 1,
  `rating` int(11) NOT NULL DEFAULT 0,
  `code` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `youtube` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `vid` varchar(128) NOT NULL,
  `view` int(11) NOT NULL DEFAULT 0,
  `comment` int(11) NOT NULL DEFAULT 0,
  `hash` varchar(32) NOT NULL,
  `time` bigint(20) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idt` (`idt`),
  ADD KEY `session` (`session`),
  ADD KEY `send` (`send`);

ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sess` (`sess`),
  ADD KEY `ip` (`ip`),
  ADD KEY `sex` (`sex`),
  ADD KEY `search` (`search`);

ALTER TABLE `chat_talks`
  ADD PRIMARY KEY (`idt`),
  ADD KEY `status` (`status`);

ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `rid` (`rid`);

ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `log_ip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `hash` (`hash`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `mail` (`mail`);

ALTER TABLE `xbt_files`
  ADD PRIMARY KEY (`fid`),
  ADD UNIQUE KEY `info_hash` (`info_hash`),
  ADD KEY `rid` (`rid`);

ALTER TABLE `xbt_files_users`
  ADD UNIQUE KEY `fid` (`fid`,`uid`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `xbt_users`
  ADD PRIMARY KEY (`uid`);

ALTER TABLE `xrelease`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `last` (`last`),
  ADD KEY `day` (`day`),
  ADD KEY `status` (`status`);
ALTER TABLE `xrelease` ADD FULLTEXT KEY `name` (`name`,`ename`,`search_status`);

ALTER TABLE `youtube`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vid` (`vid`),
  ADD KEY `type` (`type`),
  ADD KEY `time` (`time`);


ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chat_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chat_talks`
  MODIFY `idt` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `log_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `xbt_files`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `xbt_users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `xrelease`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `youtube`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
