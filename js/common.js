/**
 * Yippee-ki-yay, motherfucker!
 * It's a common js
 */


/**
 * Запись одной строки в лог
 * @param message Не поверите, та самая строка
 */
function log(message) {
    if (message !== '') {
        var d = new Date();
        var t = (d.getHours() > 9 ? d.getHours() : '0' + d.getHours()) + ':' +
            (d.getMinutes() > 9 ? d.getMinutes() : '0' + d.getMinutes()) + ':' +
            (d.getSeconds() > 9 ? d.getSeconds() : '0' + d.getSeconds());
        $("#logPre").text($("#logPre").text() + '[' + t + '] ' + message + "\n");
    } else {
        $("#logPre").text($("#logPre").text() + "\n");
    }
}


/**
 * Завершение вычислений
 */
function onFinish() {
    $("#beginBtn").show();
    $("#logLoader").hide();
}


/**
 * Вывод в удобоваримой форме пользовательских данных *
 */
function showUserData(data) {
    log(data);
}


/**
 * Выполнение одного этапа задачи с логгированием результатов
 * @param act Действие для передачи в контроллер
 * @param filePos Смещение в файле, с которого продолжается чтение
 * @param rowCount Счётчик считанных строк
 */
function nextStep(act, filePos, rowCount) {
    $.ajax({
        'type'      : 'GET',
        'url'       : '/base/controller.php?action=' + act + '&file_pos=' + filePos + '&row_count=' + rowCount,
        'dataType'  : 'json',
        'timeout'   : 240000,
        success: function (response) {
            if (response.success) {
                // Выводим счётчик
                if (typeof response.row_count !== 'undefined') {
                    log("Рядов считано: " + response.row_count);
                }

                // Если есть следующий этап, выполняем его
                if (typeof response.next_step !== 'undefined' && response.next_step == 'step') {
                    nextStep('step', response.file_pos, response.row_count);

                // Прячем лоадер, показываем кнопку перезапуска и выводим удобный результат
                } else {
                    onFinish();
                    if (typeof response.userData !== 'undefined') {
                        showUserData(response.userData);
                    }
                }

            } else {
                log("# Произошла ошибка: " + response.message);
                onFinish();
            }
        },
        error: function () {
            log("# Произошла ошибка");
            onFinish();
        }
    });
}


/**
 * Действия при загрузке страницы
 */
$(window).load(function () {
    $(".notice-row").slideDown(500);

    // Старт отчёта
    $("#beginBtn").click(function () {
        $(".notice-row").slideUp();
        $("#beginBtn").hide();
        $("#logLoader").show();
        $("#logPre").text('');
        $('#logPre').show();

        log("# Импорт данных КЛАДР\n");

        // Выполняем первый шаг
        nextStep('step', 0, 0);
    })
});