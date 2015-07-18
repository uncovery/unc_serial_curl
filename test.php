<?php

/**
 * This file is to test the function
 */

require_once(__DIR__ . "/unc_serial_curl.php");

// we want to get the google logo
$url_raw = 'https://www.google.com/images/srpr/logo11w.png';

// these are Certificates for testing. Get your own, updated ones here:
// http://curl.haxx.se/docs/caextract.html
$ssl_cert = __DIR__ . "/google.crt";

$result_arr = unc_serial_curl($url_raw, 0, 50, false, $ssl_cert);
var_dump($result_arr);