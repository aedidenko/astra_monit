<?php

//Можно создать conf.php в корне мониторинга, в который поместить любые из настроек, для более удобного обновления.
//При добавлении в него настроек, заменить "def" на "define".
if (file_exists("conf.php")) {
    include_once("conf.php");
}

function def($const,$value) {
    if (!defined($const)) {
        define($const, $value);
    }
}

////////////////////////////////////////////////////////////////////////////

    //Параметры для подключения к базе
    def('SQL_HOST','172.18.0.11');
    def('SQL_DB','astra');
    def('SQL_USER','root');
    def('SQL_PASSWORD','2207811');

    //API_KEY для отправки уведомлений в pushbullet. Если больше одного, указывать через пробел. Раскомментировать для отправки (должен быть установлен php5-curl)
    //def('API_KEY','123123123qweqweqwe F.D78sadsdf98d7f');

    //Расскомментируйте для отправлки уведомлений в telegram. Настройки в includes/telegam/config.py
    //def('TELEGRAM','telegram true');


    //Внешний вид
    def('LEN_INPUT','28');//Длина строки "Вход" в таблице
    def('TIME_RESP','300');//Время, через которое изменится время канала на красное, если не приходит статистика (в секундах).
    def('LANG','ru');//Язык для выбора шаблона
    def('PAGE_RELOAD_TIME','300');//Время автообновления страниц, секунды

    //Названия для кастомизирования столбцов
    def('OPT_1','');
    def('OPT_2','');
    def('OPT_3','');
    def('OPT_4','');
    def('STR_1','');

    //Поддержка udpxy(relay) для плеера
    //def('UDPXY_HOST','iptv2.server.my');
    //def('UDPXY_HOSTONSERVER',true);
    //def('UDPXY_PORT','4050');


    //Опции ниже не проверялись!!!

//    Раскомментируйте и впишите сервер, если хотите получать уведомления.
//    def('XMPPLOGIN','admin@172.18.0.8'); // Имя пользователя
//    def('XMPPPASS','2207811'); // пароль
//    def('XMPPDOMAIN','jabber.ar');   // имя домена
//    def('XMPPHOST','172.18.0.8');   //  сервер для подключения
//    def('XMPPPORT','5222');   // порт
//    def('XMPPALERTJID','client@172.18.0.8'); // адрес назначения куда слать уведомления

////////////////////////////////////////////////////////////////////////////

    setlocale (LC_ALL,'ru_RU.UTF-8');

//-------System
    $path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']);
    define('SCRIPT_DIR',$path_parts["dirname"]);
    $path_parts = pathinfo($_SERVER["PHP_SELF"]);

    $path_parts["dirname"]=='/'?'':$path_parts["dirname"];
    define('SCRIPT_WEBDIR',$path_parts["dirname"]);
    unset ($path_parts);

    $data=Array();
    read_request($data);

    function read_request(&$data) {
        foreach ($_REQUEST as $key => $val)
            {
            $data[$key] = rtrim(stripslashes($val));
            }
        $data['remote_ip'] = $_SERVER["REMOTE_ADDR"];
    }

?>
