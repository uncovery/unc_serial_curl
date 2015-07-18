# unc_serial_curl
is a replacement for file_get_contents, supporting several files and SSL

How to:
-------
    // include the file
    require_once(__DIR__ . "/unc_serial_curl.php");
    // add absulute path to SSL certificates
    // download fresh ones here:
    // http://curl.haxx.se/docs/caextract.html
    $ssl_cert = __DIR__ . "/ca-bundle_2015_07_18.crt";
    // add either one file or array of several
    $files = array('file1' => "https://test.com/test.png");
    // run the function
    $result_arr = unc_serial_curl($files, 0, 50, $ssl_cert);

The result will look like this

    array(
        'file1' = array(
            // this contains the file data. use file_put_contents() to save to disk
            'content' => 'whatever your file contents are' 
            // response codes
            'response' => array(
                'url' => 'https://test.com/test.png',
                'content_type' => "image/png"
                'http_code' => 200,
                // and so on. See text.php for a full list
            )
        )
    )

Hints:
------

* If there is only one file, there is not need to use an array. the result will still be an array and the file contents will have the index 0 (instead of 'file1' as in the example above)
* If the array is not associative, the results will be numbered 0,1,2 etc.
* If the files do not use HTTPS, there is no need to use a ssl_cert path.

see test.php for an example