import json, re, requests, os
from pybaseball import *
from datetime import timedelta, datetime
import pandas as pd
from sqlalchemy import create_engine, text
from sqlalchemy.exc import IntegrityError
import pymysql
from config import DB_CONNECTION_STRING, API_KEY

# Date
d = datetime.today()
day = d.strftime('%d')
month = d.strftime('%m')
year = d.year

# API
url = f"http://api.sportradar.us/mlb/trial/v7/en/games/{year}/{month}/{day}/schedule.json?api_key={API_KEY}"

# SQL engine
engine = create_engine(DB_CONNECTION_STRING)

# Get schedule
#   From file
#f = open('/Users/lkstanford/fantasybaseball/initialization/daily_schedule.json')
#sched = json.load(f)
#f.close()

#   From API
r = requests.get(url)
s_json = json.loads(r.content)['games']

# Loop
# Initialize a list to hold game data
games_data = []

for g in s_json:
    try:
        game_date = (datetime.strptime(g['scheduled'], "%Y-%m-%dT%H:%M:%S%z") - timedelta(hours=5)).strftime("%Y-%m-%d %H:%M:%S")
        g_dict = {
            "srid": g['id'],
            "status": g['status'],
            "home_team": g['home_team'],
            "away_team": g['away_team'],
            "venue": g['venue']['id'],
            "date": game_date
        }
        games_data.append(g_dict)
    except Exception as e:
        print(f"Error processing game data: {e}")

# Bulk insert after the loop
try:
    if games_data:
        g_df = pd.DataFrame(games_data)
        g_df.to_sql('mlb_schedule', con=engine, if_exists='append', index=False)
        print("All game data inserted successfully.")
except IntegrityError as ie:
    print(f"IntegrityError occurred: {ie}")
except Exception as e:
    print(f"Error inserting into database: {e}")
