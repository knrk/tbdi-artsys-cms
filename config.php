<?php

const APP_LOCALE = 'cs_CZ';
const APP_LANG_TAG = 'cs';
const APP_LOCALES = array('cs', 'en');

const DB_HOST = 'localhost';
const DB_NAME = 'tbdevelopment';
const DB_USER = 'tbdevelopment';
const DB_PASS = '0#k0woAAM';

const MAIL = true;
const MAIL_HOSTNAME = "";
const MAIL_USER = "";
const MAIL_PASS = "";
const MAIL_SMTP_PORT = 465;
const MAIL_SMTPS = "ssl";
const MAIL_SMTP_AUTH = true;
// 0 - no debug, 1 - errors and messages, 2 - messages only
const MAIL_DEBUG = 1;
const MAIL_FROM = "robot@tbdevelopment.cz";
const MAIL_FROM_NAME = "TBDI";
const MAIL_REPLY_TO = "robot@tbdevelopment.cz";
const MAIL_REPLY_NAME = "TBDI";
const MAIL_CONTACT_DEFAULT = "robot@tbdevelopment.cz";

const CRON_TASKS = [
    'initial' => 'cc8161008478c7149b675d2c31b3f55a',
    'daily' => 'CLUQoA7BNPYzmu1EP6N1n4SEcr4uwvRsfyCdZ8iX',
    'monthly' => '9rjT9U9o1ttjklhlZ5r0CP1cGV5gZsclyYOC706U'
];

const PDF_META_AUTHOR = 'TBDI_Author';
const PDF_META_CREATOR = 'TBDI';

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

const DEFAULT_DOMAIN = 'tbdiwww.konarik.info';
const APP_DOMAIN = 'tbdiapp.konarik.info';
define('APP_DOMAIN_URL', $protocol . APP_DOMAIN);

// const DEFAULT_DOMAIN = 'tbdevelopment.cz';
// const APP_DOMAIN = 'club.tbdevelopment.cz';
// const APP_DOMAIN_URL = 'http://club.tbdevelopment.cz';

const DOMAINS = array(DEFAULT_DOMAIN, APP_DOMAIN);

const TEMPLATE_NAME_AJAX = "index";
const TEMPLATE_DIR_AJAX = "ajaxTemplate";
const TEMPLATE_NAME_PUBLIC = "index";
const TEMPLATE_DIR_PUBLIC = "publicTemplate";
const TEMPLATE_NAME_ADMIN = "index";
const TEMPLATE_DIR_ADMIN = "adminTemplate";
const TEMPLATE_NAME_CABINET = "index";
const TEMPLATE_DIR_CABINET = "cabinetTemplate";

const CACHE_DB_RESULTS = true;

const DEBUG = false;
const DEBUG_SOURCE = false;
const DEBUG_STACKTRACE = false;
const ERR_FRIENDLY = false;
const LOG = true;
const LOG_SIZE = 1024;

const AUTH_EXPIRE = 14400;
const AUTH_USER = 101578;

const DEFAULT_MODULE = "mainpage";
const DEFAULT_ACTION = "index";
?>