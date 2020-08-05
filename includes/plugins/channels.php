<?php

    if(isset($data['enable']) && $data['enable']) 
    {
        $data['enable']='true';
    }else{
        $data['enable']='false';
    }

    if(isset($data['event']) && $data['event']) 
    {
        $data['event']='true';
    }else{
        $data['event']='false';
    }

    if(isset($data['keys_shar']) && $data['keys_shar']) 
    {
        $data['keys_shar']='true';
    }else{
        $data['keys_shar']='false';
    }

    if(isset($data['stalker_on']) && $data['stalker_on']) 
    {
        $data['stalker_on']='true';
    }else{
        $data['stalker_on']='false';
    }

    if(isset($data['stalker_on2']) && $data['stalker_on2']) 
    {
        $data['stalker_on2']='true';
    }else{
        $data['stalker_on2']='false';
    }

    if(isset($data['lumi_on']) && $data['lumi_on']) 
    {
        $data['lumi_on']='true';
    }else{
        $data['lumi_on']='false';
    }



    function plugin_channels_main() {
        global $data, $lang;

        $query = new db_query();

        if(isset($data['show_add_dialog']))
        {
            $channels['show_add_dialog'] = 'Y';
        }else{
            $channels['show_add_dialog'] = '';
        }

       $channels['opt1'] = OPT_1;
       $channels['opt2'] = OPT_2;
       $channels['opt3'] = OPT_3;
       $channels['opt4'] = OPT_4;
       $channels['str1'] = STR_1;

        $channels['page_reload_time'] = PAGE_RELOAD_TIME*1000;
        $data['page'] = template_parse('channels/channels_list.html',$channels);


    }

    function plugin_channels_get_channels() {
        global $data, $lang;

        $query = new db_query();
        $query->result("select channel.`name` as channel_name, channel.channel_id as chid, channel_pnr, name_rus, keys_shar, lumi_fr,
            astra_instance.name as astra_name, enable, channel.event, astra_id, stalker_on, lumi_on, stalker_on2, channel.type, LEFT(`input_0`, ".LEN_INPUT.") as input_crop, output_0,
            input.*, astra_instance.event_request as event_request, astra_instance.astra_mask as astra_mask,
            TIME_TO_SEC(timediff(now(),input.last_update)) as last_update_period,
            TIME_TO_SEC(timediff(now(),input.last_problem)) as last_problem_period
            from channel
            left join astra_instance using(astra_id)
            left join output using(channel_id)
            left join input on (channel.channel_id = input.channel_id)
            order by input.last_problem DESC,channel.channel_id, onair
        ");

        $num = 0;
        $channels = Array();
        $channels['list'] = '';

        while (is_array($channel = $query->fetch_assoc()))
        {
            $num++;
            $channel['num'] = $num;

            if ($channel['channel_id'] == '0') {continue 1;}

            $channel['time_res'] = TIME_RESP;
            if ($channel['channel_name'] == $channel['name_rus']) {$channel['name_rus'] = '';}
            else {$channel['name_rus'] = "/".$channel['name_rus'];}

            if (strlen($channel['input_crop']) == LEN_INPUT) {$channel['input_crop'].="...";}

            $channel['channel_id'] = $channel['chid'];
            $channel['bitrate'] = intval($channel['bitrate']);

            $channel['channel_buttons'] = template_parse('channels/channel_buttons.html',$channel);

            $json['data'][] = $channel;

        }

        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);  
        exit;
    }



