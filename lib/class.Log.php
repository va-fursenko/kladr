<?php
/**
 * Log explorer сlass (PHP 5 >= 5.0.0)
 * Special thanks to: all, http://www.php.net
 * Copyright (c)    viktor Belgorod, 2009-2016
 * Email            vinjoy@bk.ru
 * Version            2.4.0
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the MIT License (MIT)
 * @see https://opensource.org/licenses/MIT
 */


/** Собственное исключение для класса */
class LogException extends BaseException
{
    # Языковые константы класса
    const L_LOG_FILE_UNREADABLE = 'Файл лога недоступен для чтения';
    const L_LOG_FILE_UNWRITABLE = 'Файл лога недоступен для записи';
}

/** @todo Реализовать работу с логами через БД */
/** @todo Добавить скрипт создания таблицы логов */
/** @todo Создать файл лога при его отсутствии. ДАВНО ПОРА БЫЛО */

/**
 * Класс работы с логами
 * @author    viktor
 * @version   2.4.0
 * @package   Micr0
 */
class Log
{
    protected static $_db = null;

    # Типы записей
    const T_PHP_EXCEPTION = 'php_exception';
    const T_PHP_ERROR = 'php_error';
    const T_DB_EXCEPTION = 'db_exception';
    const T_DB_QUERY = 'db_query';


    # Доступные поля (атрибуты) отдельной записи лога
    const A_DATETIME = '';
    const A_EVENT_TYPE = 'event_type';
    const A_TEXT_MESSAGE = 'text_message';
    const A_EXCEPTION = 'exception';
    const A_PHP_ERROR_MESSAGE = 'exception_message';
    const A_PHP_ERROR_CODE = 'php_error_code';
    const A_PHP_FILE_NAME = 'php_file_name';
    const A_PHP_FILE_LINE = 'php_file_line';
    const A_PHP_TRACE = 'php_trace';
    const A_PHP_CONTEXT = 'php_context';
    const A_DB_LAST_QUERY = 'db_last_query';
    const A_DB_QUERY_TYPE = 'db_query_type';
    const A_DB_ROWS_AFFECTED = 'db_rows_affected';
    const A_DB_USERNAME = 'db_username';
    const A_DB_NAME = 'db_name';
    const A_DB_HOST = 'db_host';
    const A_DB_ENCODING = 'db_encoding';
    const A_DB_SERVER_INFO = 'db_server_info';
    const A_DB_PING = 'db_ping';
    const A_DB_STATUS = 'db_status';
    const A_DB_RESULT = 'db_result';
    const A_DB_LAST_ERROR = 'db_last_error';
    const A_DB_CONNECT_ERROR = 'db_connect_error';
    const A_HTTP_REQUEST_METHOD = 'http_request_method';
    const A_HTTP_SERVER_NAME = 'http_server_name';
    const A_HTTP_REQUEST_URI = 'http_request_uri';
    const A_HTTP_USER_AGENT = 'http_user_agent';
    const A_HTTP_REMOTE_ADDRESS = 'http_remote_addr';
    const A_SESSION_ID = 'session_id';
    const A_SESSION_USER_ID = 'session_user_id';


    # Важные константы
    const MESSAGE_SEPARATOR = "\n\n\n\n";
    const MESSAGE_HTML_SEPARATOR = '<br/><br/>';

    # Языковые константы класса
    const L_LOG_EMPTY = 'Файл лога пока пуст';
    const L_EMPTY_MESSAGE = 'Запись лога пуста или имеет неправильный формат';

    # Методы класса
    /**
     * Получение заголовков всех доступных полей (атрибутов) отдельной записи лога
     * @return array
     */
    public static function attributeLabels()
    {
        return [
            self::A_DATETIME => '',
            self::A_EVENT_TYPE => 'Тип события',
            self::A_TEXT_MESSAGE => 'Ошибка',
            self::A_DB_LAST_QUERY => 'Предыдущий запрос',
            self::A_DB_QUERY_TYPE => 'Тип запроса',
            self::A_DB_ROWS_AFFECTED => 'Число затронутых строк',
            self::A_DB_USERNAME => 'Пользователь БД',
            self::A_DB_NAME => 'Имя БД',
            self::A_DB_HOST => 'Хост БД',
            self::A_DB_ENCODING => 'Кодировка БД',
            self::A_DB_SERVER_INFO => 'Сервер БД',
            self::A_DB_PING => 'Пинг БД',
            self::A_DB_STATUS => 'Статус БД',
            self::A_DB_RESULT => 'Результат запроса',
            self::A_DB_LAST_ERROR => 'Ошибка запроса к БД',
            self::A_DB_CONNECT_ERROR => 'Ошибка сединения с БД',
            self::A_PHP_ERROR_MESSAGE => 'Сообщение',
            self::A_PHP_ERROR_CODE => 'Код',
            self::A_PHP_FILE_NAME => 'Файл',
            self::A_PHP_FILE_LINE => 'Строка',
            self::A_PHP_TRACE => 'Стек вызова',
            self::A_PHP_CONTEXT => 'Контекст вызова',
            self::A_EXCEPTION => 'Исключение',
            self::A_HTTP_REQUEST_METHOD => 'Метод запроса',
            self::A_HTTP_SERVER_NAME => 'Сервер',
            self::A_HTTP_REQUEST_URI => 'URI',
            self::A_HTTP_USER_AGENT => 'User Agent',
            self::A_HTTP_REMOTE_ADDRESS => 'IP клиента',
            self::A_SESSION_ID => 'id PHP сессии',
            self::A_SESSION_USER_ID => 'id пользователя'
        ];
    }


