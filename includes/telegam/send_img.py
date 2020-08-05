#!/usr/bin/python
# -*- coding: utf-8 -*-

import config
import telepot
import sys
import urllib2

f = urllib2.urlopen(config.URL_TO_RRD_GRAPH + 'graph_day_' + sys.argv[1] + '.png')
bot = telepot.Bot(config.TOKEN)
for number in config.USER_ID:
    bot.sendPhoto(number, ('1_day.png', f), ' '.join(sys.argv[2:]))
