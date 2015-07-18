# unc_serial_curl
is a replacement for file_get_contents, supporting several files and SSL

How to:
-------

    require_once(__DIR__ . "/unc_serial_curl.php");
    $ssl_cert = __DIR__ . "/ca-bundle_2015_07_18.crt";
    $files = array('file1' => "https://test.com/test.png");
    $result_arr = unc_serial_curl($files, 0, 50, false, $ssl_cert);

The result will look like this

    array(
        'file1' = array(
            // this contains the file data. use file_put_contents() to save to disk
            'content' => '?' 
            // response codes
            'response' => array(
                'url' => 'https://test.com/test.png',
                'content_type' => "image/png"
                'http_code' => 200,
                // and so on. See text.php for a full list
            )
        )
    )

see test.php for an example