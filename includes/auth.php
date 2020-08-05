<?php
      $auth_query = new db_query();
      $users = $auth_query->assoc_array("select count(*) as users from users");

      if ($users['users']) {
          if ( $_SERVER['PHP_AUTH_USER'] && $_SERVER['PHP_AUTH_PW'] ) {

                $users = $auth_query->assoc_array("select * from users where
                                                            user = '".$auth_query->escape($_SERVER['PHP_AUTH_USER'])."' and
                                                            password = '".$auth_query->escape($_SERVER['PHP_AUTH_PW'])."'");
                if (!is_array($users))
                {
                      auth_reply();
                }
          }else{
                auth_reply();
          }
      }

      function auth_reply()
      {
             Header("WWW-Authenticate: Basic realm=\"Restricted\"");
             Header("HTTP/1.0 401 Unauthorized");
             echo("<HTML>\n<HEAD>\n<TITLE>Не авторизован!!!</TITLE>\n</HEAD>\n\n<BODY>\n\n<center><font color=red><b>Не авторизован!!!</b></font></center><br><br><br>\n\n\n\n\n\n</BODY>\n</HTML>");
             sleep(1);
             exit;
      }
?>
