<?php
    function plugin_alerts_get_alerts()
    {
        $query = new db_query();
        $lock_query = new db_query();

        $lock_query->result("lock tables `lock` write");

        $query->result("select log.*, channel.name as ch_name, dvb_input.name as dvb_name from log 
            left join input using(input_id)
            left join channel using(channel_id)
            left join dvb_input on log.dvb_input_id = dvb_input.dvb_input_id
            where alert_is_sent = 0
        order by time limit 20");

        $update = Array();

        while (is_array($log = $query->fetch_assoc()))
        {
            $log['onair'] = $log['onair']=='true'?'Да':'Нет';
            $log['scrambled'] = $log['scrambled']=='true'?'Да':'Нет';

            //var_dump($log);
            switch ($log['type'])
            {
                case 'input':
                    $message = "Время: ".$log['time'].
                    "<br />Канал: ".$log['ch_name'].
                    "<br />Работает: ".$log['onair'].
                    "<br />Зашифрован: ".$log['scrambled'];
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
                    "<br />Адаптер: ".$log['dvb_name'].
                    "<br />Сигнал: ".round($log['signal']*100/65535).
                    "<br />SNR: ".round($log['snr']*100/65535).
                    "<br />Lock: ".$status[0].
                    "<br />BER: ".$log['ber'].
                    "<br />UNC: ".$log['unc'];
                    break;
                default:
                    $message='';
                    break;
            }

            echo "\$.UIkit.notify(\"".$message."\", {status:'danger'});\n";
            $update[] = $log['log_id'];
        }

        if (is_array($update)){
            foreach ($update as $log_id) {
                $query->result("update log set alert_is_sent = 1 where log_id=".$log_id);
                //echo $query->error();
            }
        }
        exit;
    }
?>
