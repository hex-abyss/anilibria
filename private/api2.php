<?php

require '/var/www/html/private/vendor/AltoRouter/AltoRouter.php';

require '/var/www/html/private/config.php';
require '/var/www/html/private/init/mysql.php';
require '/var/www/html/private/init/var.php';
require '/var/www/html/private/func.php';

$router = new AltoRouter();
$router->setBasePath('/api2');

header('Content-Type: application/json; charset=utf-8');

// getReleases
$router->map('GET', '/getReleases', function () {
    return _getFullReleasesDataInLegacyStructure();
});

// getTitleByID
$router->map('GET', '/getTitleByID/[:releaseId]', function ($releaseId) {
    return _getReleaseByColumn('id', $releaseId);
});

// getTitleByCode
$router->map('GET', '/getTitleByCode/[:releaseAlias]', function ($releaseAlias) {
    return _getReleaseByColumn('alias', $releaseAlias);
});

// getTitleByTorrentID
$router->map('GET', '/getTitleByTorrentID/[:torrentId]', function ($torrentId) {

    global $db;
    $query = $db->prepare('
        SELECT
           t.`releases_id` as `rid` 
        FROM `torrents` as t
        INNER JOIN `releases` as r ON r.id = t.releases_id 
        WHERE r.`is_hidden` = 0 AND r.`deleted_at` IS NULL AND t.`deleted_at` IS NULL AND t.`id` = :torrentId
    ');
    $query->bindParam('torrentId', $torrentId);
    $query->execute();

    $torrent = $query->fetch(PDO::FETCH_ASSOC);

    return $torrent ? ['rid' => (int)$torrent['rid']] : null;

});

// IsReleaseExists
$router->map('GET', '/IsReleaseExists/[:releaseId]', function ($releaseId) {

    $release = _getReleaseByColumn('id', $releaseId);

    return [
        'is_exists' => is_null($release) === false,
        'releases_id' => $releaseId ? (int)$releaseId : null,
    ];
});

// getTitlesByLastUpdate
$router->map('GET', '/getTitlesByLastUpdate/[i:limit]', function ($limit) {

    global $db;

    $query = $db->prepare(
        sprintf("
            SELECT 
                `id`, 
                UNIX_TIMESTAMP(fresh_at) as `last`, 
                IF(`is_hidden` = 1, 3, IF(`is_ongoing` = 1, 1, IF(`is_completed` = 1, 2, 0))) AS `status`
            
            FROM `releases` 
            WHERE `is_hidden` = 0 AND `deleted_at` IS NULL
            ORDER BY `fresh_at` DESC
            LIMIT %s
        ", $limit && (int)$limit > 0 ? (int)$limit : 999999999999)
    );

    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($releases as $index => $release) {
        $releases[$index] = [
            'id' => (int)$release['id'],
            'last' => (int)$release['last'],
            'status' => (int)$release['status']
        ];
    }

    return $releases;

});

// getTitlesByLastChange
$router->map('GET', '/getTitlesByLastChange/[i:limit]', function ($limit) {

    global $db;

    $query = $db->prepare(
        sprintf("
            SELECT 
                `id`, 
                UNIX_TIMESTAMP(updated_at) as `last_change`, 
                IF(`is_hidden` = 1, 3, IF(`is_ongoing` = 1, 1, IF(`is_completed` = 1, 2, 0))) AS `status`
            
            FROM `releases` 
            WHERE `is_hidden` = 0 AND `deleted_at` IS NULL
            ORDER BY `updated_at` DESC
            LIMIT %s
        ", $limit && (int)$limit > 0 ? (int)$limit : 999999999999)
    );

    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);


    foreach ($releases as $index => $release) {
        $releases[$index] = [
            'id' => (int)$release['id'],
            'status' => (int)$release['status'],
            'last_change' => (int)$release['last_change'],
        ];
    }

    return $releases;

});

// getTorrents
$router->map('GET', '/getTorrents/[:releaseId]', function ($releaseId) {
    global $db;
    $query = $db->prepare('
          SELECT 
             t.`id` AS `fid`,
             tf.`leechers`, 
             tf.`seeders`,
             t.`completed_times` as `completed`,
             0 AS `flags`,
             UNIX_TIMESTAMP(t.`fresh_at`) AS `mtime`,
             UNIX_TIMESTAMP(t.`created_at`) AS `ctime`,
             JSON_ARRAY(CONCAT_WS(" ", t.`type`, t.`quality`, IF(t.`is_hevc` = 1, "HEVC", null)), t.`description`, tf.`size`) as `info`
          
          FROM `torrents` AS t
          INNER JOIN `releases` AS r ON r.`id` = t.`releases_id` AND r.`is_hidden` = 0 AND r.`deleted_at` IS NULL
          INNER JOIN `torrents_files` as tf on tf.id = (select `id` from `torrents_files` where `torrents_id` = t.`id` and `deleted_at` IS NULL ORDER BY `created_at` DESC LIMIT 1)
          WHERE r.`id` = :releaseId
          GROUP BY t.`id`
          ORDER BY t.`created_at` ASC
    ');

    $query->bindParam('releaseId', $releaseId);
    $query->execute();

    $torrents = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($torrents as $index => $torrent) {
        $torrents[$index] = array_merge($torrent, [
            'fid' => (int)$torrent['fid'],
            'seeders' => (int)$torrent['seeders'],
            'leechers' => (int)$torrent['leechers'],
            'completed' => (int)$torrent['completed'],
            'flags' => (int)$torrent['flags'],
            'mtime' => (int)$torrent['mtime'],
            'ctime' => (int)$torrent['ctime'],
        ]);
    }

    return $torrents;

});

// getYouTube
$router->map('GET', '/getYouTube/[i:limit]', function ($limit) {

    global $db;

    $limit = $limit && (int)$limit > 0
        ? (int)$limit
        : 999999999999;

    $query = $db->prepare('
        SELECT 
            `id`, 
           `title`,
           `youtube_id` as `vid`,
           UNIX_TIMESTAMP(`created_at`) as `time`,
           `views` as `view`,
           `comments` as `comment`
        FROM `youtube`
        WHERE `deleted_at` IS NULL
        ORDER BY `created_at` DESC
        LIMIT ' . $limit);

    $query->execute();
    $youtube = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($youtube as $index => $video) {
        $youtube[$index] = array_merge($video, [
            'id' => (int)$video['id'],
            'time' => (int)$video['time'],
            'view' => (int)$video['view'],
            'comment' => (int)$video['comment'],
        ]);
    }

    return $youtube;

});

// getGenres
$router->map('GET', '/getGenres', function () {
    global $db;
    $query = $db->prepare('SELECT `id`, `name`, 0 as `rating` FROM `genres` ORDER BY `name`');
    $query->execute();
    $genres = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($genres as $index => $genre) {
        $genres[$index] = array_merge($genre, [
            'id' => (int)$genre['id'],
            'rating' => (int)$genre['rating']
        ]);
    }

    return $genres;

});

// getYears
$router->map('GET', '/getYears', function () {
    global $db;
    $query = $db->prepare('SELECT `year` FROM `releases` WHERE `year` > 0 AND `is_hidden` = 0 AND `deleted_at` IS NULL GROUP BY `year`');
    $query->execute();
    $years = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($years as $index => $year) {
        $years[$index] = array_merge($year, [
            'year' => (int)$year['year']
        ]);
    }

    return $years;

});

// getSchedule
$router->map('GET', '/getSchedule/[:day]', function ($day) {
    global $db;
    $query = $db->prepare('
        SELECT 
            id, 
            IF(is_hidden = 1, 3, IF(is_ongoing = 1, 1, IF(is_completed = 1, 2, 0))) AS `status` 
        FROM `releases` 
        WHERE `is_ongoing` = 1 AND `publish_day` = :day AND `is_hidden` = 0 AND `deleted_at` IS NULL
    ');
    $query->bindParam('day', $day);
    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($releases as $index => $release) {
        $releases[$index] = array_merge($release, [
            'id' => (int)$release['id'],
            'status' => (int)$release['status']
        ]);
    }

    return $releases;

});

// getTorrentSeedStats
$router->map('GET', '/getTorrentSeedStats/[i:limit]', function ($limit) {

    $limit = $limit && (int)$limit > 0 ? (int)$limit : 999999999999;

    global $db;
    $query = $db->prepare(
        sprintf('
            SELECT 
               `torrents_downloaded` as `downloaded`,
               `torrents_uploaded` as `uploaded`,
               `login` 
            FROM `users`
            WHERE `torrents_uploaded` IS NOT NULL
            ORDER BY `torrents_uploaded` DESC
            %s',
            "LIMIT " . $limit
        )
    );

    $query->execute();
    $torrents = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($torrents as $index => $torrent) {
        $torrents[$index] = array_merge($torrent, [
            'uploaded' => (int)$torrent['uploaded'],
            'downloaded' => (int)$torrent['downloaded'],
        ]);
    }

    return $torrents;

});

// getUserIdBySession
$router->map('GET', '/getUserIdBySession/[:sessionId]', function ($sessionId) {
    global $db;
    $query = $db->prepare('SELECT `users_id` as `uid` FROM `users_sessions` WHERE `id` = :sessionId');
    $query->bindParam('sessionId', $sessionId);
    $query->execute();
    $session = $query->fetch(PDO::FETCH_ASSOC);

    return $session ? ['uid' => (int)$session['uid']] : null;

});

// getUserFavorites
$router->map('GET', '/getUserFavorites/[:userId]', function ($userId) {
    global $db;
    $query = $db->prepare('SELECT `releases_id` as `rid` FROM `users_favorites` WHERE `users_id` = :userId');
    $query->bindParam('userId', $userId);
    $query->execute();
    $favorites = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($favorites as $index => $favorite) {
        $favorites[$index] = array_merge($favorite, [
            'rid' => (int)$favorite['rid']
        ]);
    }

    return $favorites ?? [];
});

// addUserFavorite
$router->map('GET', '/addUserFavorite/[:userId]/[:releaseId]', function ($userId, $releaseId) {
    global $db;
    $query = $db->prepare('INSERT INTO `users_favorites`  (`id`, `users_id`, `releases_id`, `created_at`, `updated_at`) VALUES (UUID(), :userId, :releaseId, NOW(), NOW())');
    return $query->execute(['userId' => $userId, 'releaseId' => $releaseId]);
});

// delUserFavorite
$router->map('GET', '/delUserFavorite/[:userId]/[:releaseId]', function ($userId, $releaseId) {
    global $db;
    $query = $db->prepare('DELETE FROM `users_favorites` WHERE `users_id` = :userId and `releases_id` = :releaseId');
    return $query->execute(['userId' => $userId, 'releaseId' => $releaseId]);
});

// buildSearchCache
$router->map('GET', '/buildSearchCache', function () {
    global $db;
    $query = $db->prepare('SELECT `id`, `name`, `name_english` as ename, `name_alternative` as aname, `description` FROM `releases` WHERE `is_hidden` = 0 AND `deleted_at` IS NULL');
    $query->execute();
    $releases = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($releases as $index => $release) {
        $releases[$index] = array_merge($release, [
           'id' => (int)$release['id'],
        ]);
    }

    return $releases;
});


$match = $router->match();
$response = is_array($match) && is_callable($match['target']) ? call_user_func_array($match['target'], $match['params']) : null;

if (empty($response) && !is_array($response)) header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
if (empty($response) === false || is_array($response)) echo json_encode($response);

