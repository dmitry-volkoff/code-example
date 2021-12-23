<?php
namespace CRM\Utils;
header('X-Accel-Buffering: no');
header('Content-Encoding: identity');
header('Accept-Encoding: identity');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('memory_limit', '128M');
//ini_set('output_buffering', 'Off');
//ini_set('implicit_flush', 'On');
ignore_user_abort(true);
set_time_limit(0);

define('WEB_HOOK_URL_CLOUD', '');//url on create Webhook - облако
define('WEB_HOOK_URL_BOX', '');//url on create Webhook - коробка
//define('WEB_HOOK_URL_BOX', 'https://100.90.1.50/rest/31/i7ziq2fpk78danrl/');//url on create Webhook - коробка

const DRY_RUN = 0; // прогон без реальной записи в базу
const RUN_MAX_RECORDS = 0; // столько записей обработать и остановиться; 0 = все записи
const TITLE_PREFIX = ''; //'beta-test-';
const TEST_USER_BOX = 0; //40; // ID пользователя для теста экспорта; 0 - если не используется

const ORIGINATOR_ID = 'cloud_lead'; // Это пишем в одноимённое поле сделки для связи с облачным лидом

const IS_CLI = (PHP_SAPI === 'cli');
const LINE_BREAK = IS_CLI ? "\n" : '<br>';
//const SPACE_CHAR = IS_CLI ? " " : '&nbsp;';

ob_implicit_flush(true);

//require_once './vendor/autoload.php';
require_once './src/autoload.php';