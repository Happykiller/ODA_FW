import logging
import sys
import os

# Setup logging
logger = logging.getLogger(sys.argv[0])
logger.setLevel(logging.DEBUG)
# Use file output for production logging:
logfilename = sys.argv[0]+".log"
filelog = logging.FileHandler(logfilename, 'a',encoding = "UTF-8")
filelog.setLevel(logging.DEBUG)
# Use console for development logging:
conlog = logging.StreamHandler()
conlog.setLevel(logging.DEBUG)
# Specify log formatting:
formatter = logging.Formatter("%(asctime)s:%(levelname)s:%(message)s")
conlog.setFormatter(formatter)
filelog.setFormatter(formatter)
# Add console log to logger
logger.addHandler(conlog)
logger.addHandler(filelog)
