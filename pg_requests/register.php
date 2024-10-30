<?php

require_once(__PG_ROOT__.'/pg_requests/http_request.php'); 

//register plugin for usage, get it from pg_login_data option
//return html code or session
function pg_register_plugin($html = false)
{
	$login = array();
	$opt = get_option('pg_login_data');

	if ($opt)
	{
		$json = json_decode($opt);
		$login['api_key'] = $json->api_key;
		$login['username'] = $json->username;
		$login['password'] = $json->password; //md5 hashed
	}
	
	if ($html)
		return '&rarr;  <font color="red">ung&uuml;ltig</font>';
}

//get api key and return quota for dashboard
function pg_get_quota()
{
    //$retval = array('max'=>1, 'used'=>0); //quota for given api key
    
    $retval = array('max'=>0, 'used'=>0); //for show
      
    $opt = get_option('pg_login_data');
    if ($opt)
    {
        $json = json_decode($opt);
        $data = array("apikey" => (string)$json->api_key);
        
        $result = json_decode(pg_postRequest($data, 'quota'));
        if ($result)
        {
           $retval['max'] = (int)$result->max_quota;
           $retval['used'] = (int)$result->used_quota;
        }
    }   
       
    return $retval;
}