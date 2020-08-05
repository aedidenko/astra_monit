<?php
    if ( defined('XMPPLOGIN')) {

        $query = new db_query();
        $lock_query = new db_query();

        $lock_query->result("lock tables `lock` write");

        $query->result("select log.*, channel.name as ch_name, dvb_input.name as dvb_name from log 
            left join input using(input_id)
            left join channel using(channel_id)
            left join dvb_input on log.dvb_input_id = dvb_input.dvb_input_id
            where xmpp_is_sent = 0
        order by time limit 20");

        $update = Array();

        if ($query->affected_rows() > 0) {
            $conn = new XMPPHP_XMPP(XMPPHOST, XMPPPORT, XMPPLOGIN, XMPPPASS, 'xmpphp', XMPPDOMAIN, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
            try {
                $conn->connect();
                $conn->processUntil('session_start');
                $conn->presence();
            }catch(XMPPHP_Exception $e) {
                $query->result("UNLOCK TABLES");  
                die($e->getMessage());
            }
        }

        while (is_array($log = $query->fetch_assoc()))
        {
            $log['onair'] = $log['onair']=='true'?'Да - *OK*':'Нет - *HELP*';
            $log['scrambled'] = $log['scrambled']=='true'?'Да - ]:->':'Нет - 8-)';

            switch ($log['type'])
            {
                case 'input':
                    $message = "Время: ".$log['time'].
                    "\nКанал: ".$log['ch_name'].
                    "\nРаботает: ".$log['onair'].
                    "\nЗашифрован: ".$log['scrambled'];
                    break;
                case 'dvb':

                    //Status
                    $status = decbin($log['status']);
                    $status = substr("00000",0,5 - strlen($status)) . $status;
                    $status = str_split($status);

                    /*
                    $adapter['status_sig'] = status_img($status[4]);
                    $adapter['status_carr'] = status_img($status[3]);
                    $adapter['status_fec'] = status_img($status[2]);
                    $adapter['status_sync'] = status_img($status[1]);
                    $adapter['status_lock'] = status_img($status[0]);
                    */

                    $message = "Время: ".$log['time'].
                    "\nАдаптер: ".$log['dvb_name'].
                    "\nСигнал: ".round($log['signal']*100/65535).
                    "\nSNR: ".round($log['snr']*100/65535).
                    "\nLock: ".$status[0].
                    "\nBER: ".$log['ber'].
                    "\nUNC: ".$log['unc'];
                    break;
                default:
                break;
            }

            try {
                $conn->message(XMPPALERTJID, $message);
                $update[] = $log['log_id'];
            }catch(XMPPHP_Exception $e) {
                //die($e->getMessage());
            }
        }

        if (is_array($update)){
            foreach ($update as $log_id) {
                $query->result("update log set xmpp_is_sent = 1 where log_id=".$log_id);
                //echo $query->error();
            }
        }

        if (isset($conn) && is_object($conn)) $conn->disconnect();
    }else{
        $query = new db_query(); 
        $query->result("update log set xmpp_is_sent = 1");
    }


?>
