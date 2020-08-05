<?php
  if ( defined('DEVINO_LOGIN')) {
  
        $query = new db_query();
        $lock_query = new db_query();
        
        $lock_query->result("lock tables `lock` write");
        
        $query->result("select log.*, send_sms from log left join channels using(server,output)
                               where sms_is_sent = 0 and send_sms = 1
                               order by time limit 20");
  
        $update = Array();
  
        if ($query->affected_rows() > 0 ) {
           $sms = new DevinoSMS();
           $phones = array(DEVINO_PHONE);
        }
  
        while (is_array($log = $query->fetch_assoc()))
        {
            $log['ready'] = $log['ready']?'Да':'Нет';
            $log['scrambled'] = $log['scrambled']?'Да':'Нет';
            $log['cam'] = $log['cam']?'Да':'Нет';
            $log['keys'] = $log['keys']?'Да':'Нет';
            
            $message = "Время: ".$log['time'].
                       "\nКанал: ".$log['channel'].
                       "\nСервер: ".$log['server'].
                       "\nРаботает: ".$log['ready'].
                       "\nЗашифрован: ".$log['scrambled'].
                       "\nCAM: ".$log['cam'].
                       "\nКлючи: ".$log['keys'];
            
            
            if ($log['send_sms'] == 0) {
                $update[] = $log['log_id'];
            }elseif($sms->Send(DEVINO_FROM,$phones,$message))
            {
                $update[] = $log['log_id'];
            }
        }
        
        if (is_array($update)){
            foreach ($update as $log_id) {
                $query->result("update log set sms_is_sent = 1 where log_id=".$log_id);
            }
        }
                
  }else{
      $query = new db_query(); 
      $query->result("update log set sms_is_sent = 1");
  }
  

?>
