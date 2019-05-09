<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Приложения AniLibria.TV';
$var['page'] = 'app';

$version = $var['app_version'];
$updateSrc = file_get_contents($_SERVER['DOCUMENT_ROOT']."/private/app_updates/version_$version.txt");
$updateJson = json_decode($updateSrc, true);
$versionName = $updateJson['update']['version_name'];
$appLink = $updateJson['update']['links'][0]['url'];

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>


<style>
.app-body {
    margin-top: 10px;
}

.day {
    background:rgba(0,0,0, 0.7);
    text-align: center;
    font-size:20px;
    margin: 10px 0 10px 0;
    height: 30px;
    line-height	: 30px;
    border-radius: 4px;
    color: white;
}

.app_heading {
    background:rgba(0,0,0, 0.7);
    text-align: center;
    font-size:20px;
    margin: 10px 0 0 0;
    height: 40px;
    line-height	: 40px;
    border-radius: 4px;
    color: white;
}

a#join-team-link, a#join-team-link:visited {
	font-size:13pt;
	font-family: 'PT Sans', sans-serif;
	font-weight:400;
	display:inline-block;
	background-color:#f04646;
	padding:10px;
	color:#FFF;
	text-decoration:none;
	border-radius: 4px;
}

.andriodLogo {
	width: 200px;
	height: 200px;
	position: absolute;
	background: url(/img/android.png) no-repeat;
	background-size: cover;
}

.winLogo {
    width: 150px;
    height: 150px;
    margin-left: 20px;
    position: absolute;
    background: url(/img/Win10logo.png) no-repeat;
    background-size: cover;
}

.spoiler {
    cursor: pointer;
}

.spoiler-content {
    display: none;
}

.spoiler-content.opened {
    display: block;
}
</style>


<div class="news-block">
    <div class="app-body">
        <div class="app_heading spoiler">Приложение для Android (версия <?php echo $versionName; ?>)</div>
        <div class="spoiler-content">
            <div style="height: 200px; padding-top: 10px;">
                <div class="andriodLogo"></div>
                <div style="float:right; width: 640px;">
                    <p style=" text-align: right; ">
                        На данный момент (версия 2.0) функционал приложения: просмотр онлайн с возможностью выбрать SD и HD качество (для большинства экранов телефонов будет достаточно качества SD), скачивание торрент.файлов, поиск по жанрам, избранное, просмотр новостей и блогов, просмотр комментариев<br>
                        -2.0.1: Исправлен баг с проблемами авторизации через ВК
                    </p>
                </div>

                <div class="clear"></div>

                <p style="text-align: right;">
                    <a id="join-team-link" href="<?php echo $appLink; ?>">Скачать .apk файл</a>
                </p>
            </div>
            <div class="day">Инструкция по установке</div>
            <p style="text-align: left;">
                - Скачайте .apk файл, найдите его в папке "downloads" и запустите.<br>
                - Вы увидите окно с надписью "Установка заблокирована", не пугайтесь, нажмите кнопку "настройки".<br>
                - Найдите пункт "Неизвестные источники" и выставьте параметр "разрешить".<br>
                - Выберите галочку "Разрешить только эту установку" и нажмите "ок".<br>
                - Нажмите установить, когда появится надпись "приложение установлено", нажмите "открыть".
            </p>
            <div class="day">Скриншоты</div>
            <p style="text-align: center;">
                <img src="/img/001app.jpg" width="230" height="410">&nbsp;
                <img src="/img/002app.jpg" width="230" height="410">&nbsp;
                <img src="/img/003app.jpg" width="230" height="410">
            </p>
            <span style="text-align: center;">Внимание! Приложение работает на Android от версии 4.4. На более старых версиях приложение работать не будет!</span>
        </div>

        <div class="app_heading spoiler">Приложение для Windows 10</div>
        <div class="spoiler-content">
            <div style="height: 150px; padding-top: 10px;">
                <div class="winLogo"></div>
                <div style="float:right; width: 640px; padding-bottom: 60px;">
                    <p style=" text-align: right; ">
                        Поиск по каталогу релизов, просмотр комментариев, видеоплеер для просмотра онлайн контента а также возможность авторизации под своим аккаунтом с веб-сайта и синхронизации избранного.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="day">Инструкция по установке</div>
            <p style="text-align: left;">
                - Наиболее простой и предпочтительный способ установки через Windows Store. Для этого нажмите на кнопку снизу.<br/>
                - Но также существуют и альтернативные способы установки приложения без участия Windows Store, <a href="https://anilibria.github.io/anilibria-win/">GitHub</a>.<br/>
            </p>
            <p style="text-align: center;">
                <a id="join-team-link" target="_blank" href="//www.microsoft.com/store/apps/9n1zg939ctg5?cid=storebadge&ocid=badge">Скачать в Microsoft</a>
            </p>
            <div>
                <div class="day spoiler">Скриншоты <small>(спойлер)</small></div>
                <p class="spoiler-content" style="text-align: center;">
                    <img src="/img/WinApp003.jpg" width="840" height="505">
                    <img src="/img/WinApp002.jpg" width="840" height="505">
                </p>
                <p style="text-align: center;">
                    <img src="/img/WinApp001.jpg" width="840" height="505">
                </p>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div style="margin-top:10px;"></div>
</div>

<div id="vk_comments" style="margin-top: 10px;"></div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
