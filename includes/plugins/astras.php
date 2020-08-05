<?php
  function plugin_astras_main() {
          global $data;
          
          $webdir = pathinfo($_SERVER['REQUEST_URI']);
          $astras['monitor_link'] = "http://".$_SERVER['HTTP_HOST'].$webdir['dirname']."/monitor.php";
 
          if(isset($data['show_add_dialog']))
          {
               $astras['show_add_dialog'] = 'Y';
          }else{
               $astras['show_add_dialog'] = '';
          }
          
          $data['page'] = template_parse('astras/astras_list.html',$astras);
  }
  
  function plugin_astras_get_astras() {
          global $data;
          
          $query = new db_query();
          $query->result("select * from astra_instance");
          
          $astras = Array();
          $astras['list'] = '';

          while (is_array($astra = $query->fetch_assoc()))
          {
              $query2 = new db_query();
              $query2->result("SELECT COUNT(*) FROM `channel` WHERE `astra_id`='$astra[astra_id]'");
              $channels_count_array = $query2->fetch_assoc();
              $astra['channels_count'] = $channels_count_array['COUNT(*)'];

              $astra['astra_buttons'] = template_parse('astras/astra_buttons.html', $astra);
              $json['data'][] = $astra;
          }
          echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);  
          exit;

  }


  function plugin_astras_delete_astra() {
          global $data;
          
          $query = new db_query();
          $query->result("delete from astra_instance where astra_id=".intval($data['astra_id']));
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=astras',true, 303);
          exit;
  }
  
  function plugin_astras_add_astra() {
          global $data;
          
          $query = new db_query();
          $query->result("insert into astra_instance set name = '".$query->escape($data['name'])."',
                                                 control_server_addr = '".$query->escape($data['control_server_addr'])."',
                                                 event_request = '".$query->escape($data['event_request'])."',
                                                 astra_mask = '".$query->escape($data['astra_mask'])."'");
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=astras',true, 303);
          exit;
  }
  
    function plugin_astras_edit_astra() {
          global $data;
          if(intval($data['astra_id'])) {
              $query = new db_query();
              $query->result("update astra_instance set name = '".$query->escape($data['name'])."',
                                                     control_server_addr = '".$query->escape($data['control_server_addr'])."',
                                                     event_request = '".$query->escape($data['event_request'])."',
                                                     astra_mask = '".$query->escape($data['astra_mask'])."'
                                                     where astra_id=".intval($data['astra_id']));
          }
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=astras',true, 303);
          exit;
  }
  
    function plugin_astras_edit_api() {
          global $data;
          if(intval($data['astra_id'])) {
              $query = new db_query();
              $query->result("update astra_instance set auth_api = '".$query->escape($data['auth_api'])."'
                                                     where astra_id=".intval($data['astra_id']));
          }
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=astras',true, 303);
          exit;
  }
  
  function plugin_astras_reload_astra() {
          global $data;
          if(intval($data['astra_id']))  
          {
                reload_astra($data['astra_id']);
          }
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=astras',true, 303);
          //exit;
  }
  

  
?>
