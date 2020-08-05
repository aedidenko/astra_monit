<?php

  function plugin_adapters_main() {
          global $data;
          if(isset($data['show_add_dialog']))
          {
              $adapters['show_add_dialog'] = 'Y';
          }else{
              $adapters['show_add_dialog'] = '';
          }
          $data['page'] = template_parse('adapters/adapters_list.html',$adapters);
  }
  
  
  function plugin_adapters_get_adapters() {
          global $data;
          
          $query = new db_query();
          $query->result("select dvb_input.*, TIME_TO_SEC(timediff(now(),dvb_input.last_update)) as last_update_period
                                 from dvb_input group by dvb_input_id");
          
          $num = 0;
          
          $adapters = Array();
          $adapters['list'] = '';
          
          while (is_array($adapter = $query->fetch_assoc()))
          {
              $num++;  
              $adapter['num'] = $num;
              //$adapter['delete_display'] = $adapter['input_id']?'display: none':'';
              
              //Transform to %
              $adapter['signal'] = round($adapter['signal']*100/65535);
              $adapter['snr'] = round($adapter['snr']*100/65535);
              
              //Status
              $status = decbin($adapter['status']);
              $status = substr("00000",0,5 - strlen($status)) . $status;
              $status = str_split($status);
              
                                      
              $adapter['status_sig'] = status_img($status[4]);
              $adapter['status_carr'] = status_img($status[3]);
              $adapter['status_fec'] = status_img($status[2]);
              $adapter['status_sync'] = status_img($status[1]);
              $adapter['status_lock'] = status_img($status[0]);

               $adapter['last_update'] = new_time(strtotime( $adapter['last_update']));
              
              $adapter['adapter_buttons'] = template_parse('adapters/adapter_buttons.html', $adapter);;
              $json['data'][] = $adapter;
          }
          
          echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);  
          exit;

  }
  
  function status_img($in) {
       if ($in == 1) {$img = '<i class="uk-icon-dot-circle-o uk-icon-small" style="color: green" ></i>';}
       if ($in == 0) {$img = '<i class="uk-icon-circle-o uk-icon-small" style="color: red" ></i>';}
       return $img;
  }
  
  function plugin_adapters_delete_adapter() {
          global $data;
          
          $query = new db_query();
          
          
          $query->result("delete from dvb_input where dvb_input_id=".intval($data['dvb_input_id']));    
          
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=adapters',true, 303);
          exit;
  }
  
  function plugin_adapters_add_adapter() {
          global $data;
          
          $query = new db_query();
          
          $data['lnb_sharing']=isset($data['lnb_sharing'])?'false':'true';
          $data['tone']=isset($data['tone'])?'true':'false';
          $data['budget']=isset($data['budget'])?'true':'false';
          $data['no_sdt']=isset($data['no_sdt'])?'true':'false';
          $data['no_eit']=isset($data['no_eit'])?'true':'false';
         
          switch ($data['type']) {
              case 'S':
                $query->result("insert into dvb_input set name = '".$query->escape($data['name'])."',
                                       type = 'S',
                                       adapter = '".intval($data['adapter'])."',
                                       device = '".intval($data['device'])."',
                                       mac = '".$query->escape($data['mac'])."',
                                       budget = '".$query->escape($data['budget'])."',
                                       buffer_size = '".intval($data['buffer_size'])."',
                                       no_sdt = '".$query->escape($data['no_sdt'])."',
                                       no_eit = '".$query->escape($data['no_eit'])."',
                                       pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                       pass_eit = '".$query->escape($data['pass_eit'])."',
                                       modulation = '".$query->escape($data['modulation'])."',
                                       fec = '".$query->escape($data['fec'])."',
                                       frequency = '".intval($data['frequency'])."',
                                       polarization = '".$query->escape($data['polarization'])."',
                                       symbolrate = '".intval($data['symbolrate'])."',
                                       lof1 = '".intval($data['lof1'])."',
                                       lof2 = '".intval($data['lof2'])."',
                                       slof = '".intval($data['slof'])."',
                                       bitrate = '".intval($data['bitrate'])."',
                                       lnb_sharing = '".$query->escape($data['lnb_sharing'])."',
                                       tone = '".$query->escape($data['tone'])."',
                                       diseqc = '".intval($data['diseqc'])."'");
                break;
              case 'S2':
                   $query->result("insert into dvb_input set name = '".$query->escape($data['name'])."',
                                       type = 'S2',
                                       adapter = '".intval($data['adapter'])."',
                                       device = '".intval($data['device'])."',
                                       mac = '".$query->escape($data['mac'])."',
                                       budget = '".$query->escape($data['budget'])."',
                                       buffer_size = '".intval($data['buffer_size'])."',
                                       modulation = '".$query->escape($data['modulation'])."',
                                       fec = '".$query->escape($data['fec'])."',
                                       frequency = '".intval($data['frequency'])."',
                                       polarization = '".$query->escape($data['polarization'])."',
                                       symbolrate = '".intval($data['symbolrate'])."',
                                       lof1 = '".intval($data['lof1'])."',
                                       lof2 = '".intval($data['lof2'])."',
                                       slof = '".intval($data['slof'])."',
                                       bitrate = '".intval($data['bitrate'])."',
                                       lnb_sharing = '".$query->escape($data['lnb_sharing'])."',
                                       tone = '".$query->escape($data['tone'])."',
                                       no_sdt = '".$query->escape($data['no_sdt'])."',
                                       no_eit = '".$query->escape($data['no_eit'])."',
                                       pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                       pass_eit = '".$query->escape($data['pass_eit'])."',
                                       diseqc = '".intval($data['diseqc'])."',
                                       rolloff = '".$query->escape($data['rolloff'])."'");
                break;
              case 'T':
                   $query->result("insert into dvb_input set name = '".$query->escape($data['name'])."',
                                       type = 'T',
                                       adapter = '".intval($data['adapter'])."',
                                       device = '".intval($data['device'])."',
                                       mac = '".$query->escape($data['mac'])."',
                                       budget = '".$query->escape($data['budget'])."',
                                       buffer_size = '".intval($data['buffer_size'])."',
                                       no_sdt = '".$query->escape($data['no_sdt'])."',
                                       no_eit = '".$query->escape($data['no_eit'])."',
                                       pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                       pass_eit = '".$query->escape($data['pass_eit'])."',
                                       modulation = '".$query->escape($data['modulation'])."',
                                       fec = '".$query->escape($data['fec'])."',
                                       frequency = '".intval($data['frequency'])."',
                                       bandwidth = '".$query->escape($data['bandwidth'])."',
                                       guardinterval = '".$query->escape($data['guardinterval'])."',
                                       transmitmode = '".$query->escape($data['transmitmode'])."',
                                       hierarchy = '".$query->escape($data['hierarchy'])."'");
                break;
              case 'T2':
                   $query->result("insert into dvb_input set name = '".$query->escape($data['name'])."',
                                       type = 'T2',
                                       adapter = '".intval($data['adapter'])."',
                                       device = '".intval($data['device'])."',
                                       mac = '".$query->escape($data['mac'])."',
                                       budget = '".$query->escape($data['budget'])."',
                                       buffer_size = '".intval($data['buffer_size'])."',
                                       no_sdt = '".$query->escape($data['no_sdt'])."',
                                       no_eit = '".$query->escape($data['no_eit'])."',
                                       pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                       pass_eit = '".$query->escape($data['pass_eit'])."',
                                       modulation = '".$query->escape($data['modulation'])."',
                                       fec = '".$query->escape($data['fec'])."',
                                       frequency = '".intval($data['frequency'])."',
                                       bandwidth = '".$query->escape($data['bandwidth'])."',
                                       stream_id = '".$query->escape($data['stream_id'])."',
                                       guardinterval = '".$query->escape($data['guardinterval'])."',
                                       transmitmode = '".$query->escape($data['transmitmode'])."',
                                       hierarchy = '".$query->escape($data['hierarchy'])."'");

                break;
              case "C":
                   $query->result("insert into dvb_input set name = '".$query->escape($data['name'])."',
                                       type = 'C',
                                       adapter = '".intval($data['adapter'])."',
                                       device = '".intval($data['device'])."',
                                       mac = '".$query->escape($data['mac'])."',
                                       budget = '".$query->escape($data['budget'])."',
                                       buffer_size = '".intval($data['buffer_size'])."',
                                       no_sdt = '".$query->escape($data['no_sdt'])."',
                                       no_eit = '".$query->escape($data['no_eit'])."',
                                       pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                       pass_eit = '".$query->escape($data['pass_eit'])."',                                       
                                       modulation = '".$query->escape($data['modulation'])."',
                                       fec = '".$query->escape($data['fec'])."',
                                       frequency = '".intval($data['frequency'])."',
                                       symbolrate = '".intval($data['symbolrate'])."'");

                break;
              case 'ASI':
                $query->result("insert into dvb_input set name = '".$query->escape($data['name'])."',
                                      type = 'ASI',
                                      adapter = '".intval($data['adapter'])."',
                                      device = '".intval($data['device']).",
                                      budget = '".$query->escape($data['budget'])."',
                                      buffer_size = '".intval($data['buffer_size'])."',
                                      pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                      pass_eit = '".$query->escape($data['pass_eit'])."',
                                      no_sdt = '".$query->escape($data['no_sdt'])."',
                                      no_eit = '".$query->escape($data['no_eit'])."'");
                
                break;
          }
          
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=adapters',true, 303);
          exit;
  }
  
  function plugin_adapters_edit_adapter() {
          global $data;
          
          if(intval($data['dvb_input_id']))         
          {
              $data['lnb_sharing']=isset($data['lnb_sharing'])?'false':'true';
              $data['tone']=isset($data['tone'])?'true':'false';
              $data['budget']=isset($data['budget'])?'true':'false';
              $data['no_sdt']=isset($data['no_sdt'])?'true':'false';
              $data['no_eit']=isset($data['no_eit'])?'true':'false';
              $data['pass_sdt']=isset($data['pass_sdt'])?'true':'false';
              $data['pass_eit']=isset($data['pass_eit'])?'true':'false';


              $query = new db_query();

              switch ($data['type']) {
                  case 'S':
                    $query->result("update dvb_input set name = '".$query->escape($data['name'])."',
                                           type = 'S',
                                           adapter = '".intval($data['adapter'])."',
                                           device = '".intval($data['device'])."',
                                           mac = '".$query->escape($data['mac'])."',
                                           budget = '".$query->escape($data['budget'])."',
                                           buffer_size = '".intval($data['buffer_size'])."',
                                           modulation = '".$query->escape($data['modulation'])."',
                                           fec = '".$query->escape($data['fec'])."',
                                           frequency = '".intval($data['frequency'])."',
                                           polarization = '".$query->escape($data['polarization'])."',
                                           symbolrate = '".intval($data['symbolrate'])."',
                                           lof1 = '".intval($data['lof1'])."',
                                           lof2 = '".intval($data['lof2'])."',
                                           slof = '".intval($data['slof'])."',
	                                   bitrate = '".intval($data['bitrate'])."',
                                           lnb_sharing = '".$query->escape($data['lnb_sharing'])."',
                                           tone = '".$query->escape($data['tone'])."',
                                           no_sdt = '".$query->escape($data['no_sdt'])."',
                                           no_eit = '".$query->escape($data['no_eit'])."',
                                           pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                           pass_eit = '".$query->escape($data['pass_eit'])."',
                                           diseqc = '".intval($data['diseqc'])."' where dvb_input_id=".intval($data['dvb_input_id']));
                    break;
                  case 'S2':
                       $query->result("update dvb_input set name = '".$query->escape($data['name'])."',
                                           type = 'S2',
                                           adapter = '".intval($data['adapter'])."',
                                           device = '".intval($data['device'])."',
                                           mac = '".$query->escape($data['mac'])."',
                                           budget = '".$query->escape($data['budget'])."',
                                           buffer_size = '".intval($data['buffer_size'])."',
                                           modulation = '".$query->escape($data['modulation'])."',
                                           fec = '".$query->escape($data['fec'])."',
                                           frequency = '".intval($data['frequency'])."',
                                           polarization = '".$query->escape($data['polarization'])."',
                                           symbolrate = '".intval($data['symbolrate'])."',
                                           lof1 = '".intval($data['lof1'])."',
                                           lof2 = '".intval($data['lof2'])."',
                                           slof = '".intval($data['slof'])."',
                                           bitrate = '".intval($data['bitrate'])."',
                                           lnb_sharing = '".$query->escape($data['lnb_sharing'])."',
                                           tone = '".$query->escape($data['tone'])."',
                                           pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                           pass_eit = '".$query->escape($data['pass_eit'])."',
                                           no_sdt = '".$query->escape($data['no_sdt'])."',
                                           no_eit = '".$query->escape($data['no_eit'])."',
                                           diseqc = '".intval($data['diseqc'])."',
                                           rolloff = '".$query->escape($data['rolloff'])."' where dvb_input_id=".intval($data['dvb_input_id']));
                    break;
                  case 'T':
                       $query->result("update dvb_input set name = '".$query->escape($data['name'])."',
                                           type = 'T',
                                           adapter = '".intval($data['adapter'])."',
                                           device = '".intval($data['device'])."',
                                           mac = '".$query->escape($data['mac'])."',
                                           budget = '".$query->escape($data['budget'])."',
                                           buffer_size = '".intval($data['buffer_size'])."',
                                           modulation = '".$query->escape($data['modulation'])."',
                                           fec = '".$query->escape($data['fec'])."',
                                           frequency = '".intval($data['frequency'])."',
                                           pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                           pass_eit = '".$query->escape($data['pass_eit'])."',
                                           no_sdt = '".$query->escape($data['no_sdt'])."',
                                           no_eit = '".$query->escape($data['no_eit'])."',
                                           bandwidth = '".$query->escape($data['bandwidth'])."',
                                           guardinterval = '".$query->escape($data['guardinterval'])."',
                                           transmitmode = '".$query->escape($data['transmitmode'])."',
                                           hierarchy = '".$query->escape($data['hierarchy'])."' where dvb_input_id=".intval($data['dvb_input_id']));
                    break;
                  case 'T2':
                       $query->result("update dvb_input set name = '".$query->escape($data['name'])."',
                                           type = 'T2',
                                           adapter = '".intval($data['adapter'])."',
                                           device = '".intval($data['device'])."',
                                           mac = '".$query->escape($data['mac'])."',
                                           budget = '".$query->escape($data['budget'])."',
                                           buffer_size = '".intval($data['buffer_size'])."',
                                           modulation = '".$query->escape($data['modulation'])."',
                                           fec = '".$query->escape($data['fec'])."',
                                           frequency = '".intval($data['frequency'])."',
                                           pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                           pass_eit = '".$query->escape($data['pass_eit'])."',
                                           no_sdt = '".$query->escape($data['no_sdt'])."',
                                           no_eit = '".$query->escape($data['no_eit'])."',
                                           bandwidth = '".$query->escape($data['bandwidth'])."',
                                           stream_id = '".$query->escape($data['stream_id'])."',
                                           guardinterval = '".$query->escape($data['guardinterval'])."',
                                           transmitmode = '".$query->escape($data['transmitmode'])."',
                                           hierarchy = '".$query->escape($data['hierarchy'])."' where dvb_input_id=".intval($data['dvb_input_id']));

                    break;
                  case "C":
                       $query->result("update dvb_input set name = '".$query->escape($data['name'])."',
                                           type = 'C',
                                           adapter = '".intval($data['adapter'])."',
                                           device = '".intval($data['device'])."',
                                           mac = '".$query->escape($data['mac'])."',
                                           budget = '".$query->escape($data['budget'])."',
                                           buffer_size = '".intval($data['buffer_size'])."',
                                           modulation = '".$query->escape($data['modulation'])."',
                                           fec = '".$query->escape($data['fec'])."',
                                           frequency = '".intval($data['frequency'])."',
                                           pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                           pass_eit = '".$query->escape($data['pass_eit'])."',
                                           no_sdt = '".$query->escape($data['no_sdt'])."',
                                           no_eit = '".$query->escape($data['no_eit'])."',
                                           symbolrate = '".intval($data['symbolrate'])."' where dvb_input_id=".intval($data['dvb_input_id']));

                    break;
                  case 'ASI':
                    $query->result("update dvb_input set name = '".$query->escape($data['name'])."',
                                          type = 'ASI',
                                          adapter = '".intval($data['adapter'])."',
                                          device = '".intval($data['device']).",
                                          buffer_size = '".intval($data['buffer_size'])."',
                                          pass_sdt = '".$query->escape($data['pass_sdt'])."',
                                          pass_eit = '".$query->escape($data['pass_eit'])."',
                                          no_sdt = '".$query->escape($data['no_sdt'])."',
                                          no_eit = '".$query->escape($data['no_eit'])."',
                                          budget = '".$query->escape($data['budget'])."' where dvb_input_id=".intval($data['dvb_input_id']));
                    
                    break;
              }

              
          }
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=adapters',true, 303);
          exit;
  }
  
  
  function plugin_adapters_get_adapter_params() {
          global $data;
          
          $query = new db_query();
          //$ret='alert('.intval($data['dvb_input_id']).');';
          
          $adapter = $query->assoc_array("select * from dvb_input where dvb_input_id=".intval($data['dvb_input_id']));
          
          $ret='';
          
          if(is_array($adapter))
          {
              if( $adapter['buffer_size']<1 ) $adapter['buffer_size']='';
              if( !$adapter['diseqc'] )  $adapter['diseqc']='';
              if( !$adapter['device'] )  $adapter['device']='';
              if( $adapter['lnb_sharing'] == 'true' )
              {
                   $adapter['lnb_sharing']='false';
              }else{
                   $adapter['lnb_sharing']='true';
              }
              
              foreach($adapter as $key => $value )
              {
                  
                  if($value == 'true' || $value == 'false')
                  {
                      if ($value == 'true') {
                          $ret.="\$('#adapter-edit-form #".$key."').prop('checked', true);\n";    
                      }else{
                          $ret.="\$('#adapter-edit-form #".$key."').prop('checked', false);\n";    
                      }
                  }else{
                      $ret.="\$('#adapter-edit-form #".$key."').val('".$value."');\n";    
                  }
                  //$ret.='alert("'.$key.'");';
              }
          }
          echo $ret;
          exit;
  }
  
  
?>
