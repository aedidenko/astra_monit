<?php
if(!isset($data['scripts']) || !$data['scripts']) $data['scripts']='';
if(!isset($data['css']) || !$data['css']) $data['css']='';

//Добавить css в шаблон
function template_add_css($css_filename,&$data)
{
	$data['css'].="\n".file_get_contents(SCRIPT_DIR."/css/$css_filename")."\n";
}

//Добавить скрипт в шаблон
function template_add_script($script_filename,&$data)
{
	$data['scripts'].="\n".file_get_contents(SCRIPT_DIR."/scripts/$script_filename")."\n";
}

//Парсилка шаблона
function template_parse($template_file,&$data)
{
	$data['php_self']=$_SERVER['PHP_SELF'];
	$data['date_now']=date("d/m/Y");
	$data['time_now']=date("H:i:s");
	$data['script_www_root']=SCRIPT_WEBDIR;
    
	$template=file_get_contents(SCRIPT_DIR."/templates/".LANG."/".$template_file);
    
    foreach ($data as $key => $val)
    {
        $template = str_replace ('{'.$key.'}',$val,$template);    
    }
   
	return $template;
}

?>