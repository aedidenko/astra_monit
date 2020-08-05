<?php
    //Подготовка массива к выводу.
    //-----------------------------------------
    function htmlspecialchars_array(&$data)
    {
        $data=array_map('htmlspecialchars_array_callback',$data);
    }

    function htmlspecialchars_array_callback($val)
    {
        return htmlspecialchars(trim($val),ENT_QUOTES);
    }


    function ip_2_long($ip)
    {
        return sprintf("%u",ip2long($ip));
    }

    function is_ip($ip) {
        $valid = TRUE;
        $ip = explode(".", $ip);
        if(count($ip)!=4) {
            return FALSE;
        }
        foreach($ip as $key => $block) {
            if(!is_numeric($block) || $block>=255 || ((!$key || $key==3) && $block<1) ) {
                $valid = FALSE;
            }
        }
        return $valid;
    }


    function is_ip_mask($ip) {
        $valid = TRUE;

        $ip = explode(".", $ip);
        if(count($ip)!=4) {
            return FALSE;
        }
        foreach($ip as $key => $block) {
            if(!is_numeric($block) || $block > 255 || ((!$key || $key == 3) && $block < 0) ) {
                $valid = FALSE;
            }
        }
        return $valid;
    }


    function new_time($a) { // преобразовываем время в нормальный вид
        //date_default_timezone_set('Europe/Moscow');
        $ndate = date('d.m.Y', $a);
        $ndate_time = date('H:i:s', $a);
        $ndate_exp = explode('.', $ndate);
        $nmonth = array(
            1 => 'янв',
            2 => 'фев',
            3 => 'мар',
            4 => 'апр',
            5 => 'мая',
            6 => 'июн',
            7 => 'июл',
            8 => 'авг',
            9 => 'сен',
            10 => 'окт',
            11 => 'ноя',
            12 => 'дек'
        );

        foreach ($nmonth as $key => $value) {
            if($key == intval($ndate_exp[1])) $nmonth_name = $value;
        }

        if($ndate == date('d.m.Y')) return 'сегодня в '.$ndate_time;
        elseif($ndate == date('d.m.Y', strtotime('-1 day'))) return 'вчера в '.$ndate_time;
        else return $ndate_exp[0].' '.$nmonth_name.' '.$ndate_exp[2].' в '.$ndate_time;
    }

?>