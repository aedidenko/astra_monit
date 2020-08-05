<?php
if (isset($channel_name_problem['name'])) {
    if (defined('API_KEY')) {alert_pushbullet($channel_name_problem['name'], $channel_name_problem['astraname'], "проблемы");}
}
else if (isset($channel_name_ok['name'])) {
    if (defined('API_KEY')) {alert_pushbullet($channel_name_ok['name'], $channel_name_ok['astraname'], "работает");}
}

if (isset($channel_name_problem['name'])) {
    if (defined('TELEGRAM')) {alert_telegram_msg($channel_name_problem['name'], $channel_name_problem['astraname'], "проблемы", "\xE2\x9D\x97");}
}
else if (isset($channel_name_ok['name'])) {
    if (defined('TELEGRAM')) {alert_telegram_msg($channel_name_ok['name'], $channel_name_ok['astraname'], "работает", "\xE2\x9C\x85");}
}

if (isset($dvb_name_problem['name'])) {
    if (defined('TELEGRAM')) {alert_telegram_graph($input_info['dvb_id'], $dvb_name_problem['name'], $input_info['status'], $input_info['ber'], $input_info['unc']);}
}





function alert_pushbullet($alert_channel, $alert_astra, $text) {
$api_array = explode(" ", API_KEY);
    foreach($api_array as $api_ar_k=>$api_key) {
        if (extension_loaded('curl')) {
            //$api_key = API_KEY;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.pushbullet.com/v2/pushes");
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'type=note&title='.$alert_channel.': '.$text.'&body='.$alert_astra.'');
            curl_setopt($ch, CURLOPT_USERPWD, $api_key);
            curl_exec($ch);
            curl_close($ch);
        }
        else{
            exec ("curl https://api.pushbullet.com/v2/pushes -u '$api_key': -d type=note -d title='$alert_channel $text' -d body='$alert_astra' -X POST --max-time 5");
        }
    }
}

function alert_telegram_msg($alert_channel, $alert_astra, $text, $smile) {
    exec ("includes/telegam/send_msg.py '$smile' '<b>$alert_channel</b>' '($alert_astra)' '$text'");
}

function alert_telegram_graph($dvb_id, $dvb_name, $dvb_status, $dvb_ber, $dvb_unc) {
    exec ("includes/telegam/send_img.py '$dvb_id' '$dvb_name,' 'status: $dvb_status,' 'ber: $dvb_ber,' 'unc: $dvb_unc'");
}

?>
