import json, re, requests, os
from pybaseball import *
from datetime import timedelta, datetime
import pandas as pd
from sqlalchemy import create_engine, text
from sqlalchemy.exc import IntegrityError


d = datetime.today() - timedelta(days=16)

# Create engine
engine = create_engine('mysql+pymysql://c0_baseball:3!cZ6QfrREkoT@192.168.1.80:3306/c0baseball')

# Get API key from config file
config_path = os.path.join(os.path.dirname(__file__), 'config.json')
with open(config_path, 'r') as config_file:
    config = json.load(config_file)
key = config['API_KEY']

def getInjuries():
    url = f"https://api.sportradar.com/mlb/trial/v7/en/league/injuries.json?api_key={key}"
    r = requests.get(url)
    result = json.loads(r.content)['teams']
    return result

try:
    teams = getInjuries()
    injuries = []  # List to collect all injuries

    with engine.connect() as conn:
        for t in teams:
            for p in t['players']:
                player_id = p['id']
                status = p['status']

                for i in p['injuries']:
                    injuries.append({
                        "injury_srid": i['id'],
                        "player_srid": player_id,
                        "comment": i['comment'],
                        "description": i['desc'],
                        "status": status,
                        "start_date": i['start_date'],
                        "update_date": i.get('update_date', datetime.today().strftime('%Y-%m-%d'))
                    })

                # Use parameterized query for updating player status
                update_sql = text("UPDATE players SET status = :status WHERE sportsradar_id = :player_id")
                update_params = {'status': status, 'player_id': player_id}
                conn.execute(update_sql, update_params)

        # Convert injuries list to DataFrame and write to SQL outside the loop
        if injuries:
            injury_df = pd.DataFrame(injuries)
            injury_df.to_sql('injuries', con=engine, if_exists='append', index=False)

except IntegrityError as e:
    print(f"Integrity error occurred: {e}")
except Exception as e:
    print(f"An error occurred: {e}")

