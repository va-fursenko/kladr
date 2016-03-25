<?php
/**
 * Поэтапное получение отчёта
 * User: viktor
 * Date: 09.03.16
 * Time: 10:07
 */

if (!isset($_GET['action'])) {
    exit("Прощай, со всех вокзалов поезда уходят в дальние края. Прощай, мы расстаёмся навсегда под белым небом янваааааряяя!...");
}
$act = $_GET['action'];


/* Конфиг */
require_once(__DIR__ . '/config.php');

/* Бэйс либс */
require_once(CONFIG::ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'class.BaseException.php');
require_once(CONFIG::ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'class.Filter.php');
require_once(CONFIG::ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'class.Log.php');
require_once(CONFIG::ROOT . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'class.ErrorHandler.php');

/* Воркин модуле */
require_once(CONFIG::ROOT . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'class.KladrReader.php');


try {

    switch ($act) {


        // Выполнение однога шага импорта
        case 'step':

            $result = KLADRReader::openFile();

            if ($result['success']) {
                $result['nextStep'] = 'openSecond';
                $result['nextMessage'] = "# Импорт узкой матрицы";
            }
            break;


        // O_o
        default:
            $result = [
                'success' => false,
                'message' => "Неизвестная команда $act",
            ];
    }


// А что, а вдруг?
} catch (Exception $e) {
    Log::save(Log::dumpException($e), CONFIG::ERROR_LOG_FILE);
    $result = [
        'success' => false,
        'message' => $e->getMessage(),
    ];
}


echo json_encode($result);