    /**
     * Преобразовывает массив параметров в html-представление ошибки
     * Подход не комильфо, да, зато всегда под рукой в том же классе
     * @param array $messageArray Сообщение в виде массива
     * @return string
     */
    public static function parseMessage($messageArray)
    {
        $captions = self::attributeLabels();
        $result = '<table>';
        if (is_array($messageArray)) {
            foreach ($messageArray as $caption => $data) {

                switch ($caption) {
                    case self::A_DATETIME :
                        $data = "<b>$data</b>";
                        break;

                    case self::A_PHP_TRACE :
                    case self::A_PHP_CONTEXT :
                        // Пропишем стили для наглядного вывода лога в log/index.php и css/log.css, но и здесь на всякий случай оставим
                        // <div style="min-height:100px; max-height:400px; overflow-x:scroll; overflow-y:scroll; font-size:7pt; border:1px dashed; padding:2px 0px 4px 6px; background-color:#dddddd;">
                        $data = self::printObject($data, true);
                        break;

                    default:
                        $res = self::printObject($data, false);
                        $data = is_array($data) ? "<pre>$res</pre>" : $res;
                }

                // В пустых строках толку нет
                if ($data !== '') {
                    $result .=
                        '<tr>' . // Стиль "text-align:right; vertical-align:top; font-weight: bold;" перенесён в css/log.css
                        '<td class="l-col">' .
                        ($captions[$caption] === '' ? '' : $captions[$caption] . ':') .
                        '</td>' .
                        "<td>$data</td>" .
                        '</tr>';
                }
            }
            $result .= '</table>';

        } else {
            $result = self::L_EMPTY_MESSAGE;
        }
        return $result . self::MESSAGE_HTML_SEPARATOR;
    }


    /**
     * Запись в файл лога сообщения
     * @param string $filename Короткое имя файла
     * @param array $messageArray Сообщение, записываемое в файл
     * @return bool
     * @throws Exception
     */
    protected static function toFile($filename, $messageArray)
    {
        $filePath = CONFIG::ROOT . DIRECTORY_SEPARATOR . CONFIG::LOG_DIR . DIRECTORY_SEPARATOR . $filename;
        if (!is_writable($filePath)) {
            try {
                // Открывать тут можно не абы какой файл, а только в папке логов
                fopen($filePath, "bw");
            } catch (Exception $e) {
                throw new LogException(LogException::L_LOG_FILE_UNWRITABLE . ' - ' . $filename);
            }
        }
        return file_put_contents($filePath, addslashes(serialize($messageArray)) . self::MESSAGE_SEPARATOR,
            FILE_APPEND);
    }


    /**
     * Запись в таблицу логов БД сообщения
     * @param array $messageArray Сообщение, записываемое в лог
     * @return bool
     */
    protected static function toDb($messageArray)
    {
        $messageArray = Filter::slashesAdd($messageArray);
        /** @todo Дописать нормальную работу с БД */
        $result = self::$_db->query($messageArray);
        return $result;
    }


    /**
     * Запись в файл лога или определённую таблицу БД сообщения
     * @param array $object Сообщение, записываемое в лог
     * @param mixed $filename ,.. Имя файла логов
     * @return bool
     */
    public static function save(array $object, $filename = null)
    {
        if (!isset($object[self::A_DATETIME])) {
            $object = [self::A_DATETIME => date("Y-m-d H:i:s")] + $object;// Дата должна идти первой в сообщении
        }
        if (CONFIG::LOG_USE_DB) {
            return (bool)self::toDb($object);
        } else {
            return (bool)self::toFile($filename ? $filename : CONFIG::LOG_FILE, $object);
        }
    }


    /**
     * Выводит на экран список логов
     * @param string $typeName Тип логов
     * @param int $startFrom Начальная позиция в выборке
     * @param int $limit Число выбираемых записей
     * @param bool $descOrder ,.. Флаг - порядок вывода записей(обратный или прямой)
     * @return string
     */
    public static function showLogDb($typeName, $startFrom, $limit, $descOrder = true)
    {
        /** @todo Дописать нормальную работу с БД */
        return self::$_db->query($typeName, $startFrom, $limit, 'datetime', $descOrder);
    }


