<?php 
	require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'config.php');
	require_once(CONFIG::ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'class.Log.php');

    if (isset($_GET['clear'])){
        fclose(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'error.log', 'w'));
        fclose(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'db.error.log', 'w'));
        fclose(fopen(__DIR__ . DIRECTORY_SEPARATOR . 'db.log', 'w'));
        header('Location: http://' . $_SERVER['SERVER_NAME'] . '/log/');
    }
?>
<!DOCTYPE html>
<html>

    <head>
        <title><?= CONFIG::PAGE_TITLE ?>&nbsp;/log/</title>
        <meta charset="<?= CONFIG::PAGE_CHARSET ?>">
        <script src="<?= CONFIG::HOST ?>js/jquery.js"></script>
        <script src="<?= CONFIG::HOST ?>js/bootstrap.js"></script>
        <script src="<?= CONFIG::HOST ?>js/log.js"></script>
        <link rel="stylesheet" href="<?= CONFIG::HOST ?>css/bootstrap.css">
        <link rel="stylesheet" href="<?= CONFIG::HOST ?>css/log.css">
    </head>

    <body>

        <div id="logDiv">
            <div class="tab-header">
                <ul class="nav nav-pills" role="tablist">
                    <li class="active">
                        <a href="#e0" aria-controls="e1" role="tab" data-toggle="tab">Ошибки PHP</a>
                    </li>
                    <li>
                        <a href="#e1" aria-controls="e1" role="tab" data-toggle="tab">Ошибки БД</a>
                    </li>
                    <li>
                        <a href="#e2" aria-controls="e2" role="tab" data-toggle="tab">Запросы БД</a>
                    </li>
                </ul>

                <div class="btn-container">
                    <a class="btn btn-danger" href="/log/index.php?clear">Очистить все логи</a>
                </div>
            </div>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade active in" id="e0">
                    <?= Log::showLogFile('error.log') ?>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="e1">
                    <?= Log::showLogFile('db.error.log') ?>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="e2">
                    <?= Log::showLogFile('db.log') ?>
                </div>
            </div>
        </div>
	
	<div id="scrollup" class="scrollup"><img alt="Прокрутить вверх" class="scrollup" src="<?= CONFIG::HOST ?>/img/up.png"></div>

    </body>
</html>


