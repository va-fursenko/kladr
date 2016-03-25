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

            <table id="userDataTable" class="table table-condensed">
                <caption>Weekly incoming per channel ESCC: Russia</caption>
                <thead>
                    <th>&nbsp;</th>
                    <th>SG/TL<br/>this week</th>
                    <th>Applications<br/>this week</th>
                    <th>SG/TL<br/>this week</th>
                    <th>Applications<br/>this week</th>
                </thead>
                <tbody>
                </tbody>
            </table>

            <pre id="logPre" class='log-container'></pre>
        </div>

        <div class="row notice-row">
            <span>
                <sup class="text-danger">*</sup>Необходимо дать права записи на корень проекта.<br/>
                &nbsp;Входные данные ожидаются там же, в виде DBF-файла,<br/>
                &nbsp;как он предоставляется на официальном сайте;<br/>
                &nbsp;Имя по умолчанию задаётся в классе KladrReader::FILENAME.
            </span>
        </div>

    </div>



</body>
</html>
