<?php

require_once(__PG_ROOT__.'/pg_config.php'); 

/*methods are:
translate: get simple translation
quota: get the quota
*/
function pg_postRequest($data, $method="translate")
{
    $opt = get_option('pg_login_data');

    if ($opt)
    {
        $json = json_decode($opt);
        $key = $json->api_key;
    }
    else
        wp_die("Kein API Key gefunden");

    switch ($method)
    {
        case 'translate':
            
            $request = curl_init();
            
            curl_setopt($request, CURLOPT_CONNECTTIMEOUT, PG_CURLLIMIT);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_URL, PG_HOST.'/'.PG_JSON.'?func='.PG_FUNC);
            curl_setopt($request, CURLOPT_PORT, PG_PORT);
            curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-type: text/json", "X-LTS-Apikey: ".(string)$key));
            curl_setopt($request, CURLOPT_POST, 1);
            curl_setopt($request, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($request);
            
            if (!$result)
                trigger_error(curl_error($request));

            curl_close($request);

            break;
            
        case 'info':
        
            $request = curl_init();

            curl_setopt($request, CURLOPT_CONNECTTIMEOUT, PG_CURLLIMIT);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_URL, PG_HOST.'/'.PG_JSON.'?func=info');
            curl_setopt($request, CURLOPT_PORT, PG_PORT);
            curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-type: text/json"));
            curl_setopt($request, CURLOPT_POST, 1);
            
            $result = curl_exec($request);
            
            if (!$result)
                trigger_error(curl_error($request));
            
            curl_close($request);
            
            break;
        
        case 'quota':
            
            $url = "http://".PG_QUOTA_HOST.":".PG_QUOTA_PORT;
            $func = "/".PG_QUOTA_FUNC."/";
            
            $request = curl_init();
            
            curl_setopt($request, CURLOPT_CONNECTTIMEOUT, PG_CURLLIMIT);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_URL, $url.$func);
            curl_setopt($request, CURLOPT_POST, 1);
            curl_setopt($request, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($request);

            if (!$result)
                trigger_error(curl_error($request));
            
            curl_close($request);

            //print_r("Result: '".$result."'.<br>\n"); 
            
            break;
            
         case 'hasquota':
            
            $url = "http://".PG_QUOTA_HOST.":".PG_QUOTA_PORT;
            $func = "/hasQuota/";
            
            $request = curl_init();
            
            curl_setopt($request, CURLOPT_CONNECTTIMEOUT, PG_CURLLIMIT);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_URL, $url.$func);
            curl_setopt($request, CURLOPT_POST, 1);
            curl_setopt($request, CURLOPT_POSTFIELDS, $data);

            $result = curl_exec($request);

            if (!$result)
                trigger_error(curl_error($request));
            
            curl_close($request);

            //print_r("Result: '".$result."'.<br>\n"); 
            
            break;
    }
    
	return $result;
}
