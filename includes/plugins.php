<?php
  
    //$plugins = array('channels','adapters','users', 'playlist', 'camservers');
    //$plugins = array('channels','users', 'camservers', 'adapters','astras','alerts');
    $plugins = array('channels','users', 'camservers', 'adapters','astras');
    
    foreach ($plugins as $plugin)
    {
        require_once('plugins/'.$plugin.'.php');
    }
        
    $data['plugin']=isset($data['plugin'])?$data['plugin']:'channels';
    $data['action']=isset($data['action'])?$data['action']:'';
    
    
    $plugin_main='plugin_'.$data['plugin'].'_'.$data['action'];
    
   
    if(function_exists($plugin_main))
    {
           $plugin_main();
    }else{
           $plugin_main='plugin_'.$data['plugin'].'_main';
           if(function_exists($plugin_main))
           {
                $plugin_main();
           }else{
                $data['page'] = 'Ошибка';
           }
    }
  
?>
