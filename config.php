<?php

const APP_LOCALE = 'cs_CZ';
const APP_LANG_TAG = 'cs';
const APP_LOCALES = array('cs', 'en');

const DB_HOST = 'localhost';
const DB_NAME = 'tbdevelopment';
const DB_USER = 'tbdevelopment';
const DB_PASS = '0#k0woAAM';

const MAIL_host = "";
const MAIL_USER = "";
const MAIL_PASS = "";
const MAIL_SMTP_PORT = 465;
const MAIL_SMTPS = "ssl";
const MAIL_SMTP_AUTH = true; 

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

const DEFAULT_DOMAIN = 'tbdiwww.konarik.info';
const APP_DOMAIN = 'tbdiapp.konarik.info';
define('APP_DOMAIN_URL', $protocol . APP_DOMAIN);

// const DEFAULT_DOMAIN = 'tbdevelopment.cz';
// const APP_DOMAIN = 'club.tbdevelopment.cz';
// const APP_DOMAIN_URL = 'http://club.tbdevelopment.cz';

const DOMAINS = array(DEFAULT_DOMAIN, APP_DOMAIN);

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