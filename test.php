<?php

/**
 * This file is to test the function
 */

require_once(__DIR__ . "/unc_serial_curl.php");

// we want to get the google logo
// this can be an array (see next example
$files = 'https://www.google.com/images/srpr/logo11w.png';
// for testing, we just write to the current directory
$target_directory = __DIR__;

// execute
unc_serial_curl_test($files, $target_directory);


// this is a sample function 
function unc_serial_curl_test($files, $target_directory) {

    // these are Certificates for testing. Get your own, updated ones here:
    // http://curl.haxx.se/docs/caextract.html
    $ssl_cert = __DIR__ . "/ca-bundle_2015_07_18.crt";

    $result_arr = unc_serial_curl($files, 0, 50, $ssl_cert);

    foreach ($result_arr as $file_id => $R) {
        $url = $R['response']['url'];
        $filename = basename($url);
        // let's make a rename in case 2 files have the same name
        $safe_filename = $file_id . "_" . $filename;

        // target directory writable check
        if (!is_writable($target_directory)) {
            die("directory $target_directory is not writable!");
        }

        // write file content to current directory
        $write_check = file_put_contents($target_directory . "/" .$safe_filename, $R['content']);
        if (!$write_check)  {
            die("Failed to write $safe_filename to $target_directory");
        }
    }
}

/**
 * sample valid response
 * 
  ["response"]=>
    array(23) {
      ["url"]=>
      string(46) "https://www.google.com/images/srpr/logo11w.png"
      ["content_type"]=>
      string(9) "image/png"
      ["http_code"]=>
      int(200)
      ["header_size"]=>
      int(346)
      ["request_size"]=>
      int(179)
      ["filetime"]=>
      int(-1)
      ["ssl_verify_result"]=>
      int(0)
      ["redirect_count"]=>
      int(0)
      ["total_time"]=>
      float(0.238054)
      ["namelookup_time"]=>
      float(0.001161)
      ["connect_time"]=>
      float(0.029571)
      ["pretransfer_time"]=>
      float(0.169783)
      ["size_upload"]=>
      float(0)
      ["size_download"]=>
      float(14022)
      ["speed_download"]=>
      float(58902)
      ["speed_upload"]=>
      float(0)
      ["download_content_length"]=>
      float(14022)
      ["upload_content_length"]=>
      float(0)
      ["starttransfer_time"]=>
      float(0.20908)
      ["redirect_time"]=>
      float(0)
      ["redirect_url"]=>
      string(0) ""
      ["primary_ip"]=>
      string(22) "2607:f8b0:4003:c02::68"
      ["certinfo"]=>
      array(0) {
      }
    }

 */