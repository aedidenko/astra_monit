<?php

class db_query {
    private $link;
    public $res;
    private $host;
    private $user;
    private $password;
    private $db;

    function result($query)
    {
        if(!$this->link || !mysqli_ping($this->link))
        {
            $this->connect();
        }

        //       echo ($query);
        $this->res = mysqli_query($this->link, $query) or trigger_error(mysql_error()." in ". $query);;
        return $this->res;
    }
    
    function escape($string)
    {
        return mysqli_escape_string($this->link,$string);
    }

    function data_seek($row)
    {
        return mysqli_data_seek($this->res, $row);
    }

    function assoc_array($query)
    {
        if ($this->res = $this->result($query))
        {
              $ret = mysqli_fetch_assoc($this->res);
              return $ret;
        }
        return false;
    }

    function fetch_assoc()
    {
        return mysqli_fetch_assoc($this->res);
    }

    function affected_rows()
    {
        return mysqli_affected_rows($this->link);
    }

    function error()
    {
        return mysqli_error($this->link);
    }

    function insert_id()
    {
        return mysqli_insert_id($this->link);
    }

    function client_encoding()
    {
        return mysqli_client_encoding($this->link);
    }

    private function connect()
    {
          $this->link = mysqli_connect($this->host,$this->user,$this->password,$this->db);
          if (!$this->link)
          {
            echo "Ошибка соединится с сервером!!!";
            exit;
          }

          mysqli_set_charset($this->link,"utf8");
          @mysqli_query($this->link, "SET NAMES 'utf8'");
          //mysqli_query($this->link, "SET CHARACTER SET utf8");
    }

    function __construct($host = SQL_HOST, $user = SQL_USER, $password = SQL_PASSWORD, $db = SQL_DB) {
          if (!$this->link)
          {
              $this->host = $host;
              $this->user = $user;
              $this->password = $password;
              $this->db = $db;  
              $this->connect();
         }
    }

    function __destruct() {
          //if ($this->link)
          //{
          //      mysqli_close($this->link);
          //}

          if ($this->res)
          {
                @mysqli_free_result($this->res);
          }
    }

};

?>