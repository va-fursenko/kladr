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
 */
function nextStep(act) {
    $.ajax({
        type: 'GET',
        url: '/base/controller.php?action=' + act,
        dataType: 'json',
        timeout: 240000,
        success: function (response) {
            if (response.success) {
                log(response.message == ''
                    ? "Завершено"
                    : "Завершено. " + response.message
                );

                // Если есть следующий этап, выполняем его
                if (typeof response.nextStep !== 'undefined') {
                    log('');
                    log(response.nextMessage);
                    nextStep('step', response.nextStep);

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
        nextStep('step', 0);
    })
});