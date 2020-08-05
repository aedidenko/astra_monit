#!/bin/bash

SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. $SCRIPT_PATH/config.sh

id=$1
channel_id=$id
channel_name=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select channel_id as '' from input where channel_id=$id"`
DB_SNR=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select input.bitrate as '' from input where channel_id=$1;"`
let "SNR=$DB_SNR/1000"
DB_SIG=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select input.pes_error as '' from input where channel_id=$1;"`
let "SIG=$DB_SIG"
DB_BER=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select input.cc_error as '' from input where channel_id=$1;"`
let "BER=$DB_BER"

DB=`echo $db_path$channel_id.rrd | tr -d ' '`

if [ ! -f $DB ]
then
/usr/bin/rrdtool create $DB --step 60 \
DS:snr:GAUGE:120:0:100 \
DS:sig:GAUGE:120:0:100 \
DS:unc:GAUGE:120:0:U \
DS:ber:GAUGE:120:0:U \
RRA:AVERAGE:0.5:1:10080
fi

RES=`echo N:$SNR:$SIG:$UNC:$BER | tr -d ' '`
/usr/bin/rrdtool update $DB $RES

/usr/bin/rrdtool graph $graph_path/graph_hour_$id.png \
--width 600 \
--height 300 \
--start -1h \
--end now \
--upper-limit 16 \
--rigid \
DEF:gsnr=$DB:snr:AVERAGE \
DEF:gsig=$DB:sig:AVERAGE \
DEF:gunc=$DB:unc:AVERAGE \
DEF:gber=$DB:ber:AVERAGE \
CDEF:cber=gber,100,% \
CDEF:csig=gsig,100,% \
LINE2:gsig#0000FF:"PES-Error" \
LINE2:gber#FF0000:"CC-Error" \
LINE1:gsnr#009900:"Bitrate(Mbit/s)" \
LINE1:gunc#00FFFF:"" \
COMMENT:"PES-Error\:Ошибка в заголовке пакета с данными видео/аудио(Неправильный ключ дешифрования)" \
COMMENT:"CC-Error\:Нарушение последовательности пакетов в потоке(Теряются данные при получении потока)" \

/usr/bin/rrdtool graph $graph_path/graph_day_$id.png \
--width 600 \
--height 300 \
--start -1d \
--end now \
--upper-limit 16 \
--rigid \
DEF:gsnr=$DB:snr:AVERAGE \
DEF:gsig=$DB:sig:AVERAGE \
DEF:gunc=$DB:unc:AVERAGE \
DEF:gber=$DB:ber:AVERAGE \
CDEF:cber=gber,100,% \
CDEF:csig=gsig,100,% \
LINE2:gsig#0000FF:"PES-Error" \
LINE2:gber#FF0000:"CC-Error" \
LINE1:gsnr#009900:"Bitrate(Mbit/s)" \
LINE1:gunc#00FFFF:"" \
COMMENT:"PES-Error\:Ошибка в заголовке пакета с данными видео/аудио(Неправильный ключ дешифрования)" \
COMMENT:"CC-Error\:Нарушение последовательности пакетов в потоке(Теряются данные при получении потока)" \

/usr/bin/rrdtool graph $graph_path/graph_week_$id.png \
--width 600 \
--height 300 \
--start -7d \
--end now \
--upper-limit 16 \
--rigid \
DEF:gsnr=$DB:snr:AVERAGE \
DEF:gsig=$DB:sig:AVERAGE \
DEF:gunc=$DB:unc:AVERAGE \
DEF:gber=$DB:ber:AVERAGE \
CDEF:cber=gber,100,% \
CDEF:csig=gsig,100,% \
LINE2:gsig#0000FF:"PES-Error" \
LINE2:gber#FF0000:"CC-Error" \
LINE1:gsnr#009900:"Bitrate(Mbit/s)" \
LINE1:gunc#00FFFF:"" \
COMMENT:"PES-Error\:Ошибка в заголовке пакета с данными видео/аудио(Неправильный ключ дешифрования)" \
COMMENT:"CC-Error\:Нарушение последовательности пакетов в потоке(Теряются данные при получении потока)" \
