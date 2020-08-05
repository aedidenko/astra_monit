<?php
    require_once("includes/config.php");
    require_once("includes/mysql.php");

    $json_request = json_decode(file_get_contents('php://input'), true);

    //file_put_contents("/tmp/dump",$json_request,FILE_APPEND);

    $query = new db_query();

    $input_info = array_pop($json_request);
if (isset($input_info['scrambled'])) {
    $input_info['scrambled']=$input_info['scrambled']?'true':'false';
}
if (isset($input_info['onair'])) {
    $input_info['onair']=$input_info['onair']?'true':'false';
}


//$results = print_r($input_info, true);
//exec ("echo '$results' >> /tmp/112233");


//Автоматическое добавление каналов
if (isset($input_info['channel'])) {
    if (!isset($input_info['channel']['input']['0'])) { $input_info['channel']['input']['0'] = 'nil';}
    if (!isset($input_info['channel']['output']['0'])) { $input_info['channel']['output']['0'] = 'nil';}

    $astra_id_in_in = '0';
    if (isset($input_info['channel']['groups']['monitor'])) {
        $astra_id_in = $query->assoc_array("SELECT * from astra_instance WHERE astra_instance.name='".$query->escape($input_info['channel']['groups']['monitor'])."'");
        $astra_id_in_in = $astra_id_in['astra_id'];
        if (!isset($astra_id_in_in)) {
            $ret_astra = $query->result("insert into `astra_instance` set
                `name` = '".$query->escape($input_info['channel']['groups']['monitor'])."',
                `control_server_addr` = '".$query->escape($input_info['hostname'])."'
                ");
            }

        $ret_channel = $query->result("insert into `channel` set
            `name` = '".$query->escape($input_info['channel']['name'])."',
            z`name_rus` = '".$query->escape($input_info['channel']['name'])."',
            `channel_pnr` = '".$query->escape($input_info['channel']['channel_pnr'])."',
            `channel_id` = '".$query->escape($input_info['channel']['id'])."',
            `type` = '".$query->escape($input_info['channel']['type'])."',
            `astra_id` = '".$astra_id_in_in."'
            ON DUPLICATE KEY UPDATE
            `name_rus` = '".$query->escape($input_info['channel']['name'])."',
            `channel_pnr` = '".$query->escape($input_info['channel']['channel_pnr'])."',
            `channel_id` = '".$query->escape($input_info['channel']['id'])."',
            `type` = '".$query->escape($input_info['channel']['type'])."',
            `astra_id` = '".$astra_id_in_in."'
            ");
        }
    else {
        //если нету астры с id=0, то содать её. ToDo: переделать
        $astra_id_test = $query->assoc_array("SELECT astra_id from astra_instance WHERE astra_instance.astra_id='0' or astra_instance.name='autoscan'");
        $astra_id_test2 = $astra_id_test['astra_id'];
        if (!isset($astra_id_test2)) {
            $ret_astra = $query->result("insert into `astra_instance` set
                `name` = 'autoscan',
                `astra_id` = '0'
                ");
            $ret_astra2 = $query->result("update `astra_instance` set `astra_id` = '0' where `name` = 'autoscan'");
            }

        //если не указана категория в астре
        $ret_channel = $query->result("insert into `channel` set
            `name` = '".$query->escape($input_info['channel']['name'])."',
            `name_rus` = '".$query->escape($input_info['channel']['name'])."',
            `channel_pnr` = '".$query->escape($input_info['channel']['channel_pnr'])."',
            `channel_id` = '".$query->escape($input_info['channel']['id'])."',
            `type` = '".$query->escape($input_info['channel']['type'])."',
            `astra_id` = '".$astra_id_in_in."'
            ON DUPLICATE KEY UPDATE
            `name_rus` = '".$query->escape($input_info['channel']['name'])."',
            `channel_pnr` = '".$query->escape($input_info['channel']['channel_pnr'])."',
            `type` = '".$query->escape($input_info['channel']['type'])."'
            ");
        }

    $ret_input = $query->result("insert into `input` set
        `channel_id` = '".$query->escape($input_info['channel']['id'])."',
        `input_0` = '".$query->escape($input_info['channel']['input']['0'])."',
        `input_0_id` = '0'
        ON DUPLICATE KEY UPDATE
        `channel_id` = '".$query->escape($input_info['channel']['id'])."',
        `input_0` = '".$query->escape($input_info['channel']['input']['0'])."',
        `input_0_id` = '0'
        ");

    $ret_output = $query->result("insert into `output` set
        `channel_id` = '".$query->escape($input_info['channel']['id'])."',
        `output_0` = '".$query->escape($input_info['channel']['output']['0'])."',
        `output_0_id` = '0'
        ON DUPLICATE KEY UPDATE
        `channel_id` = '".$query->escape($input_info['channel']['id'])."',
        `output_0` = '".$query->escape($input_info['channel']['output']['0'])."',
        `output_0_id` = '0'
        ");

    }

//Автоматическое добавление dvb
if (isset($input_info['dvb'])) {
    $ret_dvb = $query->result("insert into `dvb_input` set
        `dvb_input_id` = '".$query->escape($input_info['dvb']['id'])."',
        `symbolrate` = '".$query->escape($input_info['dvb']['symbolrate'])."',
        `adapter` = '".$query->escape($input_info['dvb']['adapter'])."',
        `mac` = '".$query->escape($input_info['dvb']['mac'])."',
        `type` = '".$query->escape($input_info['dvb']['type'])."',
        `polarization` = '".$query->escape($input_info['dvb']['polarization'])."',
        `name` = '".$query->escape($input_info['dvb']['name'])."',
        `device` = '".$query->escape($input_info['dvb']['device'])."',
        `frequency` = '".$query->escape($input_info['dvb']['frequency'])."'
        ON DUPLICATE KEY UPDATE
        `symbolrate` = '".$query->escape($input_info['dvb']['symbolrate'])."',
        `adapter` = '".$query->escape($input_info['dvb']['adapter'])."',
        `mac` = '".$query->escape($input_info['dvb']['mac'])."',
        `type` = '".$query->escape($input_info['dvb']['type'])."',
        `polarization` = '".$query->escape($input_info['dvb']['polarization'])."',
        `name` = '".$query->escape($input_info['dvb']['name'])."',
        `device` = '".$query->escape($input_info['dvb']['device'])."',
        `frequency` = '".$query->escape($input_info['dvb']['frequency'])."'
        ");

}

//статистика
    if (isset($input_info['channel_id'])) {
        $ret = $query->result("update input set
            scrambled = '".$query->escape($input_info['scrambled'])."',
            cc_error = '".$query->escape($input_info['cc_error'])."',
            pes_error = '".$query->escape($input_info['pes_error'])."',
            bitrate = '".$query->escape($input_info['bitrate'])."',
            onair = '".$query->escape($input_info['onair'])."',
            last_update = now()
            where channel_id = '".$query->escape($input_info['channel_id'])."'");
    }

//Уведомление для канала срабатывает при повторной интерации в течении 5 минут. Проблемой считается любая ошибка или отсутствие сигнала.
    if (isset($input_info['channel_id']) and ($input_info['onair'] == "false" or $input_info['cc_error'] > "0" or $input_info['pes_error'] > "0" ) and ($input_info['channel_id'] != 0)) {
        $last_status_date = $query->assoc_array("SELECT last_problem from input where channel_id = '".$query->escape($input_info['channel_id'])."'");

        $ret = $query->result("update input set
            last_problem = now()
            where channel_id = '".$query->escape($input_info['channel_id'])."'");

        $timediff = strtotime("now") - strtotime($last_status_date['last_problem']);
        if ($timediff < 300) { //если была проблема менее чем 5 минут назад
            $channel_status_problem = $query->assoc_array("SELECT alert_status from input where channel_id = '".$query->escape($input_info['channel_id'])."'");
            if ($channel_status_problem['alert_status'] == "false") {//если инпут с проблемным статусом
                $channel_name_problem = $query->assoc_array("SELECT channel.`name`,channel.`event`,astra_instance.`name` as astraname FROM astra_instance 
                INNER JOIN channel ON astra_instance.astra_id = channel.astra_id WHERE channel.channel_id = '".$query->escape($input_info['channel_id'])."'");
                if ($channel_name_problem['event'] == "true") {
                    $problem = $query->result("update input set alert_status='true' where channel_id = '".$query->escape($input_info['channel_id'])."'");
                    //exec ("echo 'Меньше 5 минут между проблемами у канала $channel_name_problem[name]' | sendxmpp didenko@skvcom.ru");
                    require_once("includes/alerts.php");
                }
            }
        }
    }
    else if (isset($input_info['channel_id'])) {
    $channel_status_problem = $query->assoc_array("SELECT alert_status from input where channel_id = '".$query->escape($input_info['channel_id'])."'");
        if ($channel_status_problem['alert_status'] == "true") {
            $problem = $query->result("update input set alert_status='false' where channel_id = '".$query->escape($input_info['channel_id'])."'");
            $channel_name_ok = $query->assoc_array("SELECT channel.`name`,astra_instance.`name` as astraname FROM astra_instance 
            INNER JOIN channel ON astra_instance.astra_id = channel.astra_id WHERE channel.channel_id = '".$query->escape($input_info['channel_id'])."'");
            //exec ("echo 'Канал восстановлен' | sendxmpp didenko@skvcom.ru");
            require_once("includes/alerts.php");
        }
    }

//Уведомление для dvb срабатывает при повторной интерации в течении 5 минут. Проблемой считается любая ошибка или отсутствие сигнала.
    if (isset($input_info['dvb_id']) and ($input_info['status'] != "31" or $input_info['ber'] > "0" or $input_info['unc'] > "0" ) and ($input_info['dvb_id'] != 0)) {
        $dvb_status = $query->assoc_array("SELECT alert_status_dvb,last_problem_dvb from dvb_input where dvb_input_id = '".$query->escape($input_info['dvb_id'])."'");
        if ($dvb_status['alert_status_dvb'] == "false") { //если ранее не было проблем, регистрируем проблему и дату
            $ret = $query->result("update dvb_input set last_problem_dvb = now(), alert_status_dvb='true' where dvb_input_id = '".$query->escape($input_info['dvb_id'])."'");
            }
        else if ($dvb_status['alert_status_dvb'] == "true") { //если проблема повторяется, проверяем когда она началась
            $timediff = strtotime("now") - strtotime($dvb_status['last_problem_dvb']);
            if ($timediff > 120) { //если проблема длится более 120 сек
                $dvb_name_problem = $query->assoc_array("SELECT dvb_input.`name` FROM dvb_input 
                    WHERE dvb_input.dvb_input_id = '".$query->escape($input_info['dvb_id'])."'");
                require_once("includes/alerts.php");
                }
            }
        }
    else if (isset($input_info['dvb_id'])) {
    $dvb_status_problem = $query->assoc_array("SELECT alert_status_dvb from dvb_input where dvb_input_id = '".$query->escape($input_info['dvb_id'])."'");
        if ($dvb_status_problem['alert_status_dvb'] == "true") { //если были проблемы, регистрируем их отсутствие
            $problem = $query->result("update dvb_input set alert_status_dvb='false' where dvb_input_id = '".$query->escape($input_info['dvb_id'])."'");
            $dvb_name_ok = $query->assoc_array("SELECT dvb_input.`name` FROM dvb_input 
            WHERE dvb_input.dvb_input_id = '".$query->escape($input_info['dvb_id'])."'");
            //exec ("echo 'dvb ok' | sendxmpp didenko@skvcom.ru");
            require_once("includes/alerts.php");
        }
    }


    if (isset($input_info['dvb_id'])) {
        $ret_dvb = $query->result("UPDATE `dvb_input` set
            `last_update` = FROM_UNIXTIME('".$query->escape($input_info['timestamp'])."'),
            `status` =  '".$query->escape($input_info['status'])."',
            `signal` =  '".$query->escape($input_info['signal'])."',
            `snr` =  '".$query->escape($input_info['snr'])."',
            `ber` =  '".$query->escape($input_info['ber'])."',
            `unc` =  '".$query->escape($input_info['unc'])."' WHERE  `dvb_input_id` ='".$query->escape($input_info['dvb_id'])."';");
    }

//    Сообщения через xmpp/jabber
//    if (defined('XMPPLOGIN')) {
//        require_once("includes/XMPPHP/XMPP.php");
//    }
//    require_once("includes/xmpp_alerts.php");

//        $results = print_r($last_status, true);
//        exec ("echo '$results' | sendxmpp client@jabber.ar");
//        exec ("echo '$results' >> /tmp/0000011");

?>

