<?php
/**
 * Класс импорта данных КЛАДР из родного файла KLADR.DBF
 * @version 1.0
 * @php     >=5.2
 * @author  User: viktor
 * @date    25.03.16
 */

require_once ('dbf.php');

/**
 * Класс импорта данных КЛАДР из родного файла KLADR.DBF
 */
class KLADRReader
{

    /**
     * @const Файл с таблицей входных данных
     */
    const FILENAME = 'KLADR.csv';


    /**
     * @const Порция строк, считываемая за раз
     */
    const ROWS_PER_STEP = 10000;


    /**
     * Импорт файла
     * @param string $filename = self::FILENAME Имя файла БД
     * @param int $filePos Смещение в файле, с которого начинается чтение
     * @return string
     */
    public static function readBlock($filename = self::FILENAME, $filePos = 0)
    {
        if (!is_readable(CONFIG::ROOT . DIRECTORY_SEPARATOR . $filename)) {
            return self::error("Невозможно открыть файл БД: $filename");
        }
        $file = fopen(CONFIG::ROOT . DIRECTORY_SEPARATOR . $filename, 'r');

        // Пропускаем первую строку, или устанавливаем курсор в последнюю обработанную позицию и читаем первый ряд
        if ($filePos > 0) {
            fseek($file, $filePos);
        }
        $row = fgetcsv($file, 100, ';', '"');

        $rowCounter = 0;
        // Читаем не более ROWS_PER_STEP строк
        while (is_array($row) && $rowCounter < self::ROWS_PER_STEP) {

            $rowCounter++;
            $row = fgetcsv($file, 100, ';', '"');
        }

        fclose($file);
        return self::success(ftell($file));
    }




    // Для красоты
    /**
     * Возврат успеха с сообщением
     * @param string $message
     * @return array
     */
    protected static function success($message)
    {
        return ['success' => true, 'message' => $message];
    }
    /**
     * Возврат ошибки с сообщением
     * @param string $message
     * @return array
     */
    protected  static function error($message)
    {
        return ['success' => false, 'message' => $message];
    }
}
