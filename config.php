<?php
/**
 * Общий конфиг
 * User: Виктор
 * Date: 12.02.2016
 * Time: 0:15
 */


/** @const Хост */
define('HOST', 'http://' . $_SERVER['HTTP_HOST'] . '/');


/**
 * Класс конфига со статическими свойствами
 */
class CONFIG
{
    # Файл с данными
    const BASE_FILENAME = 'KLADR.csv';


    # Общие
    /** @const bool Флаг дебага */
    const DEBUG = true;
    /** @const string Базовая директория */
    const ROOT = __DIR__;
    /** @const Домен проекта */
    const HOST = HOST;


    # Страница
    /** @const string Кодировка страниц */
    const PAGE_CHARSET = 'UTF-8';
    /** @const string Общий заголовок страниц */
    const PAGE_TITLE = 'Импорт КЛАДР ^_^';


    # Логи
    /** @const bool Флаг логгирования в БД или файл */
    const LOG_USE_DB = false;
    /** @const string Директория логов */
    const LOG_DIR = 'log';
    /** @const string Лог */
    const LOG_FILE = 'common.log';
    /** @const string Лог ошибок */
    const ERROR_LOG_FILE = 'error.log';
}