//    function plugin_channels_delete_channel() {
//        global $data;
//
//        $query = new db_query();
//        if (intval($data['channel_id']))
//        {
//            $query->result("delete from channel where channel_id=".intval($data['channel_id']));
//        }
//        header('Location: '.$_SERVER['PHP_SELF'].'?plugin=channels',true, 303);
//        exit;
//    }

    function plugin_channels_channel_info() {
        global $data;

        if (intval($data['channel_id']))
        {
            $query = new db_query();
            $channel = $query->assoc_array("select * from channel
                where channel_id=".intval($data['channel_id']));
            htmlspecialchars_array($channel);

            $astra_select = new web_select();
            $astra_select->name='astra_id';
            $astra_select->title_column='name';
            $astra_select->value_column='astra_id';
            $astra_select->selected=$channel['astra_id'];

            $channel['enable_checked'] = $channel['enable'] == 'true'?'checked':'';
            $channel['event_checked'] = $channel['event'] == 'true'?'checked':'';
           // $channel['stalker_on_checked'] = $channel['stalker_on'] == 'true'?'checked':'';
           // $channel['stalker_on2_checked'] = $channel['stalker_on2'] == 'true'?'checked':'';
           // $channel['lumi_on_checked'] = $channel['lumi_on'] == 'true'?'checked':'';
           // $channel['keys_shar_checked'] = $channel['keys_shar'] == 'true'?'checked':'';

           // $channel['opt'] = OPT_1;
          //  $channel['opt2'] = OPT_2;
           // $channel['opt3'] = OPT_3;
          //  $channel['opt4'] = OPT_4;
          //  $channel['str1'] = STR_1;

            $query->result("select * from astra_instance order by name");
            $channel['astra_select'] = $astra_select->get_from_sql($query);

            //$data['page'] = template_parse('channels/channel_edit_form.html',$channel);

        }else{
            header('Location: '.$_SERVER['PHP_SELF'].'?plugin=channels',true, 303);
        }
    }


//    function plugin_channels_add_channel() {
//        global $data;
//
//        if (intval($data['astra_id']))
//        {
//            $query = new db_query();
//            $channel_id_min = $query->assoc_array("SELECT MIN(channel_id)+1 FROM channel AS t1 WHERE (SELECT COUNT(*) FROM channel AS t2 WHERE t2.channel_id=t1.channel_id+1)=0");
//            $query = new db_query();
//            $query->result("insert into channel set
//                astra_id=".intval($data['astra_id']).",
//                channel_id=".$channel_id_min['MIN(channel_id)+1'].",
//                name='".$query->escape($data['name'])."',
//                `enable`='".$query->escape($data['enable'])."',
//                `event`='".$query->escape($data['event'])."'");
//
//            $data['channel_id'] = $query->insert_id();
//            header('Location: '.$_SERVER['PHP_SELF'].'?plugin=channels&action=channel_info&channel_id='.intval($data['channel_id']),true, 303);
//        }else{
//            header('Location: '.$_SERVER['PHP_SELF'].'?plugin=channels',true, 303);
//        }
//
//    }
//
//    function plugin_channels_edit_channel() {
//        global $data;
//        if (intval($data['channel_id']) && intval($data['astra_id']))
//        {
//            $query = new db_query();
//            $query->result("update channel set astra_id=".intval($data['astra_id']).",
//                name='".$query->escape($data['name'])."',
//                name_rus='".$query->escape($data['name_rus'])."',
//                lumi_fr='".$query->escape($data['lumi_fr'])."',
//                channel_pnr=".intval($data['channel_pnr']).",
//                `enable`='".$query->escape($data['enable'])."',
//                `event`='".$query->escape($data['event'])."',
//                `keys_shar`='".$query->escape($data['keys_shar'])."',
//                `stalker_on`='".$query->escape($data['stalker_on'])."',
//                `stalker_on2`='".$query->escape($data['stalker_on2'])."',
//                `lumi_on`='".$query->escape($data['lumi_on'])."'
//                where channel_id=".intval($data['channel_id']));
//
//            header('Location: '.$_SERVER['PHP_SELF'].'?plugin=channels&action=channel_info&channel_id='.intval($data['channel_id']),true, 303);
//        }else{
//            header('Location: '.$_SERVER['PHP_SELF'].'?plugin=channels',true, 303);
//        }
//    }


//    function plugin_channels_get_inputs() {
//        global $data;
//        $query = new db_query();
//        $json['data'] = array();
//
//        if (intval($data['channel_id']))
//        {
//            $channel_cfg = $query->assoc_array("select * from channel
//                where channel_id=".intval($data['channel_id']));
//
//            $query->result("select input_0 from input
//                where channel_id=".intval($data['channel_id']));
//
//            while(is_array($input = $query->fetch_assoc()))
//            {
                //$input['url'] = make_input_url($input['input_id'], $channel_cfg);
                //htmlspecialchars_array($input);
                //echo json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//                $input['url'] = $input['input_0'];
//                unset($tmp);
//                $tmp[] = $input['url'];
//                $tmp[] = template_parse("channels/input_line.html", $input);
//
//                $json['data'][] = $tmp;
                //echo template_parse("channels/input_line.html", $input);
//            }
//
//        }
//        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);  
//        exit;
//
//    }

    function plugin_channels_play_channel() {
        global $data;

        $query = new db_query();
        $channel = $query->assoc_array("select * from stream where id=".intval($data['channel_id']));
        echo template_parse("channels/play.php", $channel);
        exit;
    }
    function plugin_channels_error_channel() {
        global $data;

        $query = new db_query();
        $channel = $query->assoc_array("select * from channel where channel_id=".intval($data['channel_id']));

        echo template_parse("channels/channel_error.html", $channel);
        exit;
    }

//    function plugin_channels_get_outputs() {
//        global $data;
//        $query = new db_query();
//        $json['data'] = array();
//
//        if (intval($data['channel_id']))
//        {
//            $query->result("select output_0 from output
//                where channel_id=".intval($data['channel_id']));
//
//            while(is_array($output = $query->fetch_assoc())) 
//            {
//                $output['url'] = $output['output_0'];
//
//                unset($tmp);
//                $tmp[] = $output['url'];
//                //$tmp[] = $output['output_pid'];
//                $tmp[] = template_parse("channels/output_line.html", $output);
//
//                $json['data'][] = $tmp;
//            }
//
//        }
//        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);  
//        exit;
//
//    }

//    function plugin_channels_get_astra_select() {
//        global $data;
//        $query = new db_query();
        //$ret='alert('.intval($data['input_id']).');';

//        $astra = $query->result("select * from astra_instance order by name");
//        $select = new web_select();
        //$select->options_only = true;
//        $select->name = "astra_id";
//        $select->title_column = 'name';
//        $select->value_column = 'astra_id';
//        $select->select_first = false;
//        echo $select->get_from_sql($astra);
//        exit;
//    }

  function plugin_channels_reload_channel() {
          global $data;
          if(intval($data['astra_id']) && intval($data['channel_id']))
          {
                reload_channel($data['astra_id'], intval($data['channel_id']));
          }
          header('Location: '.$_SERVER['PHP_SELF '].'?plugin=channels',true,303);
          //exit;
  }


?>
