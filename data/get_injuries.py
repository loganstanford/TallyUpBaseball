import json, re, requests
from pybaseball import *
from datetime import timedelta, datetime
import pandas as pd
from sqlalchemy import create_engine, text
from sqlalchemy.exc import IntegrityError
import pymysql

d = datetime.today() - timedelta(days=1)

# Create engine
engine = create_engine('mysql+pymysql://c0_baseball:3!cZ6QfrREkoT@192.168.1.80:3306/c0baseball')

# SportsRadar functions
key = 'dce2xmaf6x9z7cp25tz7msse'

def getInjuries():
    url = f"https://api.sportradar.com/mlb/trial/v7/en/league/injuries.json?api_key={key}"
    r = requests.get(url)
    result = json.loads(r.content)['teams']
    return result

teams = getInjuries()
for t in teams:
    for p in t['players']:
        player_id = p['id']
        status = p['status']
        for i in p['injuries']:
            inj_id = i['id']
            comment = i['comment']
            desc = i['desc']
            inj_status = i['status']
            start_date = i['start_date']
            update_date = i['update_date']
            injury_dict = {
                "injury_srid": inj_id,
                "player_srid": player_id,
                "comment": comment,
                "description": desc,
                "status": status,
                "start_date": start_date,
                "update_date": update_date
            }
            injury_df = pd.DataFrame(injury_dict, index=[0])
            injury_df.to_sql('injuries', con=engine, if_exists='append', index=False)
        
        sql = f"UPDATE players SET status = '{status}' WHERE sportsradar_id = '{player_id}'"
        with engine.connect() as conn:
            try:
                conn.execute(text(sql))
                conn.commit()
            except IntegrityError:
                print("Injury already in table")

