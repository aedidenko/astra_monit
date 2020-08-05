<?php
    function make_output_url_short($channel_id){

        if(!$channel_id) return;
        $query = new db_query();
        $output='';
        $output_cfg = $query->assoc_array("select output_0 from output
            where channel_id=".$channel_id);

        $output = $output_cfg['output_0'];
        if (strpos($output, 'udp') !== false) {
            $output = preg_replace('/udp.*\@/', 'udp://@', $output);
            $output = preg_replace('/\#.*$/', '', $output);
        }
        return $output;
    }


    function make_input_url($input_id, $channel_cfg = array('channel_pnr' => 0 ))
    {
        if(!$input_id) return;

        $query = new db_query();
        $filter_request = new db_query();

        $input_cfg = $query->assoc_array("select input_0
            where input_id=".$input_id);
        return $input;
    }

    function make_output_url($output_id){

        if(!$output_id) return;
        $query = new db_query();
        $output = '';
        $output_cfg = $query->assoc_array("select output_0 from output
            where output_id=".$output_id);
        return $output;
    }

?>
