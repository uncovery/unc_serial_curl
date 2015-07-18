<?php
/**
 * This is a function that can replace file_get_contents for single file and also
 * download several files in an array. The files are passed back in the result along with the answer
 * 
 * Supports SSL transfers
 * 
 * @param array $url_raw either the URL of a single file or an array of files, optionally with custom keys to match incoming with ougoing
 * @param int $javascript_loop How often to loop to handle Javascript 
 * @param int $timeout How long to waitfor the timeout (default 50)
 * @param boolean $header Include or exclude the header in the file (for debugging set to true)
 * @param string $ssl_cert absolute path to the SSL certificate to successfully download files over SSL. See http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/ for more info
 * @param string $custom_agent default agent is Firefox v. 36
 * @return array
 */
function unc_serial_curl($url_raw, $javascript_loop = 0, $timeout = 50, $header = false, $ssl_cert = '', $custom_agent = false) {
    if (!is_array($url_raw)) {
        $urls = array($url_raw);
    } else {
        $urls = $url_raw;
    }
    
    if (!$custom_agent) {
        $user_agent = "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0";
    } else {
        $user_agent = $custom_agent;
    }

    $channels = array();
    $mh = curl_multi_init();
    foreach ($urls as $key => $url) {
        $url_fixed = str_replace( "&amp;", "&", urldecode(trim($url)));
        $cookie = tempnam("/tmp", "CURLCOOKIE");
        $channels[$key] = curl_init();
        curl_setopt_array($channels[$key], array(
            CURLOPT_USERAGENT => $user_agent,
            CURLOPT_URL => $url_fixed,
            CURLOPT_HEADER  => $header, // this needs to be disabled otherwise images are corrupted
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CAINFO => $ssl_cert,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_MAXREDIRS => 10,
        ));
        $check = curl_multi_add_handle($mh, $channels[$key]);
        if ($check !== 0) {
            die("Failed to add curl options for URL $url_fixed");
        }
    }

    // repeat curl as long as it takes to process
    $active = null;
    do {
        $status = curl_multi_exec($mh, $active);
    } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

    $output = array();
    foreach ($channels as $key => $channel) {
        $output[$key] = unc_serial_curl_response_process($channel, $user_agent, $javascript_loop);
        curl_multi_remove_handle($mh, $channel);
        curl_close($channel);
    }
    curl_multi_close($mh);
    return $output;
}

/**
 * Extension function of unc_serial_curl to parse output and re-run the function
 * if necessary
 *
 * @param type $channel
 * @param type $user_agent
 * @param type $javascript_loop
 * @return type
 */
function unc_serial_curl_response_process($channel, $user_agent, $javascript_loop) {
    $content = curl_multi_getcontent($channel);
    $response = curl_getinfo($channel);
    if ($response['http_code'] == 301 || $response['http_code'] == 302) {
        ini_set("user_agent", $user_agent);
        $headers = get_headers($response['url']);
        if ($headers) {
            foreach($headers as $value) {
                if (substr(strtolower($value), 0, 9 ) == "location:") {
                    return unc_serial_curl(trim(substr($value, 9, strlen($value))));
                }
            }
        }
    }
    $pattern_1 = "/>[[:space:]]+window\.location\.replace\('(.*)'\)/i";
    $pattern_2 = "/>[[:space:]]+window\.location\=\"(.*)\"/i";
    if ((preg_match($pattern_1, $content, $value) || preg_match($pattern_2, $content, $value)) && $javascript_loop < 5) {
        return unc_serial_curl( $value[1], $javascript_loop+1);
    } else {
        return array('content' => $content, 'response' => $response);
    }
}
