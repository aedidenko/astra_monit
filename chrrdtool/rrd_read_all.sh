#!/bin/bash

SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. $SCRIPT_PATH/config.sh
result=`mysql -u$my_user -h$my_host -p$my_pass $my_db -e "select channel_id as '' from input;"`
for i in ${result[@]}
    do `$SCRIPT_PATH/rrd_update.sh $i > /dev/null`
done
