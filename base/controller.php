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
require_once('..' . DIRECTORY_SEPARATOR . 'config.php');

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

            $result = KLADRReader::readBlock(
                CONFIG::BASE_FILENAME,
                isset($_GET['file_pos']) ? $_GET['file_pos'] : 0,
                isset($_GET['row_count']) ? $_GET['row_count'] : 0
            );
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

