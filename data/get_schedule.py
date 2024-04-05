import json, re, requests
from pybaseball import *
from datetime import timedelta, datetime
import pandas as pd
from sqlalchemy import create_engine, text
from sqlalchemy.exc import IntegrityError
import pymysql

# Date
d = datetime.today()
day = d.strftime('%d')
month = d.strftime('%m')
year = d.year

# API
key = 'dce2xmaf6x9z7cp25tz7msse'
url = f"http://api.sportradar.us/mlb/trial/v7/en/games/{year}/{month}/{day}/schedule.json?api_key={key}"

# SQL engine
engine = create_engine('mysql+pymysql://c0_baseball:3!cZ6QfrREkoT@192.168.1.80:3306/c0baseball')

# Get schedule
#   From file
#f = open('/Users/lkstanford/fantasybaseball/initialization/daily_schedule.json')
#sched = json.load(f)
#f.close()

#   From API
r = requests.get(url)
s_json = json.loads(r.content)['games']

# Loop
for g in s_json:
    g_dict = {
        "srid": g['id'],
        "status": g['status'],
        "home_team": g['home_team'],
        "away_team": g['away_team'],
        "venue": g['venue']['id'],
        "date": (datetime.strptime(g['scheduled'], "%Y-%m-%dT%H:%M:%S%z") - timedelta(hours=5)).strftime("%Y-%m-%d %H:%M:%S")
    }

    g_df = pd.DataFrame(g_dict, index=[0])
    try:
        g_df.to_sql('mlb_schedule', con=engine, if_exists='append', index=False)
    except IntegrityError as ie:
        print(f"Duplicate record for {g_dict['srid']}")
    except Exception as e:
        print("SOME OTHER ERROR OCCURED")
    else:
        print(f"Inserted {g_dict['srid']}")