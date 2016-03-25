<!DOCTYPE html>
<html>

<head>
    <title><?= CONFIG::PAGE_TITLE ?></title>
    <meta charset="<?= CONFIG::PAGE_CHARSET ?>">
    <!-- js -->
    <script src="<?= CONFIG::HOST ?>/js/jquery.js"></script>
    <script src="<?= CONFIG::HOST ?>/js/bootstrap.js"></script>
    <script src="<?= CONFIG::HOST ?>/js/common.js"></script>
    <!-- css -->
    <link rel="stylesheet" href="<?= CONFIG::HOST ?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?= CONFIG::HOST ?>/css/common.css">
</head>

<body>

    <div class="container" style="margin-top: 100px;">
        <div class="row log-row">
            <h4 class="log-caption">Импорт данных из КЛАДР</h4>
            <img class="log-caption log-loader" id="logLoader" src="img/loader.gif">
            <a id="beginBtn" class="log-caption btn btn-primary" href="javascript:void(0);">Получить данные</a>
            <pre id="logPre" class='log-container'></pre>
        </div>

        <div class="row notice-row">
            <sup class="text-danger">*</sup>
            <ul>
                <li>Необходимо дать права записи на корень проекта</li>
                <li>Входные данные ожидаются там же, в виде CSV-файла,</li>
                <li>созданного из оригинального DBF</li>
                <li>Разделители ;</li>
                <li>Строкикак экранируются "</li>
                <li>Имя по умолчанию задаётся в конфиге</li>
            </ul>
        </div>

    </div>



</body>
</html>