    /**
     * Получает количество записей в логе
     * @param string $typeName Тип логов
     */
    public static function checkLogDb($typeName)
    {
        /** @todo Дописать нормальную работу с БД */
        return self::$_db->query($typeName);
    }


    /**
     * Выводит на экран файл лога
     * @param string $fileName Имя файла
     * @param bool $descOrder ,.. Флаг - порядок вывода записей(обратный или прямой)
     * @return string
     * @throws Exception
     */
    public static function showLogFile($fileName, $descOrder = true)
    {
        $filePath = CONFIG::ROOT . DIRECTORY_SEPARATOR . CONFIG::LOG_DIR . DIRECTORY_SEPARATOR . $fileName;
        if (!is_readable($filePath)) {
            throw new LogException(LogException::L_LOG_FILE_UNREADABLE . ' - ' . $fileName);
        }
        $content = file_get_contents($filePath);
        $content = explode(self::MESSAGE_SEPARATOR, $content);
        $result = '';
        if (count($content) > 1) {
            foreach ($content as $key => $message) {
                if ($message == '') {
                    break;
                }
                if ($descOrder) {
                    $result = self::parseMessage(unserialize(stripslashes($message))) . $result;
                } else {
                    $result .= self::parseMessage(unserialize(stripslashes($message)));
                }
            }
        } else {
            $result = self::L_LOG_EMPTY;
        }
        return $result;
    }






# ---------------------------------------------- Методы перевода переменных в строку ---------------------------------------------- #

    /**
     * Вывод сложного объекта в строку с подробной информацией
     * @param mixed $object Выводимый объект
     * @param bool $withPre Флаг - оборачивать или нет результат тегами <pre>
     * @return string
     */
    public static function dumpObject($object, $withPre = false)
    {
        ob_start();
        var_dump($object);
        $strObject = ob_get_contents();
        ob_end_clean();
        return $withPre
            ? '<pre>' . $strObject . '</pre>'
            : $strObject;
    }


    /**
     * Вывод сложного объекта в строку с подробной информацией
     * @param mixed $object Выводимый объект
     * @param bool $withPre Флаг - оборачивать или нет результат тегами <pre>
     * @return string
     */
    public static function exportObject($object, $withPre = false)
    {
        $result = var_export($object, true);
        return $withPre
            ? '<pre>' . $result . '</pre>'
            : $result;
    }


    /**
     * Вывод сложного объекта в строку или на экран
     * @param mixed $object Выводимый объект
     * @param bool $withPre Флаг - оборачивать или нет результат тегами <pre>
     * @return string Строковое представление элемента, или bool в случае вывода его в буфер вывода
     */
    public static function printObject($object, $withPre = false)
    {
        $result = print_r($object, true);
        return $withPre
            ? '<pre>' . $result . '</pre>'
            : $result;
    }


    /**
     * Попытка вывести переменную в строку
     * @param mixed $param Переменная для перевода в строку
     * @return string
     */
    public static function showObject($param)
    {
        if (Filter::isString($param)) {
            return "'$param'";
        }
        if (Filter::isBool($param)) {
            return $param ? 'true' : 'false';
        }
        if (Filter::isNumeric($param) || Filter::isDate($param) || Filter::isDatetime($param)) {
            return strval($param);
        }
        return self::dumpObject($param, false);
    }


    /**
     * Выжимка исключения в массив
     * @param Exception $e
     * @return array
     */
    public static function dumpException(Exception $e)
    {
        return [
            self::A_EVENT_TYPE => self::T_PHP_EXCEPTION,
            self::A_EXCEPTION => $e->__toString(),
            self::A_PHP_ERROR_MESSAGE => $e->getMessage(),
            self::A_PHP_ERROR_CODE => $e->getCode(),
            self::A_PHP_FILE_NAME => $e->getFile(),
            self::A_PHP_FILE_LINE => $e->getLine(),
            self::A_PHP_TRACE => $e->getTrace(), // Serialization of 'Closure' is not allowed
            self::A_SESSION_ID => session_id(),
            self::A_HTTP_REQUEST_METHOD => $_SERVER['REQUEST_METHOD'],
            self::A_HTTP_SERVER_NAME => $_SERVER['SERVER_NAME'],
            self::A_HTTP_REQUEST_URI => $_SERVER['REQUEST_URI'],
            self::A_HTTP_USER_AGENT => $_SERVER['HTTP_USER_AGENT'],
            self::A_HTTP_REMOTE_ADDRESS => $_SERVER['REMOTE_ADDR']
        ];
    }


    /**
     * Добавление к строке метки времени для вывода в лог
     * @param string $message
     * @return string
     */
    public static function line($message)
    {
        return '[' . date("H:i:s") . '] ' . $message; // date("Y-m-d H:i:s")
    }

}

