<?php
/**
 * Error'n'Exceptions handler class
 * Special thanks to: all, http://www.php.net
 * Copyright (c)    viktor, Belgorod, 2008-2016
 * Email            vinjoy@bk.ru
 * version          2.0.0
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the MIT License (MIT)
 * @see https://opensource.org/licenses/MIT
 */


/** @todo Добавить возвращение или отрисовку сообщений об ошибках - как в гет, так и пост, в зависимости от режиме дебага */
/** @todo Добавить обработку register_shutdown_function */


/**
 * Обработчик неперехваченных исключений
 * @param Exception $e
 * @return void
 */
function customExceptionHandler(Exception $e)
{
    // Если исключение из нашей иерархии, воспользуемся его собственным методом
    if ($e instanceof BaseException) {
        $mArr = $e->toArray();

        // Иначе выводим всю стандартную информацию
    } else {
        $mArr = Log::dumpException($e);
    }
    // Без вьюх пока только так
    echo "Exception has been raised \"{$mArr[Log::A_PHP_ERROR_MESSAGE]}\"<br/><br/>";
    Log::save(
        $mArr,
        CONFIG::ERROR_LOG_FILE
    );
}


/**
 * Обработчик ошибок php
 * @param int $errNo Уровень ошибки
 * @param string $errStr Сообщение об ошибке
 * @param string $errFile Файл с ошибкой
 * @param int $errLine Строка с ошибкой
 * @param array $errContext Массив всех переменных, существующих в области видимости, где произошла ошибка
 * @return void
 */
function customErrorHandler($errNo, $errStr, $errFile, $errLine, $errContext = null)
{
    $mArr = [
        Log::A_EVENT_TYPE => Log::T_PHP_ERROR,
        Log::A_SESSION_ID => session_id(),
        Log::A_PHP_ERROR_MESSAGE => $errStr,
        Log::A_PHP_ERROR_CODE => $errNo,
        Log::A_PHP_FILE_NAME => $errFile,
        Log::A_PHP_FILE_LINE => $errLine,
        Log::A_HTTP_REQUEST_METHOD => $_SERVER['REQUEST_METHOD'],
        Log::A_HTTP_SERVER_NAME => $_SERVER['SERVER_NAME'],
        Log::A_HTTP_REQUEST_URI => $_SERVER['REQUEST_URI'],
        Log::A_HTTP_USER_AGENT => $_SERVER['HTTP_USER_AGENT'],
        Log::A_HTTP_REMOTE_ADDRESS => $_SERVER['REMOTE_ADDR'],
    ];
    if ($errContext) {
        $mArr[Log::A_PHP_CONTEXT] = $errContext;
    }
    // Без вьюх пока только так
    echo "Error occurred: \"{$mArr[Log::A_PHP_ERROR_MESSAGE]}\"<br/><br/>";
    Log::save(
        $mArr,
        CONFIG::ERROR_LOG_FILE
    );
}


/**
 * Обработчик завершения скрипта и фатальных ошибок
 */
function customShutdownHandler()
{

}


/**
 * Класс работы со стеком ошибок
 * @version   2.0.0
 * @author    viktor
 * @package   Micr0
 */
class ErrorHandler
{
    /** Режим отладки */
    protected static $_debugMode = true;

    /** Обработчик ошибок */
    protected static $_errorHandler = null;
    /** Обработчики завершения работы скрипта */
    protected static $_shutdownHandlers = [];
    /** Обработчик исключений */
    protected static $_exceptionHandler = null;


    /**
     * Устанавливает обработчик на программные ошибки
     * @param callable $func Обработчик ошибок вида bool function (int $errno, string $errstr [, string $errfile [, int $errline [, array $errcontext ]]] )
     * @return mixed
     * @see http://php.net/manual/ru/function.set-error-handler.php
     */
    static function setErrorHandler(callable $func)
    {
        self::$_errorHandler = $func;
        return set_error_handler($func, E_ALL);
    }


    /**
     * Устанавливает обработчик на программные ошибки
     * @param callable $func Обработчик исключений вида void function (Exception $e){}
     * @return callable
     * @see http://php.net/manual/ru/function.set-exception-handler.php
     */
    static function setExceptionHandler(callable $func)
    {
        self::$_exceptionHandler = $func;
        return set_exception_handler($func);
    }


    /**
     * Устанавливает обработчик на окончание скрипта
     * @param callable $func Обработчик ошибок
     * @param mixed $params Возможные параметры обработчика
     * @return void
     */
    static function setShutdownHandler(callable $func, $params = null)
    {
        self::$_shutdownHandlers[] = $func;
        register_shutdown_function($func);
    }


    /**
     * Установка режима отладки
     * @param bool $debugMode
     * @return bool|void
     */
    static function debugMode($debugMode = null)
    {
        if (func_num_args() == 1) {
            self::$_debugMode = $debugMode;
        } else {
            return self::$_debugMode;
        }
    }

}


// Назначаем обработчики различных ошибок
ErrorHandler::setErrorHandler('customErrorHandler');
ErrorHandler::setExceptionHandler('customExceptionHandler');
ErrorHandler::setShutdownHandler('customShutdownHandler');