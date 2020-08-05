#!/usr/bin/python
# -*- coding: utf-8 -*-

import config
import telepot
import sys

bot = telepot.Bot(config.TOKEN)
for number in config.USER_ID:
    bot.sendMessage(number, ' '.join(sys.argv[1:]), parse_mode = 'HTML')
