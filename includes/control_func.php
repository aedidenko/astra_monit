<?php

function reload_astra($astra_id)
{
    $query = new db_query();
    $astra = $query->assoc_array("select * from astra_instance where astra_id=".intval($astra_id));

    $cmd_curl = '{"cmd":"restart"}';

    if (isset($astra['auth_api'])) {
        $astra['event_request'] = preg_replace('/http:\/\//', 'http://'.$astra['auth_api'].'@', $astra['event_request']);
    }
    post_json($astra['event_request'].'/control/', $cmd_curl);
}

function reload_channel($astra_id, $channel_id)
{
    $query = new db_query();
    $astra = $query->assoc_array("select * from astra_instance where astra_id=".intval($astra_id));

    $cmd_curl = '{"cmd":"restart-stream","id":"'.$channel_id.'"}';

    if (isset($astra['auth_api'])) {
        $astra['event_request'] = preg_replace('/http:\/\//', 'http://'.$astra['auth_api'].'@', $astra['event_request']);
    }
    post_json($astra['event_request'].'/control/', $cmd_curl);
}


function post_json($url, $cmd_curl)
{
    if (extension_loaded('curl')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$cmd_curl");
        curl_exec($ch);
        curl_close($ch);
    }
    else{
        exec ("curl -X POST -d '$cmd_curl' '$url' --max-time 3");
//        exec ("echo '$cmd_curl, $url' >> /tmp/00_post_json");
    }
}

?>
