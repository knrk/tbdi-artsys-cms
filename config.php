<?php

const DB_HOST = 'localhost';
const DB_NAME = 'tbdevelopment';
const DB_USER = 'tbdevelopment';
const DB_PASS = '0#k0woAAM';

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

const DEFAULT_DOMAIN = 'tbdiwww.konarik.info';
const APP_DOMAIN = 'tbdiapp.konarik.info';
define('APP_DOMAIN_URL', $protocol . APP_DOMAIN);

// const DEFAULT_DOMAIN = 'tbdevelopment.cz';
// const APP_DOMAIN = 'club.tbdevelopment.cz';
// const APP_DOMAIN_URL = 'http://club.tbdevelopment.cz';

const DOMAINS = array(DEFAULT_DOMAIN, APP_DOMAIN);

?>