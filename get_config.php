<?php
    require_once("includes/config.php");
    require_once("includes/utils.php");
    require_once("includes/mysql.php");
    require_once("includes/channel_func.php");

    $config_request = new db_query();

    $cfg_data=$config_request->assoc_array("select * from astra_instance
        where astra_id='".intval($data['astra_id'])."'");

    if(!$cfg_data['control_server_iface'])
    {
        $cfg_data['control_server_iface'] = '0.0.0.0';
    }

    if($cfg_data['control_server_port'])
    {
        $config['control_server'] = 'http://'.$cfg_data['control_server_iface'].':'.$cfg_data['control_server_port'];
    }

    if($cfg_data['event_request'])
    {
        $config['event_request'] = $cfg_data['event_request'];
    } 

    if($cfg_data['event_request_interval'])
    {
        $config['event_request_interval'] = $cfg_data['event_request_interval'];
    }


    $config_request->result("select * from config
        where astra_id='".intval($data['astra_id'])."'");
    while(is_array($cfg_data=$config_request->fetch_assoc()))
    {
        //var_dump($cfg_data);
        if($cfg_data['value'] == 'false') $cfg_data['value'] = false;
        if($cfg_data['value'] == 'true') $cfg_data['value'] = true;

        $config[$cfg_data['name']] = $cfg_data['value'];
    }

    //echo json_encode($config, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);

    $config_request->result("select dvb_input.* from channel
        left join input using(channel_id)
        left join dvb_input using(dvb_input_id)
        where input.type='dvb' and astra_id='".intval($data['astra_id'])."'
    group by dvb_input_id");

    while(is_array($cfg_data=$config_request->fetch_assoc()))
    {
        unset($dvb);
        $dvb['id'] = 'dvb_'.$cfg_data['dvb_input_id'];
        $dvb['event'] = true;
        $dvb['adapter'] = $cfg_data['adapter'];
        if ($cfg_data['device']) $dvb['device'] = $cfg_data['device'];
        $dvb['type'] = $cfg_data['type'];
        if ($cfg_data['mac']) $dvb['mac'] = $cfg_data['mac'];
        if ($cfg_data['budget'] == 'true' ) $dvb['budget'] = true;
        if ($cfg_data['event'] == 'true') $dvb['event'] = true; else $dvb['event'] = false;
        if ($cfg_data['modulation'] ) $dvb['modulation'] = $cfg_data['modulation'];
        if ($cfg_data['fec'] ) $dvb['fec'] = $cfg_data['fec'];
        if ($cfg_data['bitrate'] ) $dvb['bitrate'] = $cfg_data['bitrate'];
        if ($cfg_data['buffer_size'] > 0 ) $dvb['buffer_size'] = $cfg_data['buffer_size'];

        switch ($cfg_data['type'])
        {
            case 'S':
                $dvb['tp'] = $cfg_data['frequency'].':'.$cfg_data['polarization'].':'.$cfg_data['symbolrate'];
                $dvb['lnb'] = $cfg_data['lof1'].':'.$cfg_data['lof2'].':'.$cfg_data['slof'];
                if($cfg_data['lnb_sharing'] == 'true') $dvb['lnb_sharing'] = true;
                if($cfg_data['tone'] == 'true') $dvb['tone'] = true;
                if($cfg_data['diseqc'] ) $dvb['diseqc'] = $cfg_data['diseqc'];
                if($cfg_data['bitrate'] ) $dvb['bitrate'] = $cfg_data['bitrate'];
                break;
            case 'S2':
                $dvb['tp'] = $cfg_data['frequency'].':'.$cfg_data['polarization'].':'.$cfg_data['symbolrate'];
                $dvb['lnb'] = $cfg_data['lof1'].':'.$cfg_data['lof2'].':'.$cfg_data['slof'];
                if($cfg_data['lnb_sharing'] == 'true') $dvb['lnb_sharing'] = true;
                if($cfg_data['tone'] == 'true') $dvb['tone'] = true;
                if($cfg_data['diseqc'] ) $dvb['diseqc'] = $cfg_data['diseqc'];
                if($cfg_data['rolloff'] ) $dvb['rolloff'] = $cfg_data['rolloff'];
                if($cfg_data['bitrate'] ) $dvb['bitrate'] = $cfg_data['bitrate'];                
                break;
            case 'T':
                $dvb['frequency'] = $cfg_data['frequency'];
                if($cfg_data['bandwidth'] ) $dvb['bandwidth'] = $cfg_data['bandwidth'];
                if($cfg_data['guardinterval'] ) $dvb['guardinterval'] = $cfg_data['guardinterval'];
                if($cfg_data['transmitmode'] ) $dvb['transmitmode'] = $cfg_data['transmitmode'];
                if($cfg_data['hierarchy'] ) $dvb['hierarchy'] = $cfg_data['hierarchy'];
                if($cfg_data['stream_id']>0 ) $dvb['stream_id'] = $cfg_data['stream_id'];
                break;
            case 'T2':
                $dvb['frequency'] = $cfg_data['frequency'];
                if($cfg_data['bandwidth'] ) $dvb['bandwidth'] = $cfg_data['bandwidth'];
                if($cfg_data['guardinterval'] ) $dvb['guardinterval'] = $cfg_data['guardinterval'];
                if($cfg_data['transmitmode'] ) $dvb['transmitmode'] = $cfg_data['transmitmode'];
                if($cfg_data['hierarchy'] ) $dvb['hierarchy'] = $cfg_data['hierarchy'];
                if($cfg_data['stream_id']>0 ) $dvb['stream_id'] = $cfg_data['stream_id'];
                break;
            case 'C':
                $dvb['frequency'] = $cfg_data['frequency'];
                $dvb['symbolrate'] = $cfg_data['symbolrate'];
                break;
            case 'ASI':
                break;
            default:
                break;
        }
        $config['dvb_tune'][]=$dvb;
    }

    $config_request->result("select newcamd.* from channel
        left join input using(channel_id)
        left join newcamd using(newcamd_id)
        where input.cam='newcamd' and astra_id='".intval($data['astra_id'])."'
        and not ISNULL(newcamd.newcamd_id)
    group by newcamd_id");

    while(is_array($cfg_data=$config_request->fetch_assoc()))
    {
        unset($cam);
        $cam['id'] = 'cam_'.$cfg_data['newcamd_id'];
        $cam['name'] = $cfg_data['name'];
        $cam['host'] = $cfg_data['host'];
        $cam['port'] = $cfg_data['port'];
        $cam['user'] = $cfg_data['user'];
        $cam['pass'] = $cfg_data['pass'];
        $cam['key'] = $cfg_data['key'];
        if ($cfg_data['timeout']) $cam['timeout'] = $cfg_data['timeout'];
        if ($cfg_data['disable_emm'] == 'true') $cam['disable_emm'] = true;

        $config['newcamd'][]=$cam;
    }

    $config_request->result("select channel_id as id, name, channel_pnr, event, stalker_on, stalker_on2, lumi_on, enable from channel
        where astra_id='".intval($data['astra_id'])."'");

    $io_request = new db_query();

    while(is_array($cfg_data=$config_request->fetch_assoc()))
    {
        $channel=$cfg_data;
        if ($cfg_data['enable'] == 'true') $channel['enable'] = true; else $channel['enable'] = false;
        if ($cfg_data['event'] == 'true') $channel['event'] = true; else $channel['event'] = false;
        if ($cfg_data['stalker_on'] == 'true') $channel['stalker_on'] = true; else $channel['stalker_on'] = false;
        if ($cfg_data['stalker_on2'] == 'true') $channel['stalker_on2'] = true; else $channel['stalker_on2'] = false;
        if ($cfg_data['lumi_on'] == 'true') $channel['lumi_on'] = true; else $channel['lumi_on'] = false;

        $io_request->result("select input_id from input
            where channel_id=".$cfg_data['id'].' order by priority');

        while(is_array($input_cfg=$io_request->fetch_assoc()))
        {
            $channel['input'][] = make_input_url($input_cfg['input_id'], $cfg_data);
        }

        $io_request->result("select * from map
            where channel_id=".$cfg_data['id']);
        $map='';
        while(is_array($map_cfg = $io_request->fetch_assoc()))
        {
            $map[] = $map_cfg['input_pid'].'='.$map_cfg['output_pid'];
        }
        if (is_array($map)) $channel['map'] = $map;

        //OUTPUT
        $io_request->result("select output_id from output
            where channel_id=".$cfg_data['id']);

        while(is_array($output_cfg=$io_request->fetch_assoc()))
        {
            $channel['output'][] = make_output_url($output_cfg['output_id']);
        }

        $config['make_channel'][] = $channel;        

    }

    echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>

