#!/bin/bash

SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. $SCRIPT_PATH/config.sh

id=$1
adapter_id=$id
adapter_name=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select name as '' from dvb_input where dvb_input_id=$id"`
DB_SNR=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select snr as '' from dvb_input where dvb_input_id=$1;"`
let "SNR=$DB_SNR*100/65535"
DB_SIG=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select dvb_input.signal as '' from dvb_input where dvb_input_id=$1;"`
let "SIG=$DB_SIG*100/65535"
UNC=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select unc as '' from dvb_input where dvb_input_id=$1;"`
BER=`mysql -u$my_user -p$my_pass -h$my_host $my_db -e "select ber as '' from dvb_input where dvb_input_id=$1;"`

DB=`echo $db_path$adapter_id.rrd | tr -d ' '`

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

#log=`echo /usr/local/bin/rrd/$adapter_id.log | tr -d ' '`
#/usr/bin/rrdtool fetch $DB AVERAGE --start -1h | tee $log

/usr/bin/rrdtool graph $graph_path/graph_hour_$id.png \
--width 600 \
--height 300 \
--start -1h \
--end now \
--upper-limit 101 \
--rigid \
DEF:gsnr=$DB:snr:AVERAGE \
DEF:gsig=$DB:sig:AVERAGE \
DEF:gunc=$DB:unc:AVERAGE \
DEF:gber=$DB:ber:AVERAGE \
CDEF:cber=gber,100,% \
CDEF:cunc=gunc,100,% \
AREA:cunc#0000FF:"Invalid Blocks" \
AREA:cber#009900:"Bit Errors(x100)" \
LINE1:gsnr#FF0000:"SNR" \
LINE1:gsig#00FFFF:"Signal" \

/usr/bin/rrdtool graph $graph_path/graph_day_$id.png \
--width 600 \
--height 300 \
--start -1d \
--end now \
--upper-limit 101 \
--rigid \
DEF:gsnr=$DB:snr:AVERAGE \
DEF:gsig=$DB:sig:AVERAGE \
DEF:gunc=$DB:unc:AVERAGE \
DEF:gber=$DB:ber:AVERAGE \
CDEF:cber=gber,100,% \
CDEF:cunc=gunc,100,% \
AREA:cunc#0000FF:"Invalid Blocks" \
AREA:cber#009900:"Bit Errors(x100)" \
LINE1:gsnr#FF0000:"SNR" \
LINE1:gsig#00FFFF:"Signal" \

/usr/bin/rrdtool graph $graph_path/graph_week_$id.png \
--width 600 \
--height 300 \
--start -7d \
--end now \
--upper-limit 101 \
--rigid \
DEF:gsnr=$DB:snr:AVERAGE \
DEF:gsig=$DB:sig:AVERAGE \
DEF:gunc=$DB:unc:AVERAGE \
DEF:gber=$DB:ber:AVERAGE \
CDEF:cber=gber,100,% \
CDEF:cunc=gunc,100,% \
AREA:cunc#0000FF:"Invalid Blocks" \
AREA:cber#009900:"Bit Errors(x100)" \
LINE1:gsnr#FF0000:"SNR" \
LINE1:gsig#00FFFF:"Signal" \
