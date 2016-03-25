<?php
/**
 * Класс импорта данных КЛАДР из родного файла KLADR.DBF
 * @version 1.0
 * @php     >=5.2
 * @author  User: viktor
 * @date    25.03.16
 */


/**
 * Класс импорта данных КЛАДР из родного файла KLADR.DBF
 */
class KLADRReader
{

    /**
     * @const Файл с таблицей входных данных
     */
    const FILENAME = 'KLADR.DBF';


    /**
     * @const Порция строк, считываемая за раз
     */
    const ROWS_PER_STEP = 10000;


    /**
     * Импорт файла
     * @param string $filename = self::FILENAME Имя файла БД
     * @return string
     */
    public static function openFile($filename = self::FILENAME)
    {
        $db = dbase_open(__DIR__ . DIRECTORY_SEPARATOR . $filename, 0);
    }


}
