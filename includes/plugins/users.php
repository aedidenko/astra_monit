<?php
  function plugin_users_main() {
          global $data;

          if(isset($data['show_add_dialog']))
          {
               $users['show_add_dialog'] = 'Y';
          }else{
               $users['show_add_dialog'] = '';
          }
          
          $data['page'] = template_parse('users/users_list.html',$users);
  }
  
  function plugin_users_get_users() {
          global $data;
          
          $query = new db_query();
          $query->result("select * from users order by user");
          
          $num = 0;
          
          $users = Array();
          $users['list'] = '';
          
          while (is_array($user = $query->fetch_assoc()))
          {
              $num++;  
              $user['num'] = $num;
              
              $user['user_buttons'] = template_parse('users/user_buttons.html',$user);
              $json['data'][] = $user;
          }
          
          echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);  
          exit;
  }
  
  
  function plugin_users_delete_user() {
          global $data;
          
          $query = new db_query();
          $query->result("delete from users where user_id=".intval($data['user_id']));
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=users',true, 303);
          exit;
  }
  
  function plugin_users_add_user() {
          global $data;
          
          $query = new db_query();
          $query->result("insert into users set user = '".$query->escape($data['user'])."',
                                                password = '".$query->escape($data['password'])."'");
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=users',true, 303);
          exit;
  }
  
  function plugin_users_edit_user() {
          global $data;
          if(intval($data['user_id']))         
          {
              $query = new db_query();
              $query->result("update users set user = '".$query->escape($data['user'])."',
                                               password = '".$query->escape($data['password'])."'
                                               where user_id=".intval($data['user_id']));
          }
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=users',true, 303);
          exit;
  }
  
  
  
?>
