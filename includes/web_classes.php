<?php

//////////////////////////////////////////////////////////////////////////////
///Класс формирования селектов
//////////////////////////////////////////////////////////////////////////////
class web_select {
    public $name = 'select';
    public $value_column = 'value';
    public $title_column = 'title';
    public $selected = '';
    public $select_first = 1;
    public $array = Array();
//
    public $style = '';
    public $options_only = false;

    function web_select($name='',
                        $value_column='',
                        $title_column='',
                        $selected='' )
    {
        $this->name = $name?$name:$this->name;
        $this->value_column = $value_column?$value_column:$this->value_column;
        $this->title_column = $title_column?$title_column:$this->title_column;
        $this->selected = $selected?$selected:$this->selected;
    }

    function get_from_sql(&$db_query)
    {
        $select='';
        if (!$this->options_only) $select="<select name='$this->name' id='$this->name' style='$this->style'>\n";
        $first=1;
        while (is_array($opt = $db_query->fetch_assoc() ))
        {
            prepare_array_to_html($opt);
            if ($this->select_first && $first && !$this->selected) {
              $this->selected=$opt[$this->value_column];
              $first=0;
            }
            $select.="<option ".($opt[$this->value_column]==$this->selected?' selected ':'').
                     "value='{$opt[$this->value_column]}'>{$opt[$this->title_column]}</option>\n";
        }                                 
        if (!$this->options_only) $select.="</select>\n";
        return $select;
    }

    function get_from_array($array){
        $array=is_array($array)?$array:$this->array;

        if (!$this->options_only) $select="<select name='$this->name' id='$this->name' style='$this->style'>\n";
        $first=1;
        foreach ($array as $key =>$value)
        {
              htmlspecialchars(chop($value),ENT_QUOTES);
              htmlspecialchars(chop($key),ENT_QUOTES);
              if ($this->select_first && $first && !$this->selected) {
              $this->selected=$key;
              $first=0;
            }
              $select.="<option ".($key==$this->selected?' selected ':'').
                     "value='$key'>$value</option>\n";
        }
        if (!$this->options_only) $select.="</select>\n";
        return $select;
    }
   
    function get_numeric_select($min, $max)
    {
        if (!$this->options_only) $select="<select name='$this->name' id='$this->name' style='$this->style'>\n";
        for($i=$min;$i<=$max;$i++){
            $select.="<option ".($i==$this->selected?' selected ':'').
                     "value='$i'>".($i==0?ANY:$i)."</option>\n";
        }
        if (!$this->options_only) $select.="</select>\n";
        return $select;
    } 
}


function prepare_array_to_html(&$array)
{
    foreach($array as $key => $value)
    {
        $array[$key]=htmlspecialchars(trim($value),ENT_QUOTES);
    }
    return $array;
}

function prepare_string_to_html(&$string)
{
    $string=htmlspecialchars(trim($string),ENT_QUOTES);
    return $string;
}

?>