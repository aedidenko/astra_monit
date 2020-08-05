<?php
  function plugin_camservers_main() {
          global $data, $lang;

          if(isset($data['show_add_dialog']))
          {
               $camservers['show_add_dialog'] = 'Y';
          }else{
               $camservers['show_add_dialog'] = '';
          }
          
          $data['page'] = template_parse('camservers/servers_list.html',$camservers);
  }
  
  
   function plugin_camservers_get_servers() {
          global $data, $lang;
          
          $query = new db_query();
          $query->result("select * from newcamd");

          $camservers = Array();
          $camservers['list'] = '';
          
          while (is_array($server = $query->fetch_assoc()))
          {

              $server['disable_emm_text'] = $server['disable_emm']=='false'?$lang[LANG]['Yes']:$lang[LANG]['No'];

              $server['server_buttons'] = template_parse('camservers/server_buttons.html', $server);;
              $json['data'][] = $server;
          }
          
          echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);  
          exit;
  }
  
  
  function plugin_camservers_delete_server() {
          global $data;
          
          $query = new db_query();
          $query->result("delete from newcamd where newcamd_id=".intval($data['newcamd_id']));
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=camservers',true, 303);
          exit;
  }
  
  function plugin_camservers_add_server() {
          global $data;
          
          $query = new db_query();
          $query->result("insert into newcamd set name = '".$query->escape($data['name'])."', 
                                                 host = '".$query->escape($data['host'])."',
                                                 port = '".$query->escape($data['port'])."',
                                                 user = '".$query->escape($data['user'])."',
                                                 pass = '".$query->escape($data['pass'])."',
                                                 timeout = '".$query->escape($data['timeout'])."',
                                                 `key` = '".$query->escape($data['key'])."',
                                                 disable_emm = '".$query->escape($data['disable_emm'])."'");
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=camservers',true, 303);
          exit;
  }
  
  function plugin_camservers_edit_server() {
          global $data;
          
          $query = new db_query();
          if (intval($data['newcamd_id']))
          {
              $query->result("update newcamd set name = '".$query->escape($data['name'])."', 
                                                     host = '".$query->escape($data['host'])."',
                                                     port = '".$query->escape($data['port'])."',
                                                     user = '".$query->escape($data['user'])."',
                                                     pass = '".$query->escape($data['pass'])."',
                                                     timeout = '".$query->escape($data['timeout'])."',
                                                     `key` = '".$query->escape($data['key'])."',
                                                     disable_emm = '".$query->escape($data['disable_emm'])."'
                                                     where newcamd_id=".intval($data['newcamd_id']));
          }
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=camservers',true, 303);
          exit;
  }
  
  
?>
