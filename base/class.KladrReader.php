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
    const FILENAME = 'KLADR.csv';


    /**
     * @const Порция строк, считываемая за раз
     */
    const ROWS_PER_STEP = 10000;


    /**
     * Импорт файла
     * @param string $filename = self::FILENAME Имя файла БД
     * @param int $filePos Смещение в файле, с которого начинается чтение
     * @param int $rowCount Счётчик считанных файлов
     * @return string
     */
    public static function readBlock($filename = self::FILENAME, $filePos = 0, $rowCount = 0)
    {
        if (!is_readable(CONFIG::ROOT . DIRECTORY_SEPARATOR . $filename)) {
            return self::error("Невозможно открыть файл БД: $filename");
        }
        $file = fopen(CONFIG::ROOT . DIRECTORY_SEPARATOR . $filename, 'r');
        $result = '';

        // Пропускаем первую строку, или устанавливаем курсор в последнюю обработанную позицию и читаем первый ряд
        if ($filePos > 0) {
            fseek($file, $filePos);
        } else {
            // Пропускаем первый ряд
            fgetcsv($file, 200, ';');
        }
        $row = fgetcsv($file, 200, ';');

        $rowCounter = 1;
        // Читаем не более ROWS_PER_STEP строк
        while (is_array($row) && count($row) > 1 && $rowCounter < self::ROWS_PER_STEP) {

                $result .=
                    str_pad($row[0], 41) .
                    str_pad($row[1], 11) .
                    str_pad($row[2], 14) .
                    str_pad($row[3], 7) .
                    str_pad($row[4], 5) .
                    str_pad($row[5], 5) .
                    str_pad($row[6], 12) .
                    str_pad($row[7], 12) .
                    "\n";

            $rowCounter++;
            $row = fgetcsv($file, 200, ';');
        }

        $result = [
            'success'   => true,
            'file_pos'  => ftell($file),
            'row_count' => $rowCount + $rowCounter,
            'next_step' => feof($file) ? 'end' : 'step',
        ];
        fclose($file);
        return $result;
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
